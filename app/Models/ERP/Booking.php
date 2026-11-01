<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'erp_bookings';

    protected $fillable = [
        'company_id',
        'unit_id',
        'customer_id',
        'booking_no',
        'booking_date',
        'contract_price',
        'booking_money',
        'down_payment',
        'discount_amount',
        'balance_amount',
        'status',
        'active_token',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'contract_price' => 'decimal:2',
            'booking_money' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class, 'booking_id')->orderBy('installment_no');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id')->latest('payment_date');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'booking_id');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->where('status', 'verified')->sum('amount');
    }

    public function getDueAmountAttribute(): float
    {
        return max(0, (float) $this->contract_price - $this->total_paid);
    }
}

