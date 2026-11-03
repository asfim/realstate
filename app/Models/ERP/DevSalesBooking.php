<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevSalesBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dev_sales_bookings';

    protected $fillable = [
        'company_id',
        'branch_id',
        'booking_code',
        'customer_id',
        'lead_id',
        'project_id',
        'unit_id',
        'parking_slot_id',
        'sales_agent_id',
        'agreed_price_sqft',
        'agreed_unit_price',
        'agreed_parking_price',
        'agreed_utility_charge',
        'total_contract_amount',
        'downpayment_amount',
        'downpayment_due_date',
        'payment_plan_type',
        'total_installments',
        'booking_date',
        'agreement_date',
        'expected_handover_date',
        'commission_rate',
        'commission_amount',
        'status',
        'cancellation_reason',
        'special_conditions',
        'created_by',
    ];

    protected $casts = [
        'agreed_price_sqft'      => 'decimal:2',
        'agreed_unit_price'      => 'decimal:2',
        'agreed_parking_price'   => 'decimal:2',
        'agreed_utility_charge'  => 'decimal:2',
        'total_contract_amount'  => 'decimal:2',
        'downpayment_amount'     => 'decimal:2',
        'commission_rate'        => 'decimal:2',
        'commission_amount'      => 'decimal:2',
        'total_installments'     => 'integer',
        'downpayment_due_date'   => 'date',
        'booking_date'           => 'date',
        'agreement_date'         => 'date',
        'expected_handover_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CrmCustomer::class, 'customer_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(DevProject::class, 'project_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(DevProjectUnit::class, 'unit_id');
    }

    public function parkingSlot(): BelongsTo
    {
        return $this->belongsTo(DevParkingSlot::class, 'parking_slot_id');
    }

    public function salesAgent(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'sales_agent_id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(DevBookingInstallment::class, 'booking_id')->orderBy('due_date');
    }

    public function totalPaid(): float
    {
        return (float) $this->installments()->sum('paid_amount');
    }

    public function balanceDue(): float
    {
        return (float) ($this->total_contract_amount - $this->totalPaid());
    }
}
