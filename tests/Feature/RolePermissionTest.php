<?php

use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    // Run the full database seeder to set up default admin, roles and permissions
    $this->seed(DatabaseSeeder::class);
});

test('super admin can access dashboard and site settings', function () {
    $superAdmin = Admin::where('email', 'admin@example.com')->first();
    
    $response = $this->actingAs($superAdmin, 'admin')->get(route('admin.dashboard'));
    $response->assertStatus(200);

    $response = $this->actingAs($superAdmin, 'admin')->get(route('admin.settings.site'));
    $response->assertStatus(200);
});

test('viewer has read-only access and cannot edit settings or create properties', function () {
    $viewer = Admin::create([
        'name' => 'Viewer Admin',
        'email' => 'viewer@example.com',
        'password' => bcrypt('password'),
    ]);
    
    $viewer->assignRole('Viewer');

    // Viewer has dashboard.view permission, so they should be able to see dashboard
    $response = $this->actingAs($viewer, 'admin')->get(route('admin.dashboard'));
    $response->assertStatus(200);

    // Viewer has settings.view permission, so they should be able to see site settings
    $response = $this->actingAs($viewer, 'admin')->get(route('admin.settings.site'));
    $response->assertStatus(200);

    // Viewer does NOT have settings.edit permission, so updating settings should give 403
    $response = $this->actingAs($viewer, 'admin')->post(route('admin.settings.update'), ['site_title' => 'New Title']);
    $response->assertStatus(403);

    // Viewer does NOT have properties.create, so they should get 403 when trying to access the create page
    $response = $this->actingAs($viewer, 'admin')->get(route('admin.properties.create'));
    $response->assertStatus(403);
});

test('roles list is accessible only to super admin and authorized users', function () {
    $viewer = Admin::create([
        'name' => 'Viewer Admin',
        'email' => 'viewer@example.com',
        'password' => bcrypt('password'),
    ]);
    $viewer->assignRole('Viewer');

    // Viewer does NOT have roles.view permission, so they should get 403
    $response = $this->actingAs($viewer, 'admin')->get(route('admin.roles.index'));
    $response->assertStatus(403);

    $superAdmin = Admin::where('email', 'admin@example.com')->first();
    $response = $this->actingAs($superAdmin, 'admin')->get(route('admin.roles.index'));
    $response->assertStatus(200);
});
