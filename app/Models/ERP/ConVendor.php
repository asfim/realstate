<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConVendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'con_vendors';

    protected $fillable = [
        'company_id',
        'vendor_code',
        'name',
        'contact_person',
        'phone',
        'email',
        'trade_license_no',
        'tin_no',
        'bank_details',
        'status',
        'address',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(ConPurchaseOrder::class, 'vendor_id');
    }

    public function grns(): HasMany
    {
        return $this->hasMany(ConGrn::class, 'vendor_id');
    }
}

