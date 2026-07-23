<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyDetail extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyDetailFactory> */
    use HasFactory;

    protected $fillable = [
        'property_id',
        'description',
        'gallery',
        'floor_plans',
        'map_url',
        'address',
        'land_area',
        'no_of_floors',
        'apartments_per_floor',
        'launch_date',
        'expected_completion',
    ];

    protected $casts = [
        'gallery' => 'array',
        'floor_plans' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
