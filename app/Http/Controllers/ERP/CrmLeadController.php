<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ERP\CrmLead;
use App\Models\ERP\CrmLeadActivity;
use App\Models\FeaturedProject;
use App\Models\Property;
use App\Models\Submission;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use App\Services\ERP\LeadRoutingService;
use Illuminate\Http\Request;

class CrmLeadController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected DocumentSequenceService $sequenceService,
        protected LeadRoutingService $routingService,
        protected AuditLogService $auditLog
    ) {
    }

    public function index(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = CrmLead::where('company_id', $currentCompany?->id)
            ->with(['assignedAgent', 'featuredProject', 'property', 'branch'])
            ->latest();

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('agent_id')) {
            $query->where('assigned_agent_id', $request->agent_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('lead_code', 'like', "%{$search}%");
            });
        }

        $leads = $query->paginate(20)->withQueryString();

        $stageCounts = CrmLead::where('company_id', $currentCompany?->id)
            ->selectRaw('stage, count(*) as count')
            ->groupBy('stage')
            ->pluck('count', 'stage')
            ->toArray();

        $agents = Admin::all();
        $unimportedSubmissionsCount = Submission::whereDoesntHave('lead')->count();

        return view('erp.crm.leads.index', compact('leads', 'stageCounts', 'agents', 'unimportedSubmissionsCount'));
    }

    public function create()
    {
        $currentCompany = $this->context->currentCompany();
        $branches = $this->context->accessibleBranches();
        $agents = Admin::all();
        $projects = FeaturedProject::where('is_active', true)->get();
        $properties = Property::where('is_active', true)->get();

        return view('erp.crm.leads.create', compact('currentCompany', 'branches', 'agents', 'projects', 'properties'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'phone'                  => 'required|string|max:50',
            'email'                  => 'nullable|email|max:255',
            'secondary_phone'        => 'nullable|string|max:50',
            'source'                 => 'required|string',
            'stage'                  => 'required|string',
            'priority'               => 'required|string',
            'branch_id'              => 'nullable|exists:branches,id',
            'assigned_agent_id'      => 'nullable|exists:admins,id',
            'property_type_interest' => 'nullable|string',
            'featured_project_id'    => 'nullable|exists:featured_projects,id',
            'property_id'            => 'nullable|exists:properties,id',
            'min_budget'             => 'nullable|numeric|min:0',
            'max_budget'             => 'nullable|numeric|min:0',
            'preferred_location'     => 'nullable|string|max:255',
            'preferred_size_sqft'    => 'nullable|string|max:100',
            'next_follow_up_at'      => 'nullable|date',
            'notes'                  => 'nullable|string',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $validated['branch_id'] = $validated['branch_id'] ?? $currentBranch?->id;
        $validated['created_by'] = auth('admin')->id();
        $validated['lead_code'] = $this->sequenceService->getNextNumber('lead', $currentCompany->id, $validated['branch_id']);

        if (empty($validated['assigned_agent_id'])) {
            $agent = $this->routingService->getNextAvailableAgent($currentCompany->id, $validated['branch_id']);
            $validated['assigned_agent_id'] = $agent?->id;
        }

        $lead = CrmLead::create($validated);
        $lead->score = $lead->calculateScore();
        $lead->save();

        CrmLeadActivity::create([
            'lead_id'       => $lead->id,
            'admin_id'      => auth('admin')->id(),
            'type'          => 'status_change',
            'subject'       => 'Lead Created',
            'description'   => "Lead {$lead->lead_code} registered via {$lead->source}.",
            'activity_date' => now(),
            'outcome'       => 'connected',
        ]);

        $this->auditLog->log('crm.lead_created', $lead, null, $lead->toArray(), $currentCompany->id, $lead->branch_id);

        return redirect()->route('erp.leads.show', $lead)->with('success', 'Lead created successfully.');
    }

    public function show(CrmLead $lead)
    {
        $lead->load(['assignedAgent', 'branch', 'featuredProject', 'property', 'activities.admin', 'siteVisits.assignedAgent', 'customer']);
        $agents = Admin::all();
        $projects = FeaturedProject::where('is_active', true)->get();
        $properties = Property::where('is_active', true)->get();

        return view('erp.crm.leads.show', compact('lead', 'agents', 'projects', 'properties'));
    }

    public function edit(CrmLead $lead)
    {
        $branches = $this->context->accessibleBranches();
        $agents = Admin::all();
        $projects = FeaturedProject::where('is_active', true)->get();
        $properties = Property::where('is_active', true)->get();

        return view('erp.crm.leads.edit', compact('lead', 'branches', 'agents', 'projects', 'properties'));
    }

    public function update(Request $request, CrmLead $lead)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'phone'                  => 'required|string|max:50',
            'email'                  => 'nullable|email|max:255',
            'secondary_phone'        => 'nullable|string|max:50',
            'source'                 => 'required|string',
            'stage'                  => 'required|string',
            'priority'               => 'required|string',
            'branch_id'              => 'nullable|exists:branches,id',
            'assigned_agent_id'      => 'nullable|exists:admins,id',
            'property_type_interest' => 'nullable|string',
            'featured_project_id'    => 'nullable|exists:featured_projects,id',
            'property_id'            => 'nullable|exists:properties,id',
            'min_budget'             => 'nullable|numeric|min:0',
            'max_budget'             => 'nullable|numeric|min:0',
            'preferred_location'     => 'nullable|string|max:255',
            'preferred_size_sqft'    => 'nullable|string|max:100',
            'lost_reason'            => 'nullable|string',
            'next_follow_up_at'      => 'nullable|date',
            'notes'                  => 'nullable|string',
        ]);

        $before = $lead->toArray();
        $oldStage = $lead->stage;

        $lead->update($validated);
        $lead->score = $lead->calculateScore();
        $lead->save();

        if ($oldStage !== $lead->stage) {
            CrmLeadActivity::create([
                'lead_id'       => $lead->id,
                'admin_id'      => auth('admin')->id(),
                'type'          => 'status_change',
                'subject'       => "Stage Updated to " . ucfirst(str_replace('_', ' ', $lead->stage)),
                'description'   => "Pipeline stage moved from {$oldStage} to {$lead->stage}.",
                'activity_date' => now(),
                'outcome'       => 'completed',
            ]);
        }

        $this->auditLog->log('crm.lead_updated', $lead, $before, $lead->toArray(), $lead->company_id, $lead->branch_id);

        return redirect()->route('erp.leads.show', $lead)->with('success', 'Lead profile updated.');
    }

    public function updateStage(Request $request, CrmLead $lead)
    {
        $request->validate([
            'stage'       => 'required|string',
            'lost_reason' => 'nullable|string',
        ]);

        $oldStage = $lead->stage;
        $lead->stage = $request->stage;
        if ($request->filled('lost_reason')) {
            $lead->lost_reason = $request->lost_reason;
        }
        $lead->score = $lead->calculateScore();
        $lead->save();

        CrmLeadActivity::create([
            'lead_id'       => $lead->id,
            'admin_id'      => auth('admin')->id(),
            'type'          => 'status_change',
            'subject'       => "Stage changed to " . ucfirst(str_replace('_', ' ', $lead->stage)),
            'description'   => $request->lost_reason ?? "Pipeline progression from {$oldStage} to {$lead->stage}.",
            'activity_date' => now(),
            'outcome'       => 'completed',
        ]);

        return back()->with('success', 'Lead stage successfully updated.');
    }

    public function destroy(CrmLead $lead)
    {
        $before = $lead->toArray();
        $lead->delete();
        $this->auditLog->log('crm.lead_deleted', null, $before, null, $lead->company_id, $lead->branch_id);

        return redirect()->route('erp.leads.index')->with('success', 'Lead record archived.');
    }

    public function importSubmissions()
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $unimported = Submission::whereDoesntHave('lead')->get();
        $count = 0;

        foreach ($unimported as $submission) {
            $this->routingService->ingestFromSubmission($submission, $currentCompany->id, $currentBranch?->id);
            $count++;
        }

        return redirect()->route('erp.leads.index')->with('success', "{$count} website inquiries imported into CRM leads.");
    }
}

