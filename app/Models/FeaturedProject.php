<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedProject extends Model
{
    protected $table = 'featured_projects';

    protected $fillable = [
        'title',
        'location',
        'price',
        'beds',
        'baths',
        'area',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
