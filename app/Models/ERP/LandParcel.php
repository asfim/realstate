<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandParcel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'land_parcels';

    protected $fillable = [
        'company_id',
        'branch_id',
        'cost_center_id',
        'parcel_code',
        'title',
        'mouza',
        'khatian_no',
        'dag_no',
        'total_area_katha',
        'total_area_decimal',
        'acquisition_type',
        'status',
        'landowner_ratio',
        'developer_ratio',
        'estimated_land_value',
        'signing_money_committed',
        'signing_money_paid',
        'primary_contact_name',
        'primary_contact_phone',
        'target_handover_date',
        'boundary_north',
        'boundary_south',
        'boundary_east',
        'boundary_west',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'total_area_katha'        => 'decimal:4',
        'total_area_decimal'      => 'decimal:4',
        'landowner_ratio'         => 'decimal:2',
        'developer_ratio'         => 'decimal:2',
        'estimated_land_value'    => 'decimal:2',
        'signing_money_committed' => 'decimal:2',
        'signing_money_paid'      => 'decimal:2',
        'target_handover_date'    => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function owners(): HasMany
    {
        return $this->hasMany(LandParcelOwner::class, 'land_parcel_id');
    }

    public function vettings(): HasMany
    {
        return $this->hasMany(LandVetting::class, 'land_parcel_id');
    }

    public function feasibilities(): HasMany
    {
        return $this->hasMany(LandFeasibility::class, 'land_parcel_id')->latest();
    }

    public function latestFeasibility(): ?LandFeasibility
    {
        return $this->feasibilities()->first();
    }

    /**
     * Helper to check legal clearance percentage.
     */
    public function legalVettingClearance(): int
    {
        $total = $this->vettings()->count();
        if ($total === 0) {
            return 0;
        }

        $cleared = $this->vettings()->where('status', 'verified_clear')->count();
        return (int) round(($cleared / $total) * 100);
    }
}
