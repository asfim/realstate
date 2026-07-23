<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'title' => 'Apartment',
                'image' => 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=700&q=75',
                'icon_class' => 'fa-solid fa-building'
            ],
            [
                'title' => 'Villa',
                'image' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=700&q=75',
                'icon_class' => 'fa-solid fa-house'
            ],
            [
                'title' => 'Duplex',
                'image' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=700&q=75',
                'icon_class' => 'fa-solid fa-house-chimney'
            ],
            [
                'title' => 'Commercial',
                'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=700&q=75',
                'icon_class' => 'fa-solid fa-shop'
            ],
            [
                'title' => 'Office',
                'image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=700&q=75',
                'icon_class' => 'fa-solid fa-briefcase'
            ],
            [
                'title' => 'Land',
                'image' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=700&q=75',
                'icon_class' => 'fa-solid fa-map'
            ],
        ];

        foreach ($types as $type) {
            \App\Models\PropertyType::updateOrCreate(
                ['title' => $type['title']],
                [
                    'image' => $type['image'],
                    'icon_class' => $type['icon_class']
                ]
            );
        }
    }
}
