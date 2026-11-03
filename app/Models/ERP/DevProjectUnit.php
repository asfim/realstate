<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevProjectUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dev_project_units';

    protected $fillable = [
        'project_id',
        'unit_number',
        'floor_label',
        'unit_type',
        'allocation_share',
        'gross_area_sqft',
        'net_carpet_area_sqft',
        'bedrooms',
        'bathrooms',
        'balconies',
        'facing',
        'base_price_sqft',
        'total_unit_price',
        'utility_charges',
        'grand_total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'gross_area_sqft'      => 'decimal:2',
        'net_carpet_area_sqft' => 'decimal:2',
        'bedrooms'             => 'integer',
        'bathrooms'            => 'integer',
        'balconies'            => 'integer',
        'base_price_sqft'      => 'decimal:2',
        'total_unit_price'     => 'decimal:2',
        'utility_charges'      => 'decimal:2',
        'grand_total_price'    => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(DevProject::class, 'project_id');
    }

    public function parkingSlots(): HasMany
    {
        return $this->hasMany(DevParkingSlot::class, 'assigned_unit_id');
    }

    public function booking(): HasOne
    {
        return $this->hasOne(DevSalesBooking::class, 'unit_id')->where('status', '!=', 'cancelled');
    }
}
