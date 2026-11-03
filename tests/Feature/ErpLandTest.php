<?php

use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\LandFeasibility;
use App\Models\ERP\LandParcel;
use App\Models\ERP\LandVetting;
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
});

test('admin can view land parcels bank', function () {
    $response = $this->actingAs($this->admin, 'admin')->get('/erp/land/parcels');
    $response->assertStatus(200);
    $response->assertSee('Acquisition Register');
});

test('admin can register new land parcel with auto 8-stage vetting checklist', function () {
    $response = $this->actingAs($this->admin, 'admin')->post('/erp/land/parcels', [
        'title'                   => 'Gulshan 2 Lakeview Plot',
        'mouza'                   => 'Vatara',
        'khatian_no'              => 'RS 104',
        'dag_no'                  => '819',
        'total_area_katha'        => 10.0,
        'acquisition_type'        => 'joint_venture',
        'status'                  => 'scouting',
        'landowner_ratio'         => 45.00,
        'developer_ratio'         => 55.00,
        'estimated_land_value'    => 120000000,
        'signing_money_committed' => 15000000,
        'signing_money_paid'      => 3000000,
        'primary_contact_name'    => 'Alhaj Nazimuddin',
        'primary_contact_phone'   => '+880 1711 112233',
    ]);

    $parcel = LandParcel::where('title', 'Gulshan 2 Lakeview Plot')->first();
    expect($parcel)->not->toBeNull()
        ->and($parcel->parcel_code)->toStartWith('LND-')
        ->and($parcel->vettings()->count())->toBe(8)
        ->and($parcel->owners()->count())->toBe(1);

    $response->assertRedirect(route('erp.land-parcels.show', $parcel));
});

test('admin can add co-owner to land parcel jv matrix', function () {
    $parcel = LandParcel::create([
        'company_id'       => $this->company->id,
        'parcel_code'      => 'LND-TEST-001',
        'title'            => 'Banani Plot 12',
        'total_area_katha' => 5.0,
        'acquisition_type' => 'joint_venture',
        'status'           => 'legal_vetting',
        'landowner_ratio'  => 50.00,
        'developer_ratio'  => 50.00,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post(route('erp.land-parcels.owners.store', $parcel), [
        'name'                => 'Mrs. Farida Yasmin',
        'share_percentage'    => 50.00,
        'phone'               => '+880 1819 223344',
        'signing_money_share' => 5000000,
        'is_mou_signed'       => 1,
        'mou_deed_no'         => 'Deed-891/2026',
    ]);

    $response->assertSessionHas('success');
    expect($parcel->owners()->count())->toBe(1)
        ->and($parcel->owners()->first()->name)->toBe('Mrs. Farida Yasmin')
        ->and($parcel->owners()->first()->is_mou_signed)->toBeTrue();
});

test('admin can update legal vetting checklist stage', function () {
    $parcel = LandParcel::create([
        'company_id'       => $this->company->id,
        'parcel_code'      => 'LND-TEST-002',
        'title'            => 'Baridhara Parcel 5',
        'total_area_katha' => 8.0,
        'acquisition_type' => 'joint_venture',
        'status'           => 'legal_vetting',
    ]);

    $vetting = LandVetting::create([
        'land_parcel_id' => $parcel->id,
        'stage_title'    => 'Title Deed Verification',
        'status'         => 'pending',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->put(route('erp.land-vettings.update', $vetting), [
        'status'                => 'verified_clear',
        'lawyer_or_expert_name' => 'Advocate M. Rahman',
        'findings'              => 'Original title deed vetted and clear of mortgage or litigation.',
    ]);

    $response->assertSessionHas('success');
    expect($vetting->fresh()->status)->toBe('verified_clear')
        ->and($vetting->fresh()->verified_by_admin_id)->toBe($this->admin->id);
});

test('admin can calculate and approve feasibility study', function () {
    $parcel = LandParcel::create([
        'company_id'       => $this->company->id,
        'parcel_code'      => 'LND-TEST-003',
        'title'            => 'Dhanmondi Lakefront Parcel',
        'total_area_katha' => 10.0, // 10 * 720 = 7,200 sqft
        'acquisition_type' => 'joint_venture',
        'status'           => 'legal_vetting',
        'landowner_ratio'  => 50.00,
        'developer_ratio'  => 50.00,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post(route('erp.land-feasibility.store', $parcel), [
        'proposed_building_type'      => 'Residential',
        'num_of_basements'            => 2,
        'num_of_stories'              => 14,
        'permissible_far'             => 4.0, // GFA = 7,200 * 4 = 28,800 sqft
        'est_construction_cost_sqft'  => 3500,
        'projected_selling_rate_sqft' => 12000,
        'est_land_and_approval_cost'  => 5000000,
        'payback_period_months'       => 30,
    ]);

    $feasibility = LandFeasibility::where('land_parcel_id', $parcel->id)->first();
    expect($feasibility)->not->toBeNull()
        ->and($feasibility->feasibility_code)->toStartWith('FSB-')
        ->and((float) $feasibility->gross_floor_area_sqft)->toBe(28800.0)
        ->and((float) $feasibility->roi_percentage)->toBeGreaterThan(0);

    // Approve Feasibility
    $approveResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.land-feasibility.approve', $feasibility), [
        'approval_remarks' => 'Approved by Management.',
    ]);

    $approveResponse->assertSessionHas('success');
    expect($feasibility->fresh()->status)->toBe('approved')
        ->and($parcel->fresh()->status)->toBe('feasibility_study');
});
