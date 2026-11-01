<?php

namespace App\Models\ERP;

use App\Models\FeaturedProject;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicPropertyLink extends Model
{
    use HasFactory;

    protected $table = 'erp_public_property_links';

    protected $fillable = [
        'property_id',
        'unit_id',
        'sync_status',
        'notes',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
