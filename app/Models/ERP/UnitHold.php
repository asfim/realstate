<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitHold extends Model
{
    use HasFactory;

    protected $table = 'erp_unit_holds';

    protected $fillable = [
        'company_id',
        'unit_id',
        'customer_id',
        'held_until',
        'token_deposit',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'held_until' => 'datetime',
            'token_deposit' => 'decimal:2',
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
}
