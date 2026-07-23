<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'featured_project_id',
        'property_id',
        'name',
        'email',
        'phone',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function featuredProject()
    {
        return $this->belongsTo(FeaturedProject::class, 'featured_project_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
