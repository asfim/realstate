<?php

use App\Models\Admin;
use App\Models\ERP\Company;
use App\Models\ERP\Project;
use App\Models\ERP\Unit;
use App\Models\Property;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ERPPhase1Seeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(ERPPhase1Seeder::class);
});

test('super admin can view unit inventory index and unit profile', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $unit = Unit::first();

    $response = $this->actingAs($admin, 'admin')->get(route('erp.units.index'));
    $response->assertStatus(200);
    $response->assertSee('Unit Inventory Master Desk');

    $response = $this->actingAs($admin, 'admin')->get(route('erp.units.show', $unit));
    $response->assertStatus(200);
    $response->assertSee($unit->unit_code);
});

test('can create a residential flat unit with exact decimal price', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $company = Company::first();
    $project = Project::where('type', 'flat')->first();
    $building = $project->buildings()->first();
    $floor = $building?->floors()->first();

    $payload = [
        'company_id' => $company->id,
        'project_id' => $project->id,
        'building_id' => $building?->id,
        'floor_id' => $floor?->id,
        'unit_code' => 'FLAT-9B',
        'title' => 'Penthouse 9B South Facing',
        'type' => 'flat',
        'status' => 'available',
        'facing' => 'south',
        'area' => 2850.50,
        'area_unit' => 'sqft',
        'asking_price' => 42757500.00,
        'min_price' => 41000000.00,
        'bedrooms' => 4,
        'bathrooms' => 4,
        'balconies' => 3,
        'parking_included' => 1,
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.units.store'), $payload);
    $response->assertRedirect();

    $this->assertDatabaseHas('erp_units', [
        'unit_code' => 'FLAT-9B',
        'project_id' => $project->id,
        'asking_price' => 42757500.00,
    ]);

    // Verify initial status event was logged
    $this->assertDatabaseHas('erp_unit_status_events', [
        'from_status' => 'none',
        'to_status' => 'available',
    ]);
});

test('can create a residential plot unit with katha and road width', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $company = Company::first();
    $project = Project::where('type', 'plot')->first();
    $block = $project->blocks()->first();

    $payload = [
        'company_id' => $company->id,
        'project_id' => $project->id,
        'block_id' => $block?->id,
        'unit_code' => 'PLOT-Z99',
        'title' => 'Corner Estate Plot Z99',
        'type' => 'plot',
        'status' => 'available',
        'facing' => 'south_east',
        'area' => 10.0000,
        'area_unit' => 'katha',
        'asking_price' => 25000000.00,
        'road_width_ft' => 80.00,
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.units.store'), $payload);
    $response->assertRedirect();

    $this->assertDatabaseHas('erp_units', [
        'unit_code' => 'PLOT-Z99',
        'area' => 10.0000,
        'area_unit' => 'katha',
        'road_width_ft' => 80.00,
    ]);
});

test('can revise unit price and generate price history audit record', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $unit = Unit::first();
    $oldPrice = $unit->asking_price;
    $newPrice = 45000000.00;

    $payload = [
        'new_price' => $newPrice,
        'new_min_price' => 43000000.00,
        'reason' => 'Annual price escalation approved by CEO',
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.units.revise-price', $unit), $payload);
    $response->assertRedirect();

    $unit->refresh();
    expect((float)$unit->asking_price)->toBe((float)$newPrice);

    $this->assertDatabaseHas('erp_unit_price_histories', [
        'unit_id' => $unit->id,
        'old_price' => $oldPrice,
        'new_price' => $newPrice,
        'reason' => 'Annual price escalation approved by CEO',
    ]);
});

test('can transition unit lifecycle status and log status event', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $unit = Unit::where('status', 'available')->first();

    $payload = [
        'status' => 'held',
        'notes' => 'Customer reserved for 72 hours with token slip',
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.units.transition-status', $unit), $payload);
    $response->assertRedirect();

    $unit->refresh();
    expect($unit->status)->toBe('held');

    $this->assertDatabaseHas('erp_unit_status_events', [
        'unit_id' => $unit->id,
        'from_status' => 'available',
        'to_status' => 'held',
        'notes' => 'Customer reserved for 72 hours with token slip',
    ]);
});

test('cannot transition unit status to an illegal state', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    // Unit with 'draft' cannot transition directly to 'sold'
    $unit = Unit::first();
    $unit->update(['status' => 'draft']);

    $payload = [
        'status' => 'sold',
        'notes' => 'Attempting illegal bypass',
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.units.transition-status', $unit), $payload);
    $response->assertSessionHasErrors(['status']);

    $unit->refresh();
    expect($unit->status)->toBe('draft');
});

test('can link an erp unit to a website public property listing', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $unit = Unit::first();
    $property = Property::first();

    $response = $this->actingAs($admin, 'admin')->post(route('erp.units.link-property', $unit), [
        'property_id' => $property->id,
    ]);
    $response->assertRedirect();

    $this->assertDatabaseHas('erp_public_property_links', [
        'unit_id' => $unit->id,
        'property_id' => $property->id,
        'sync_status' => 'synced',
    ]);
});

