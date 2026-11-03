<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyTenantLease extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agency_tenant_leases';

    protected $fillable = [
        'company_id',
        'branch_id',
        'lease_code',
        'listing_id',
        'tenant_id',
        'monthly_rent',
        'security_deposit_amount',
        'monthly_service_charge',
        'lease_start_date',
        'lease_end_date',
        'rent_due_day',
        'status',
        'key_handover_date',
        'special_terms',
        'created_by',
    ];

    protected $casts = [
        'monthly_rent'            => 'decimal:2',
        'security_deposit_amount' => 'decimal:2',
        'monthly_service_charge'  => 'decimal:2',
        'lease_start_date'        => 'date',
        'lease_end_date'          => 'date',
        'key_handover_date'       => 'date',
        'rent_due_day'            => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(AgencyListing::class, 'listing_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(CrmCustomer::class, 'tenant_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}

