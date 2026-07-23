<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'short_description',
        'description',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_active' => 'boolean',
    ];
}
