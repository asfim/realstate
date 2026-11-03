<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConSiteStock extends Model
{
    use HasFactory;

    protected $table = 'con_site_stocks';

    protected $fillable = [
        'company_id',
        'project_id',
        'material_name',
        'unit_of_measure',
        'current_stock_qty',
        'total_received_qty',
        'total_consumed_qty',
        'last_received_date',
    ];

    protected $casts = [
        'current_stock_qty'  => 'decimal:2',
        'total_received_qty' => 'decimal:2',
        'total_consumed_qty' => 'decimal:2',
        'last_received_date' => 'date',
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
