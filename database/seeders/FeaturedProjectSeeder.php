<?php

namespace Database\Seeders;

use App\Models\FeaturedProject;
use Illuminate\Database\Seeder;

class FeaturedProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Amabilia Luxury Residence',
                'location' => 'Mohakhali, Dhaka',
                'price' => '$450,000',
                'beds' => 3,
                'baths' => 3,
                'area' => 2100,
                'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                'video_url' => 'https://www.youtube.com/watch?v=j-3nl88O7uI',
                'is_active' => true,
            ],
            [
                'title' => 'Aurelia Skyline Tower',
                'location' => 'Gulshan 2, Dhaka',
                'price' => '$850,000',
                'beds' => 4,
                'baths' => 4,
                'area' => 3200,
                'image' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
                'video_url' => 'https://www.youtube.com/watch?v=j-3nl88O7uI',
                'is_active' => true,
            ],
            [
                'title' => 'Grand Imperial Heights',
                'location' => 'Banani, Dhaka',
                'price' => '$620,000',
                'beds' => 3,
                'baths' => 3,
                'area' => 2500,
                'image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=80',
                'video_url' => 'https://www.youtube.com/watch?v=j-3nl88O7uI',
                'is_active' => true,
            ],
        ];

        foreach ($projects as $project) {
            FeaturedProject::updateOrCreate(
                ['title' => $project['title']],
                $project
            );
        }
    }
}
