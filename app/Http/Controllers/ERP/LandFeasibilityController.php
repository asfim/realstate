<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\LandFeasibility;
use App\Models\ERP\LandParcel;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;

class LandFeasibilityController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected DocumentSequenceService $sequenceService,
        protected AuditLogService $auditLog
    ) {
    }

    public function create(LandParcel $landParcel)
    {
        return view('erp.land.feasibility.create', compact('landParcel'));
    }

    public function store(Request $request, LandParcel $landParcel)
    {
        $validated = $request->validate([
            'proposed_building_type'      => 'required|string',
            'num_of_basements'            => 'required|integer|min:0|max:5',
            'num_of_stories'              => 'required|integer|min:1|max:60',
            'permissible_far'             => 'required|numeric|min:0.5|max:20',
            'est_construction_cost_sqft'  => 'required|numeric|min:100',
            'projected_selling_rate_sqft' => 'required|numeric|min:100',
            'est_land_and_approval_cost'  => 'nullable|numeric|min:0',
            'payback_period_months'       => 'nullable|integer|min:1',
        ]);

        $landAreaSqft = $landParcel->total_area_katha * 720;
        $gfa = $landAreaSqft * $validated['permissible_far'];
        $sellableArea = $gfa * 0.82; // 82% net efficiency

        $loRatio = $landParcel->landowner_ratio ?? 50.00;
        $devRatio = $landParcel->developer_ratio ?? (100 - $loRatio);

        $loShareSqft = $sellableArea * ($loRatio / 100);
        $devShareSqft = $sellableArea * ($devRatio / 100);

        $totalConstructionCost = $gfa * $validated['est_construction_cost_sqft'];
        $approvalCost = $validated['est_land_and_approval_cost'] ?? 0;
        $signingPaid = $landParcel->signing_money_paid ?? 0;
        $totalProjectCost = $totalConstructionCost + $approvalCost + $signingPaid;

        $projectedRevenue = $devShareSqft * $validated['projected_selling_rate_sqft'];
        $netProfit = $projectedRevenue - $totalProjectCost;
        $roi = $totalProjectCost > 0 ? ($netProfit / $totalProjectCost) * 100 : 0;

        $version = $landParcel->feasibilities()->count() + 1;
        $feasibilityCode = $this->sequenceService->getNextNumber('feasibility', $landParcel->company_id, $landParcel->branch_id);

        $feasibility = LandFeasibility::create([
            'land_parcel_id'              => $landParcel->id,
            'feasibility_code'            => $feasibilityCode,
            'version'                     => $version,
            'proposed_building_type'      => $validated['proposed_building_type'],
            'num_of_basements'            => $validated['num_of_basements'],
            'num_of_stories'              => $validated['num_of_stories'],
            'permissible_far'             => $validated['permissible_far'],
            'gross_floor_area_sqft'       => $gfa,
            'sellable_area_sqft'          => $sellableArea,
            'landowner_share_sqft'        => $loShareSqft,
            'developer_share_sqft'        => $devShareSqft,
            'est_construction_cost_sqft'  => $validated['est_construction_cost_sqft'],
            'est_total_construction_cost' => $totalConstructionCost,
            'est_land_and_approval_cost'  => $approvalCost,
            'est_total_project_cost'      => $totalProjectCost,
            'projected_selling_rate_sqft' => $validated['projected_selling_rate_sqft'],
            'projected_gross_revenue'     => $projectedRevenue,
            'projected_net_profit'        => $netProfit,
            'roi_percentage'              => $roi,
            'payback_period_months'       => $validated['payback_period_months'] ?? 24,
            'status'                      => 'draft',
            'created_by'                  => auth('admin')->id(),
        ]);

        $this->auditLog->log('land.feasibility_calculated', $feasibility, null, $feasibility->toArray(), $landParcel->company_id, $landParcel->branch_id);

        return redirect()->route('erp.land-parcels.show', $landParcel)->with('success', "Feasibility Study {$feasibility->feasibility_code} generated (ROI: " . number_format($roi, 1) . "%).");
    }

    public function show(LandFeasibility $feasibility)
    {
        $feasibility->load(['parcel.owners', 'createdBy', 'approvedBy']);
        return view('erp.land.feasibility.show', compact('feasibility'));
    }

    public function approve(Request $request, LandFeasibility $feasibility)
    {
        $feasibility->update([
            'status'               => 'approved',
            'approved_by_admin_id' => auth('admin')->id(),
            'approval_remarks'     => $request->approval_remarks ?? 'Feasibility approved for architectural sanction and project launching.',
        ]);

        $feasibility->parcel->update(['status' => 'feasibility_study']);

        $this->auditLog->log('land.feasibility_approved', $feasibility, null, $feasibility->toArray(), $feasibility->parcel->company_id);

        return back()->with('success', "Feasibility {$feasibility->feasibility_code} approved.");
    }
}

