<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConRequisitionItem extends Model
{
    use HasFactory;

    protected $table = 'con_requisition_items';

    protected $fillable = [
        'requisition_id',
        'material_name',
        'unit_of_measure',
        'requested_qty',
        'approved_qty',
        'remarks',
    ];

    protected $casts = [
        'requested_qty' => 'decimal:2',
        'approved_qty'  => 'decimal:2',
    ];

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(ConMaterialRequisition::class, 'requisition_id');
    }
}
