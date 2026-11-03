<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyDeal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agency_deals';

    protected $fillable = [
        'company_id',
        'branch_id',
        'deal_code',
        'listing_id',
        'customer_id',
        'agent_id',
        'deal_type',
        'closing_price',
        'company_commission_amount',
        'agent_commission_percent',
        'agent_commission_amount',
        'external_broker_name',
        'external_broker_phone',
        'external_broker_commission_amount',
        'deal_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'closing_price'                      => 'decimal:2',
        'company_commission_amount'          => 'decimal:2',
        'agent_commission_percent'           => 'decimal:2',
        'agent_commission_amount'            => 'decimal:2',
        'external_broker_commission_amount'  => 'decimal:2',
        'deal_date'                          => 'date',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CrmCustomer::class, 'customer_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'agent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
