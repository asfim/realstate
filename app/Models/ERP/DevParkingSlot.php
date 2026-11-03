<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevParkingSlot extends Model
{
    use HasFactory;

    protected $table = 'dev_parking_slots';

    protected $fillable = [
        'project_id',
        'slot_number',
        'basement_level',
        'assigned_unit_id',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(DevProject::class, 'project_id');
    }

    public function assignedUnit(): BelongsTo
    {
        return $this->belongsTo(DevProjectUnit::class, 'assigned_unit_id');
    }
}
