<?php

use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\CrmCustomer;
use App\Models\ERP\DevBookingInstallment;
use App\Models\ERP\DevParkingSlot;
use App\Models\ERP\DevProject;
use App\Models\ERP\DevProjectUnit;
use App\Models\ERP\DevSalesBooking;
use Database\Seeders\ErpFoundationSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(ErpFoundationSeeder::class);

    $this->admin = Admin::firstOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name'     => 'Super Admin',
            'password' => Hash::make('password'),
        ]
    );

    if (!$this->admin->hasRole('Super Admin')) {
        $this->admin->assignRole('Super Admin');
    }

    $this->company = Company::where('code', 'AURELIA')->first();
    $this->branch = Branch::where('code', 'DHK-HQ')->first();

    $this->customer = CrmCustomer::create([
        'company_id'    => $this->company->id,
        'branch_id'     => $this->branch->id,
        'customer_code' => 'CUST-001',
        'name'          => 'Mr. Buyer Ahmed',
        'phone'         => '+880 1819 000111',
        'email'         => 'buyer@example.com',
        'status'        => 'active',
    ]);
});

test('admin can view developer projects and bookings list', function () {
    $responseProjects = $this->actingAs($this->admin, 'admin')->get('/erp/developer/projects');
    $responseProjects->assertStatus(200);
    $responseProjects->assertSee('Developer Projects');

    $responseBookings = $this->actingAs($this->admin, 'admin')->get('/erp/developer/bookings');
    $responseBookings->assertStatus(200);
    $responseBookings->assertSee('Developer Sales Bookings');
});

test('admin can register a developer project with auto project code', function () {
    $response = $this->actingAs($this->admin, 'admin')->post('/erp/developer/projects', [
        'name'                     => 'Aurelia Crown Heights',
        'location'                 => 'Plot 14, Road 11, Banani, Dhaka',
        'project_type'             => 'residential',
        'status'                   => 'planning',
        'land_area_katha'          => 15.00,
        'total_floors'             => 14,
        'total_basements'          => 1,
        'total_units'              => 28,
        'total_parking_slots'      => 32,
        'start_date'               => '2026-01-01',
        'expected_completion_date' => '2028-12-31',
    ]);

    $response->assertSessionHasNoErrors();

    $project = DevProject::where('name', 'Aurelia Crown Heights')->first();
    expect($project)->not->toBeNull()
        ->and($project->project_code)->toStartWith('PRJ-')
        ->and($project->total_floors)->toBe(14);
});

test('admin can add unit and parking slot to project', function () {
    $project = DevProject::create([
        'company_id'   => $this->company->id,
        'branch_id'    => $this->branch->id,
        'project_code' => 'PRJ-TEST',
        'name'         => 'Aurelia Test Tower',
        'project_type' => 'residential',
        'status'       => 'under_construction',
        'total_floors' => 10,
        'total_units'  => 20,
    ]);

    // Add Unit
    $unitResponse = $this->actingAs($this->admin, 'admin')->post("/erp/developer/projects/{$project->id}/units", [
        'unit_number'      => 'A-4',
        'floor_number'     => 4,
        'unit_type'        => '3BHK',
        'gross_area_sqft'  => 2250,
        'net_area_sqft'    => 1850,
        'bedrooms'         => 3,
        'bathrooms'        => 3,
        'balconies'        => 2,
        'facing_direction' => 'South-East',
        'base_price_sqft'  => 11500,
        'allocation_share' => 'developer_share',
        'status'           => 'available',
    ]);
    $unitResponse->assertSessionHasNoErrors();

    $unit = DevProjectUnit::where('project_id', $project->id)->where('unit_number', 'A-4')->first();
    expect($unit)->not->toBeNull()
        ->and((float) $unit->gross_area_sqft)->toEqual(2250.0)
        ->and($unit->status)->toBe('available');

    // Add Parking
    $parkingResponse = $this->actingAs($this->admin, 'admin')->post("/erp/developer/projects/{$project->id}/parking", [
        'slot_number'    => 'B1-05',
        'basement_level' => 'Basement 1',
        'price'          => 450000,
        'status'         => 'available',
    ]);
    $parkingResponse->assertSessionHasNoErrors();

    $parking = DevParkingSlot::where('project_id', $project->id)->where('slot_number', 'B1-05')->first();
    expect($parking)->not->toBeNull()
        ->and((float) $parking->price)->toEqual(450000.0);
});

