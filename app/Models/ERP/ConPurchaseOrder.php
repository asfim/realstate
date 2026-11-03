<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConPurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'con_purchase_orders';

    protected $fillable = [
        'company_id',
        'branch_id',
        'project_id',
        'vendor_id',
        'requisition_id',
        'po_code',
        'po_date',
        'delivery_due_date',
        'subtotal',
        'tax_vat_amount',
        'grand_total',
        'payment_terms',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'subtotal'          => 'decimal:2',
        'tax_vat_amount'    => 'decimal:2',
        'grand_total'       => 'decimal:2',
        'po_date'           => 'date',
        'delivery_due_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(DevProject::class, 'project_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(ConVendor::class, 'vendor_id');
    }

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(ConMaterialRequisition::class, 'requisition_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ConPoItem::class, 'purchase_order_id');
    }

    public function grns(): HasMany
    {
        return $this->hasMany(ConGrn::class, 'purchase_order_id');
    }
}
