<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'author',
        'published_at',
        'summary',
        'content',
        'image',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_active' => 'boolean',
    ];
}
