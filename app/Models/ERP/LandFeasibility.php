<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandFeasibility extends Model
{
    use HasFactory;

    protected $table = 'land_feasibilities';

    protected $fillable = [
        'land_parcel_id',
        'feasibility_code',
        'version',
        'proposed_building_type',
        'num_of_basements',
        'num_of_stories',
        'permissible_far',
        'gross_floor_area_sqft',
        'sellable_area_sqft',
        'landowner_share_sqft',
        'developer_share_sqft',
        'est_construction_cost_sqft',
        'est_total_construction_cost',
        'est_land_and_approval_cost',
        'est_total_project_cost',
        'projected_selling_rate_sqft',
        'projected_gross_revenue',
        'projected_net_profit',
        'roi_percentage',
        'payback_period_months',
        'status',
        'approved_by_admin_id',
        'approval_remarks',
        'created_by',
    ];

    protected $casts = [
        'version'                     => 'integer',
        'num_of_basements'            => 'integer',
        'num_of_stories'              => 'integer',
        'permissible_far'             => 'decimal:2',
        'gross_floor_area_sqft'       => 'decimal:2',
        'sellable_area_sqft'          => 'decimal:2',
        'landowner_share_sqft'        => 'decimal:2',
        'developer_share_sqft'        => 'decimal:2',
        'est_construction_cost_sqft'  => 'decimal:2',
        'est_total_construction_cost' => 'decimal:2',
        'est_land_and_approval_cost'  => 'decimal:2',
        'est_total_project_cost'      => 'decimal:2',
        'projected_selling_rate_sqft' => 'decimal:2',
        'projected_gross_revenue'     => 'decimal:2',
        'projected_net_profit'        => 'decimal:2',
        'roi_percentage'              => 'decimal:2',
        'payback_period_months'       => 'integer',
    ];

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(LandParcel::class, 'land_parcel_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by_admin_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
