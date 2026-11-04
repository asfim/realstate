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

    public function getFormattedPriceAttribute(): string
    {
        if (empty($this->price)) {
            return 'Price on Call';
        }

        $clean = preg_replace('/[^\d.]/', '', (string)$this->price);
        if ($clean !== '' && is_numeric($clean)) {
            return 'BDT ' . number_format((float)$clean);
        }

        return (string)$this->price;
    }
}
