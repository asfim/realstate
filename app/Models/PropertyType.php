<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    protected $fillable = ['title', 'image', 'icon_class'];

    public function getNameAttribute(): ?string
    {
        return $this->title;
    }
}
