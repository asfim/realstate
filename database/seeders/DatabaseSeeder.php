<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );

        $this->call([
            AdminSeeder::class,
            RolesAndPermissionsSeeder::class,
            PropertyTypeSeeder::class,
            PropertySeeder::class,
            FeaturedProjectSeeder::class,
            ErpMasterDemoSeeder::class,
        ]);
    }
}
