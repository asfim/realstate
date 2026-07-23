<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedProperty extends Model
{
    /** @use HasFactory<\Database\Factories\FeaturedPropertyFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'location', 'price', 'beds', 'baths', 'area', 'image', 'is_active'
    ];
}
