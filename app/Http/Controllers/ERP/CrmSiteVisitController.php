<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ERP\CrmLead;
use App\Models\ERP\CrmSiteVisit;
use App\Models\FeaturedProject;
use App\Models\Property;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class CrmSiteVisitController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    public function index(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = CrmSiteVisit::where('company_id', $currentCompany?->id)
            ->with(['lead', 'assignedAgent', 'featuredProject', 'property', 'branch'])
            ->latest('scheduled_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $siteVisits = $query->paginate(20)->withQueryString();
        $agents = Admin::all();
        $projects = FeaturedProject::where('is_active', true)->get();

        return view('erp.crm.site_visits.index', compact('siteVisits', 'agents', 'projects'));
    }

    public function store(Request $request, CrmLead $lead)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'scheduled_at'        => 'required|date|after:now',
            'featured_project_id' => 'nullable|exists:featured_projects,id',
            'property_id'         => 'nullable|exists:properties,id',
            'assigned_agent_id'   => 'nullable|exists:admins,id',
            'agent_remarks'       => 'nullable|string',
        ]);

        $validated['lead_id'] = $lead->id;
        $validated['company_id'] = $currentCompany->id;
        $validated['branch_id'] = $lead->branch_id ?? $currentBranch?->id;
        $validated['assigned_agent_id'] = $validated['assigned_agent_id'] ?? $lead->assigned_agent_id ?? auth('admin')->id();
        $validated['status'] = 'scheduled';

        $visit = CrmSiteVisit::create($validated);

        // Update lead stage
        if ($lead->stage !== 'site_visit_completed') {
            $lead->update([
                'stage'             => 'site_visit_scheduled',
                'next_follow_up_at' => $visit->scheduled_at,
            ]);
        }

        $lead->score = $lead->calculateScore();
        $lead->save();

        $this->auditLog->log('crm.site_visit_scheduled', $visit, null, $visit->toArray(), $currentCompany->id, $visit->branch_id);

        return back()->with('success', 'Site visit scheduled successfully.');
    }

    public function update(Request $request, CrmSiteVisit $siteVisit)
    {
        $validated = $request->validate([
            'status'          => 'required|in:scheduled,completed,cancelled,no_show',
            'completed_at'    => 'nullable|date',
            'client_feedback' => 'nullable|string',
            'client_rating'   => 'nullable|integer|min:1|max:5',
            'agent_remarks'   => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed' && empty($validated['completed_at'])) {
            $validated['completed_at'] = now();
        }

        $before = $siteVisit->toArray();
        $siteVisit->update($validated);

        if ($siteVisit->status === 'completed') {
            $siteVisit->lead->update(['stage' => 'site_visit_completed']);
            $siteVisit->lead->score = $siteVisit->lead->calculateScore();
            $siteVisit->lead->save();
        }

        $this->auditLog->log('crm.site_visit_updated', $siteVisit, $before, $siteVisit->toArray(), $siteVisit->company_id, $siteVisit->branch_id);

        return back()->with('success', 'Site visit outcome recorded.');
    }
}