test('admin can create sales booking for unit (marks unit booked, generates milestones)', function () {
    $project = DevProject::create([
        'company_id'   => $this->company->id,
        'branch_id'    => $this->branch->id,
        'project_code' => 'PRJ-TEST-2',
        'name'         => 'Aurelia Heights Phase 2',
        'project_type' => 'residential',
        'status'       => 'under_construction',
    ]);

    $unit = DevProjectUnit::create([
        'project_id'       => $project->id,
        'unit_number'      => 'B-6',
        'floor_number'     => 6,
        'unit_type'        => '3BHK',
        'gross_area_sqft'  => 2000,
        'net_area_sqft'    => 1650,
        'base_price_sqft'  => 10000,
        'allocation_share' => 'developer_share',
        'status'           => 'available',
    ]);

    $parking = DevParkingSlot::create([
        'project_id'     => $project->id,
        'slot_number'    => 'B1-09',
        'basement_level' => 'Basement 1',
        'price'          => 500000,
        'status'         => 'available',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post('/erp/developer/bookings', [
        'project_id'            => $project->id,
        'unit_id'               => $unit->id,
        'parking_slot_id'       => $parking->id,
        'customer_id'           => $this->customer->id,
        'sales_agent_id'        => $this->admin->id,
        'agreed_price_sqft'     => 10000,
        'agreed_parking_price'  => 500000,
        'agreed_utility_charge' => 300000,
        'downpayment_amount'    => 2000000,
        'downpayment_due_date'  => '2026-11-15',
        'payment_plan_type'     => 'installment',
        'total_installments'    => 12,
        'booking_date'          => '2026-11-03',
        'expected_handover_date'=> '2028-11-03',
    ]);

    $response->assertSessionHasNoErrors();

    // Unit contract: (2000 * 10000) = 20,000,000 + 500,000 + 300,000 = 20,800,000
    $booking = DevSalesBooking::where('unit_id', $unit->id)->first();
    expect($booking)->not->toBeNull()
        ->and($booking->booking_code)->toStartWith('BKG-')
        ->and((float) $booking->total_contract_amount)->toEqual(20800000.0)
        ->and($booking->status)->toBe('active');

    // Unit should be marked booked
    expect($unit->fresh()->status)->toBe('booked');
    expect($parking->fresh()->status)->toBe('allocated');

    // Milestones check: 1 downpayment + 12 monthly = 13 installments
    expect($booking->installments()->count())->toBe(13);
});

test('admin can record installment payment and balance recalculates', function () {
    $project = DevProject::create([
        'company_id'   => $this->company->id,
        'branch_id'    => $this->branch->id,
        'project_code' => 'PRJ-TEST-3',
        'name'         => 'Aurelia Park',
        'project_type' => 'residential',
    ]);

    $unit = DevProjectUnit::create([
        'project_id'       => $project->id,
        'unit_number'      => 'C-1',
        'floor_number'     => 1,
        'unit_type'        => '2BHK',
        'gross_area_sqft'  => 1500,
        'status'           => 'booked',
    ]);

    $booking = DevSalesBooking::create([
        'company_id'            => $this->company->id,
        'branch_id'             => $this->branch->id,
        'booking_code'          => 'BKG-TEST',
        'customer_id'           => $this->customer->id,
        'project_id'            => $project->id,
        'unit_id'               => $unit->id,
        'agreed_price_sqft'     => 10000,
        'agreed_unit_price'     => 15000000,
        'agreed_parking_price'  => 0,
        'agreed_utility_charge' => 0,
        'total_contract_amount' => 15000000,
        'downpayment_amount'    => 1500000,
        'payment_plan_type'     => 'installment',
        'total_installments'    => 10,
        'booking_date'          => '2026-11-03',
        'status'                => 'active',
    ]);

    $installment = DevBookingInstallment::create([
        'booking_id'         => $booking->id,
        'milestone_name'     => 'Booking Token & Downpayment',
        'installment_number' => 1,
        'due_date'           => '2026-11-10',
        'due_amount'         => 1500000,
        'paid_amount'        => 0,
        'balance_due'        => 1500000,
        'status'             => 'pending',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post("/erp/developer/installments/{$installment->id}/pay", [
        'paid_amount'       => 1500000,
        'paid_date'         => '2026-11-03',
        'payment_reference' => 'BRAC Bank Txn #BR-889911',
    ]);
    $response->assertSessionHasNoErrors();

    $installment->refresh();
    expect((float) $installment->paid_amount)->toEqual(1500000.0)
        ->and((float) $installment->balance_due)->toEqual(0.0)
        ->and($installment->status)->toBe('paid')
        ->and($installment->payment_reference)->toBe('BRAC Bank Txn #BR-889911');
});

test('admin can cancel sales booking and unit is restored to available', function () {
    $project = DevProject::create([
        'company_id'   => $this->company->id,
        'branch_id'    => $this->branch->id,
        'project_code' => 'PRJ-TEST-4',
        'name'         => 'Aurelia Cancel Test',
        'project_type' => 'residential',
    ]);

    $unit = DevProjectUnit::create([
        'project_id'       => $project->id,
        'unit_number'      => 'D-5',
        'floor_number'     => 5,
        'unit_type'        => '3BHK',
        'gross_area_sqft'  => 1800,
        'status'           => 'booked',
    ]);

    $booking = DevSalesBooking::create([
        'company_id'            => $this->company->id,
        'branch_id'             => $this->branch->id,
        'booking_code'          => 'BKG-CANCEL-1',
        'customer_id'           => $this->customer->id,
        'project_id'            => $project->id,
        'unit_id'               => $unit->id,
        'agreed_price_sqft'     => 10000,
        'agreed_unit_price'     => 18000000,
        'total_contract_amount' => 18000000,
        'downpayment_amount'    => 2000000,
        'payment_plan_type'     => 'installment',
        'total_installments'    => 12,
        'booking_date'          => '2026-11-03',
        'status'                => 'active',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post("/erp/developer/bookings/{$booking->id}/cancel", [
        'cancellation_reason' => 'Buyer requested cancellation due to relocation abroad.',
    ]);
    $response->assertSessionHasNoErrors();

    expect($booking->fresh()->status)->toBe('cancelled');
    expect($unit->fresh()->status)->toBe('available');
});
