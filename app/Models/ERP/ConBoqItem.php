<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConBoqItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'con_boq_items';

    protected $fillable = [
        'company_id',
        'project_id',
        'category',
        'item_name',
        'unit_of_measure',
        'estimated_quantity',
        'estimated_unit_rate',
        'total_estimated_cost',
        'actual_consumed_qty',
        'actual_total_cost',
        'specifications',
    ];

    protected $casts = [
        'estimated_quantity'   => 'decimal:2',
        'estimated_unit_rate'  => 'decimal:2',
        'total_estimated_cost' => 'decimal:2',
        'actual_consumed_qty'  => 'decimal:2',
        'actual_total_cost'    => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(DevProject::class, 'project_id');
    }
}
