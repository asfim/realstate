<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dev_projects';

    protected $fillable = [
        'company_id',
        'branch_id',
        'land_parcel_id',
        'cost_center_id',
        'project_code',
        'name',
        'project_type',
        'address',
        'location',
        'land_area_katha',
        'total_floors',
        'total_basements',
        'total_units',
        'total_parking_slots',
        'sanction_no',
        'architect_name',
        'structural_engineer',
        'start_date',
        'expected_completion_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'land_area_katha'          => 'decimal:4',
        'total_floors'             => 'integer',
        'total_basements'          => 'integer',
        'total_units'              => 'integer',
        'total_parking_slots'      => 'integer',
        'start_date'               => 'date',
        'expected_completion_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function landParcel(): BelongsTo
    {
        return $this->belongsTo(LandParcel::class, 'land_parcel_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(DevProjectUnit::class, 'project_id');
    }

    public function parkingSlots(): HasMany
    {
        return $this->hasMany(DevParkingSlot::class, 'project_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(DevSalesBooking::class, 'project_id');
    }

    public function availableUnitsCount(): int
    {
        return $this->units()->where('status', 'available')->count();
    }

    public function soldUnitsCount(): int
    {
        return $this->units()->whereIn('status', ['booked', 'sold', 'handed_over'])->count();
    }
}

