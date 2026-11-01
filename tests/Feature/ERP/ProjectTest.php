<?php

use App\Models\Admin;
use App\Models\ERP\Company;
use App\Models\ERP\Project;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ERPPhase1Seeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(ERPPhase1Seeder::class);
});

test('super admin can view erp dashboard and projects list', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($admin, 'admin')->get(route('erp.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Real Estate ERP Dashboard');

    $response = $this->actingAs($admin, 'admin')->get(route('erp.projects.index'));
    $response->assertStatus(200);
    $response->assertSee('Real Estate Projects');
});

test('authorized admin can create a new project with valid data', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $company = Company::first();

    $payload = [
        'company_id' => $company->id,
        'code' => 'PRJ-TEST-TOWER',
        'name' => 'Test Luxury Tower',
        'type' => 'flat',
        'status' => 'ongoing',
        'ownership_type' => 'own_land',
        'location' => 'Bashundhara R/A, Dhaka',
        'budget' => 150000000.00,
        'launch_date' => '2026-01-01',
        'target_end_date' => '2028-12-31',
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.projects.store'), $payload);
    $response->assertRedirect();

    $this->assertDatabaseHas('erp_projects', [
        'code' => 'PRJ-TEST-TOWER',
        'name' => 'Test Luxury Tower',
        'type' => 'flat',
    ]);
});

test('cannot create project with duplicate code in the same company', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $company = Company::first();
    $existing = Project::where('company_id', $company->id)->first();

    $payload = [
        'company_id' => $company->id,
        'code' => $existing->code,
        'name' => 'Duplicate Code Project',
        'type' => 'plot',
        'status' => 'planning',
        'ownership_type' => 'own_land',
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.projects.store'), $payload);
    $response->assertSessionHasErrors(['code']);
});

test('can add a building with automatic floors to a project', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $project = Project::where('type', 'flat')->first();

    $payload = [
        'code' => 'TOWER-C',
        'name' => 'Tower C Premium',
        'total_floors' => 10,
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.projects.buildings.store', $project), $payload);
    $response->assertRedirect();

    $this->assertDatabaseHas('erp_buildings', [
        'project_id' => $project->id,
        'code' => 'TOWER-C',
        'total_floors' => 10,
    ]);

    $this->assertDatabaseHas('erp_floors', [
        'floor_name' => 'Floor 1',
        'floor_number' => 1,
    ]);
});
