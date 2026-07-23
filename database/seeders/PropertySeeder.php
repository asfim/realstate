<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $p = \App\Models\Property::create([
            'title' => 'Amabilia',
            'type' => 'Apartment',
            'location' => 'Mohakhali, Dhaka',
            'price' => 'Contact for Price',
            'beds' => 3,
            'baths' => 3,
            'area' => 2100,
            'is_active' => true
        ]);

        $p->detail()->create([
            'description' => 'Blessed with an incredible number of conveniences, Amabilia is a love letter to refined luxury living. The corner plot home dictates amazing spaciousness, open surroundings, and charming architectural details primed with greenery. Amabilia features 2100+ sft luxurious units propped with Smart Home features.',
            'address' => 'Plot-12, Abbas Garden Road, Mohakhali, Dhaka',
            'land_area' => '8 Katha',
            'no_of_floors' => 'B+G+7',
            'apartments_per_floor' => '2',
            'launch_date' => 'October 2023',
            'expected_completion' => 'September 2026',
            'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.9024424301397!2d90.3910801154313!3d23.75085809467885!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjPCsDQ1JzAzLjEiTiA5MMKwMjMnMzUuNyJF!5e0!3m2!1sen!2sbd!4v1620000000000!5m2!1sen!2sbd',
            'gallery' => [],
            'floor_plans' => []
        ]);

        $p->constructionStatus()->create([
            'status_updated_at' => 'June, 2026',
            'progress_items' => [
                ['name' => 'Foundation work', 'progress' => '100%'],
                ['name' => 'Super Structure work', 'progress' => '100%'],
                ['name' => 'Block work', 'progress' => '100%'],
                ['name' => 'Panel Work', 'progress' => '100%'],
                ['name' => 'Electrical conducing work', 'progress' => '95%'],
                ['name' => 'Door frame fitting work', 'progress' => '100%'],
                ['name' => 'Sanitary & plumbing line work', 'progress' => '98%'],
                ['name' => 'Inside Plastering work', 'progress' => '98%'],
                ['name' => 'Electrical cable wiring work', 'progress' => '95%'],
                ['name' => 'Wall Tiles & floor tiles work', 'progress' => '95%']
            ]
        ]);
    }
}
