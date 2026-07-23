<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'location',
        'image',
        'price',
        'beds',
        'baths',
        'area',
        'is_active',
    ];

    public function detail()
    {
        return $this->hasOne(PropertyDetail::class);
    }

    public function constructionStatus()
    {
        return $this->hasOne(ConstructionStatus::class);
    }
}
