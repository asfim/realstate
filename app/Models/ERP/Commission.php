<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'erp_commissions';

    protected $fillable = [
        'company_id',
        'employee_id',
        'booking_id',
        'booking_amount',
        'rate_percent',
        'commission_amount',
        'status',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'booking_amount' => 'decimal:2',
            'rate_percent' => 'decimal:2',
            'commission_amount' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}

