<?php

namespace App\Services\ERP;

use App\Models\Admin;
use App\Models\ERP\CrmCustomer;
use App\Models\ERP\CrmLead;
use App\Models\ERP\CrmLeadActivity;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class LeadRoutingService
{
    public function __construct(
        protected DocumentSequenceService $sequenceService,
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    /**
     * Auto-assign next sales agent using round-robin distribution.
     */
    public function getNextAvailableAgent(int $companyId, ?int $branchId = null): ?Admin
    {
        // Find admins with sales permissions or role, or any active admin
        $admins = Admin::all();

        if ($admins->isEmpty()) {
            return null;
        }

        // Round-robin: pick admin with the lowest count of open leads (stages: new, contacted, qualified, site_visit_scheduled)
        return Admin::withCount(['leads' => function ($query) use ($companyId) {
            $query->where('company_id', $companyId)
                ->whereIn('stage', ['new', 'contacted', 'qualified', 'site_visit_scheduled', 'site_visit_completed', 'negotiation']);
        }])
        ->orderBy('leads_count', 'asc')
        ->first();
    }

    /**
     * Ingest a website submission as an ERP Lead.
     */
    public function ingestFromSubmission(Submission $submission, int $companyId, ?int $branchId = null): CrmLead
    {
        return DB::transaction(function () use ($submission, $companyId, $branchId) {
            // Check if already ingested
            $existing = CrmLead::withTrashed()->where('submission_id', $submission->id)->first();
            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                return $existing;
            }

            $leadCode = $this->sequenceService->getNextNumber('lead', $companyId, $branchId);
            $assignedAgent = $this->getNextAvailableAgent($companyId, $branchId);

            $lead = CrmLead::create([
                'company_id'          => $companyId,
                'branch_id'           => $branchId,
                'assigned_agent_id'   => $assignedAgent?->id,
                'submission_id'       => $submission->id,
                'lead_code'           => $leadCode,
                'name'                => $submission->name,
                'phone'               => $submission->phone,
                'email'               => $submission->email,
                'source'              => 'web_form',
                'stage'               => 'new',
                'priority'            => 'high',
                'featured_project_id' => $submission->featured_project_id,
                'property_id'         => $submission->property_id,
                'notes'               => $submission->message,
            ]);

            $lead->score = $lead->calculateScore();
            $lead->save();

            // Create initial activity entry
            CrmLeadActivity::create([
                'lead_id'       => $lead->id,
                'admin_id'      => $assignedAgent?->id,
                'type'          => 'status_change',
                'subject'       => 'Web Inquiry Captured',
                'description'   => 'Automated lead ingestion from website submission #' . $submission->id,
                'activity_date' => now(),
                'outcome'       => 'connected',
            ]);

            $this->auditLog->log('crm.lead_ingested', $lead, null, $lead->toArray(), $companyId, $branchId);

            return $lead;
        });
    }

    /**
     * Convert qualified / booked Lead to Customer Master Record.
     */
    public function convertLeadToCustomer(CrmLead $lead, array $customerData): CrmCustomer
    {
        return DB::transaction(function () use ($lead, $customerData) {
            $customerCode = $this->sequenceService->getNextNumber('customer', $lead->company_id, $lead->branch_id);

            $customerData['company_id'] = $lead->company_id;
            $customerData['customer_code'] = $customerCode;
            $customerData['converted_from_lead_id'] = $lead->id;
            $customerData['name'] = $customerData['name'] ?? $lead->name;
            $customerData['phone'] = $customerData['phone'] ?? $lead->phone;
            $customerData['email'] = $customerData['email'] ?? $lead->email;
            $customerData['status'] = $customerData['status'] ?? 'active';

            $customer = CrmCustomer::create($customerData);

            // Update lead stage
            $lead->update([
                'stage' => 'booked',
                'score' => 100,
            ]);

            CrmLeadActivity::create([
                'lead_id'       => $lead->id,
                'admin_id'      => auth('admin')->id(),
                'type'          => 'status_change',
                'subject'       => 'Converted to Customer Profile',
                'description'   => "Lead converted to Customer Account {$customer->customer_code}.",
                'activity_date' => now(),
                'outcome'       => 'completed',
            ]);

            $this->auditLog->log('crm.lead_converted_to_customer', $customer, null, $customer->toArray(), $lead->company_id, $lead->branch_id);

            return $customer;
        });
    }
}

