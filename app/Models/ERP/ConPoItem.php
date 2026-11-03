<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConPoItem extends Model
{
    use HasFactory;

    protected $table = 'con_po_items';

    protected $fillable = [
        'purchase_order_id',
        'item_name',
        'unit_of_measure',
        'quantity',
        'unit_rate',
        'line_total',
        'received_qty',
    ];

    protected $casts = [
        'quantity'     => 'decimal:2',
        'unit_rate'    => 'decimal:2',
        'line_total'   => 'decimal:2',
        'received_qty' => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(ConPurchaseOrder::class, 'purchase_order_id');
    }
}
