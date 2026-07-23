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
        'description',
        'address',
        'land_area',
        'no_of_floors',
        'apartments_per_floor',
        'launch_date',
        'expected_completion',
        'nearest_landmark',
        'map_url',
        'video_url',
        'video_file',
        'video_thumbnail',
        'brochure',
        'gallery',
        'floor_plans',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'gallery' => 'array',
        'floor_plans' => 'array',
    ];

    public function getFormattedVideoUrlAttribute(): ?string
    {
        if ($this->video_file) {
            return \Illuminate\Support\Facades\Storage::url($this->video_file);
        }

        if ($this->video_url) {
            $url = $this->video_url;
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=1&rel=0';
            }
            return $url;
        }

        return null;
    }

    public function getYoutubeThumbnailUrlAttribute(): ?string
    {
        if ($this->video_url) {
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches)) {
                return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
            }
        }
        return null;
    }
}
