<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstructionStatus extends Model
{
    /** @use HasFactory<\Database\Factories\ConstructionStatusFactory> */
    use HasFactory;

    protected $fillable = [
        'property_id',
        'status_updated_at',
        'progress_items',
    ];

    protected $casts = [
        'progress_items' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
