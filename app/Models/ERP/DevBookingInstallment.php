<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevBookingInstallment extends Model
{
    use HasFactory;

    protected $table = 'dev_booking_installments';

    protected $fillable = [
        'booking_id',
        'milestone_name',
        'installment_number',
        'due_date',
        'due_amount',
        'paid_amount',
        'balance_due',
        'status',
        'paid_date',
        'payment_reference',
    ];

    protected $casts = [
        'due_date'           => 'date',
        'paid_date'          => 'date',
        'due_amount'         => 'decimal:2',
        'paid_amount'        => 'decimal:2',
        'balance_due'        => 'decimal:2',
        'installment_number' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(DevSalesBooking::class, 'booking_id');
    }
}

