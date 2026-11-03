<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\CostCenter;
use App\Models\ERP\DevProject;
use App\Models\ERP\LandParcel;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;

class DevProjectController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected DocumentSequenceService $sequenceService,
        protected AuditLogService $auditLog
    ) {
    }

    public function index(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = DevProject::where('company_id', $currentCompany?->id)
            ->with(['branch', 'landParcel', 'costCenter', 'units'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_type')) {
            $query->where('project_type', $request->project_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('project_code', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $projects = $query->paginate(15)->withQueryString();

        $statusCounts = DevProject::where('company_id', $currentCompany?->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('erp.developer.projects.index', compact('projects', 'statusCounts'));
    }

    public function create()
    {
        $currentCompany = $this->context->currentCompany();
        $branches = $this->context->accessibleBranches();
        $parcels = LandParcel::where('company_id', $currentCompany?->id)->get();
        $costCenters = CostCenter::where('company_id', $currentCompany?->id)->where('status', 'active')->get();

        return view('erp.developer.projects.create', compact('branches', 'parcels', 'costCenters'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'name'                     => 'required|string|max:255',
            'project_type'             => 'required|string',
            'location'                 => 'required|string|max:255',
            'address'                  => 'nullable|string',
            'land_parcel_id'           => 'nullable|exists:land_parcels,id',
            'cost_center_id'           => 'nullable|exists:cost_centers,id',
            'land_area_katha'          => 'nullable|numeric|min:0.01',
            'total_floors'             => 'required|integer|min:1|max:80',
            'total_basements'          => 'nullable|integer|min:0|max:5',
            'total_units'              => 'required|integer|min:1',
            'total_parking_slots'      => 'nullable|integer|min:0',
            'sanction_no'              => 'nullable|string|max:100',
            'architect_name'           => 'nullable|string|max:255',
            'structural_engineer'      => 'nullable|string|max:255',
            'start_date'               => 'nullable|date',
            'expected_completion_date' => 'nullable|date|after_or_equal:start_date',
            'status'                   => 'required|string',
            'branch_id'                => 'nullable|exists:branches,id',
            'notes'                    => 'nullable|string',
        ]);

        $validated['total_basements'] = $validated['total_basements'] ?? 0;
        $validated['company_id'] = $currentCompany->id;
        $validated['branch_id'] = $validated['branch_id'] ?? $currentBranch?->id;
        $validated['created_by'] = auth('admin')->id();
        $validated['project_code'] = $this->sequenceService->getNextNumber('project', $currentCompany->id, $validated['branch_id']);

        $project = DevProject::create($validated);
        $this->auditLog->log('dev.project_created', $project, null, $project->toArray(), $currentCompany->id, $project->branch_id);

        return redirect()->route('erp.dev-projects.show', $project)->with('success', "Project {$project->project_code} created successfully.");
    }

    public function show(DevProject $devProject)
    {
        $devProject->load(['branch', 'landParcel', 'costCenter', 'units.parkingSlots', 'parkingSlots', 'bookings.customer', 'bookings.unit']);
        return view('erp.developer.projects.show', compact('devProject'));
    }

    public function edit(DevProject $devProject)
    {
        $currentCompany = $this->context->currentCompany();
        $branches = $this->context->accessibleBranches();
        $parcels = LandParcel::where('company_id', $currentCompany?->id)->get();
        $costCenters = CostCenter::where('company_id', $currentCompany?->id)->where('status', 'active')->get();

        return view('erp.developer.projects.edit', compact('devProject', 'branches', 'parcels', 'costCenters'));
    }

    public function update(Request $request, DevProject $devProject)
    {
        $validated = $request->validate([
            'name'                     => 'required|string|max:255',
            'project_type'             => 'required|string',
            'location'                 => 'required|string|max:255',
            'address'                  => 'nullable|string',
            'land_parcel_id'           => 'nullable|exists:land_parcels,id',
            'cost_center_id'           => 'nullable|exists:cost_centers,id',
            'land_area_katha'          => 'nullable|numeric|min:0.01',
            'total_floors'             => 'required|integer|min:1|max:80',
            'total_basements'          => 'required|integer|min:0|max:5',
            'total_units'              => 'required|integer|min:1',
            'total_parking_slots'      => 'nullable|integer|min:0',
            'sanction_no'              => 'nullable|string|max:100',
            'architect_name'           => 'nullable|string|max:255',
            'structural_engineer'      => 'nullable|string|max:255',
            'start_date'               => 'nullable|date',
            'expected_completion_date' => 'nullable|date',
            'status'                   => 'required|string',
            'branch_id'                => 'nullable|exists:branches,id',
            'notes'                    => 'nullable|string',
        ]);

        $before = $devProject->toArray();
        $devProject->update($validated);
        $this->auditLog->log('dev.project_updated', $devProject, $before, $devProject->toArray(), $devProject->company_id, $devProject->branch_id);

        return redirect()->route('erp.dev-projects.show', $devProject)->with('success', 'Project details updated successfully.');
    }

    public function destroy(DevProject $devProject)
    {
        if ($devProject->bookings()->where('status', '!=', 'cancelled')->count() > 0) {
            return back()->with('error', 'Cannot archive a project with active sales bookings.');
        }

        $before = $devProject->toArray();
        $devProject->delete();
        $this->auditLog->log('dev.project_deleted', null, $before, null, $devProject->company_id, $devProject->branch_id);

        return redirect()->route('erp.dev-projects.index')->with('success', 'Project archived.');
    }
}
