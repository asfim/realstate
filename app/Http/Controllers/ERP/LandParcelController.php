<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\CostCenter;
use App\Models\ERP\LandParcel;
use App\Models\ERP\LandParcelOwner;
use App\Models\ERP\LandVetting;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;

class LandParcelController extends Controller
{
    /**
     * Standard 8 Bangladesh Real Estate Land Vetting Stages.
     */
    protected array $defaultVettingStages = [
        'Title Deed Verification (Original Registry & CS/SA/RS)',
        'Mutation Khatian & Up-to-Date Dakhila (Land Revenue / Khajna)',
        'Bia Deed Chain Verification (Last 25 Years Chain of Ownership)',
        'CS, SA, RS & City Survey (BS) Porcha Cross-Verification',
        'Non-Encumbrance Certificate (NEC) from Sub-Registry Office',
        'Zoning & Master Plan Clearance (RAJUK / CDA / RDA / Local Authority)',
        'Soil Test & Geological Load Bearing Capacity Report',
        'Environmental & Drainage Clearance (DoE / Local Pourashava)',
    ];

    public function __construct(
        protected CompanyBranchContext $context,
        protected DocumentSequenceService $sequenceService,
        protected AuditLogService $auditLog
    ) {
    }

    public function index(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = LandParcel::where('company_id', $currentCompany?->id)
            ->with(['branch', 'owners', 'vettings', 'feasibilities'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('acquisition_type')) {
            $query->where('acquisition_type', $request->acquisition_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('parcel_code', 'like', "%{$search}%")
                    ->orWhere('mouza', 'like', "%{$search}%")
                    ->orWhere('dag_no', 'like', "%{$search}%");
            });
        }

        $parcels = $query->paginate(15)->withQueryString();

        $statusCounts = LandParcel::where('company_id', $currentCompany?->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('erp.land.parcels.index', compact('parcels', 'statusCounts'));
    }

    public function create()
    {
        $currentCompany = $this->context->currentCompany();
        $branches = $this->context->accessibleBranches();
        $costCenters = CostCenter::where('company_id', $currentCompany?->id)->where('status', 'active')->get();

        return view('erp.land.parcels.create', compact('branches', 'costCenters'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'title'                   => 'required|string|max:255',
            'mouza'                   => 'nullable|string|max:255',
            'khatian_no'              => 'nullable|string|max:100',
            'dag_no'                  => 'nullable|string|max:100',
            'total_area_katha'        => 'required|numeric|min:0.01',
            'total_area_decimal'      => 'nullable|numeric|min:0',
            'acquisition_type'        => 'required|string',
            'status'                  => 'required|string',
            'landowner_ratio'         => 'nullable|numeric|min:0|max:100',
            'developer_ratio'         => 'nullable|numeric|min:0|max:100',
            'estimated_land_value'    => 'nullable|numeric|min:0',
            'signing_money_committed' => 'nullable|numeric|min:0',
            'signing_money_paid'      => 'nullable|numeric|min:0',
            'primary_contact_name'    => 'nullable|string|max:255',
            'primary_contact_phone'   => 'nullable|string|max:50',
            'target_handover_date'    => 'nullable|date',
            'boundary_north'          => 'nullable|string|max:255',
            'boundary_south'          => 'nullable|string|max:255',
            'boundary_east'           => 'nullable|string|max:255',
            'boundary_west'           => 'nullable|string|max:255',
            'branch_id'               => 'nullable|exists:branches,id',
            'cost_center_id'          => 'nullable|exists:cost_centers,id',
            'notes'                   => 'nullable|string',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $validated['branch_id'] = $validated['branch_id'] ?? $currentBranch?->id;
        $validated['created_by'] = auth('admin')->id();
        $validated['parcel_code'] = $this->sequenceService->getNextNumber('land_parcel', $currentCompany->id, $validated['branch_id']);

        if (empty($validated['total_area_decimal']) && $validated['total_area_katha']) {
            $validated['total_area_decimal'] = $validated['total_area_katha'] * 1.65; // Approx standard conversion
        }

        $parcel = LandParcel::create($validated);

        // Auto-seed default 8 standard legal vetting checklist stages
        foreach ($this->defaultVettingStages as $stageName) {
            LandVetting::create([
                'land_parcel_id' => $parcel->id,
                'stage_title'    => $stageName,
                'status'         => 'pending',
            ]);
        }

        // If primary contact was supplied, add as first owner
        if (!empty($validated['primary_contact_name'])) {
            LandParcelOwner::create([
                'land_parcel_id'   => $parcel->id,
                'name'             => $validated['primary_contact_name'],
                'share_percentage' => 100.00,
                'phone'            => $validated['primary_contact_phone'],
            ]);
        }

        $this->auditLog->log('land.parcel_created', $parcel, null, $parcel->toArray(), $currentCompany->id, $parcel->branch_id);

        return redirect()->route('erp.land-parcels.show', $parcel)->with('success', "Land parcel {$parcel->parcel_code} registered with legal vetting checklist.");
    }

    public function show(LandParcel $landParcel)
    {
        $landParcel->load(['branch', 'costCenter', 'owners', 'vettings.verifiedBy', 'feasibilities.createdBy', 'feasibilities.approvedBy']);
        return view('erp.land.parcels.show', compact('landParcel'));
    }

    public function edit(LandParcel $landParcel)
    {
        $currentCompany = $this->context->currentCompany();
        $branches = $this->context->accessibleBranches();
        $costCenters = CostCenter::where('company_id', $currentCompany?->id)->where('status', 'active')->get();

        return view('erp.land.parcels.edit', compact('landParcel', 'branches', 'costCenters'));
    }

    public function update(Request $request, LandParcel $landParcel)
    {
        $validated = $request->validate([
            'title'                   => 'required|string|max:255',
            'mouza'                   => 'nullable|string|max:255',
            'khatian_no'              => 'nullable|string|max:100',
            'dag_no'                  => 'nullable|string|max:100',
            'total_area_katha'        => 'required|numeric|min:0.01',
            'total_area_decimal'      => 'nullable|numeric|min:0',
            'acquisition_type'        => 'required|string',
            'status'                  => 'required|string',
            'landowner_ratio'         => 'nullable|numeric|min:0|max:100',
            'developer_ratio'         => 'nullable|numeric|min:0|max:100',
            'estimated_land_value'    => 'nullable|numeric|min:0',
            'signing_money_committed' => 'nullable|numeric|min:0',
            'signing_money_paid'      => 'nullable|numeric|min:0',
            'primary_contact_name'    => 'nullable|string|max:255',
            'primary_contact_phone'   => 'nullable|string|max:50',
            'target_handover_date'    => 'nullable|date',
            'boundary_north'          => 'nullable|string|max:255',
            'boundary_south'          => 'nullable|string|max:255',
            'boundary_east'           => 'nullable|string|max:255',
            'boundary_west'           => 'nullable|string|max:255',
            'branch_id'               => 'nullable|exists:branches,id',
            'cost_center_id'          => 'nullable|exists:cost_centers,id',
            'notes'                   => 'nullable|string',
        ]);

        $before = $landParcel->toArray();
        $landParcel->update($validated);
        $this->auditLog->log('land.parcel_updated', $landParcel, $before, $landParcel->toArray(), $landParcel->company_id, $landParcel->branch_id);

        return redirect()->route('erp.land-parcels.show', $landParcel)->with('success', 'Land parcel updated successfully.');
    }

    public function destroy(LandParcel $landParcel)
    {
        $before = $landParcel->toArray();
        $landParcel->delete();
        $this->auditLog->log('land.parcel_deleted', null, $before, null, $landParcel->company_id, $landParcel->branch_id);

        return redirect()->route('erp.land-parcels.index')->with('success', 'Land parcel archived.');
    }

    public function addOwner(Request $request, LandParcel $landParcel)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'share_percentage'    => 'required|numeric|min:0.01|max:100',
            'phone'               => 'nullable|string|max:50',
            'nid_number'          => 'nullable|string|max:50',
            'signing_money_share' => 'nullable|numeric|min:0',
            'is_mou_signed'       => 'nullable|boolean',
            'mou_deed_no'         => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
        ]);

        $validated['land_parcel_id'] = $landParcel->id;
        $validated['is_mou_signed'] = $request->boolean('is_mou_signed');

        LandParcelOwner::create($validated);

        return back()->with('success', 'Landowner added to Joint Venture share register.');
    }

    public function updateVetting(Request $request, LandVetting $vetting)
    {
        $validated = $request->validate([
            'status'                => 'required|in:pending,in_progress,verified_clear,disputed_red_flag,waived',
            'lawyer_or_expert_name' => 'nullable|string|max:255',
            'findings'              => 'nullable|string',
        ]);

        $validated['verified_by_admin_id'] = auth('admin')->id();
        $validated['verified_at'] = now();

        $vetting->update($validated);

        return back()->with('success', 'Legal vetting stage updated.');
    }
}
