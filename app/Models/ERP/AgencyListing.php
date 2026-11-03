<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyListing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agency_listings';

    protected $fillable = [
        'company_id',
        'branch_id',
        'listing_code',
        'title',
        'listing_type',
        'property_category',
        'location',
        'address',
        'size_sqft',
        'bedrooms',
        'bathrooms',
        'floor_number',
        'landlord_seller_name',
        'landlord_seller_phone',
        'landlord_seller_email',
        'mandate_type',
        'mandate_expiry_date',
        'asking_price',
        'reserve_price',
        'commission_rate_percent',
        'expected_commission',
        'key_held_at_branch',
        'status',
        'assigned_agent_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'size_sqft'               => 'decimal:2',
        'asking_price'            => 'decimal:2',
        'reserve_price'           => 'decimal:2',
        'commission_rate_percent' => 'decimal:2',
        'expected_commission'     => 'decimal:2',
        'key_held_at_branch'      => 'boolean',
        'mandate_expiry_date'     => 'date',
        'bedrooms'                => 'integer',
        'bathrooms'               => 'integer',
        'floor_number'            => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_agent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(AgencyDeal::class, 'listing_id');
    }

    public function leases(): HasMany
    {
        return $this->hasMany(AgencyTenantLease::class, 'listing_id');
    }
}
