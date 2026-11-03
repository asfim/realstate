<?php

use App\Models\Admin;
use App\Models\ERP\AgencyDeal;
use App\Models\ERP\AgencyListing;
use App\Models\ERP\AgencyTenantLease;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\CrmCustomer;
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
        'customer_code' => 'CUST-AG-01',
        'name'          => 'Mr. Agency Buyer',
        'phone'         => '+880 1711 998877',
        'email'         => 'buyer.agency@example.com',
        'status'        => 'active',
    ]);
});

test('admin can view agency listings, deals, and leases index', function () {
    $r1 = $this->actingAs($this->admin, 'admin')->get('/erp/agency/listings');
    $r1->assertStatus(200);
    $r1->assertSee('Brokerage Listings');

    $r2 = $this->actingAs($this->admin, 'admin')->get('/erp/agency/deals');
    $r2->assertStatus(200);
    $r2->assertSee('Brokerage Closed Deals Register');

    $r3 = $this->actingAs($this->admin, 'admin')->get('/erp/agency/leases');
    $r3->assertStatus(200);
    $r3->assertSee('Tenant Leases');
});

test('admin can register a new third-party agency listing mandate with auto code', function () {
    $response = $this->actingAs($this->admin, 'admin')->post('/erp/agency/listings', [
        'title'                   => 'Gulshan 2 Luxury 3BHK Resale Flat',
        'listing_type'            => 'resale',
        'property_category'       => 'apartment',
        'location'                => 'Road 59, Gulshan 2, Dhaka',
        'address'                 => 'House 14, Flat 6B',
        'size_sqft'               => 2650,
        'bedrooms'                => 3,
        'bathrooms'               => 4,
        'floor_number'            => 6,
        'landlord_seller_name'    => 'Engr. Mahbubur Rahman',
        'landlord_seller_phone'   => '+880 1711 445566',
        'landlord_seller_email'   => 'mahbub@example.com',
        'mandate_type'            => 'exclusive',
        'mandate_expiry_date'     => '2027-06-30',
        'asking_price'            => 38000000,
        'reserve_price'           => 36000000,
        'commission_rate_percent' => 2.00,
        'key_held_at_branch'      => 1,
        'status'                  => 'active',
        'assigned_agent_id'       => $this->admin->id,
    ]);

    $response->assertSessionHasNoErrors();

    $listing = AgencyListing::where('title', 'Gulshan 2 Luxury 3BHK Resale Flat')->first();
    expect($listing)->not->toBeNull()
        ->and($listing->listing_code)->toStartWith('LST-')
        ->and((float) $listing->asking_price)->toEqual(38000000.0)
        ->and((float) $listing->expected_commission)->toEqual(760000.0) // 2% of 38,000,000
        ->and($listing->key_held_at_branch)->toBeTrue();
});

test('admin can close a brokerage deal on a listing', function () {
    $listing = AgencyListing::create([
        'company_id'              => $this->company->id,
        'branch_id'               => $this->branch->id,
        'listing_code'            => 'LST-TEST-01',
        'title'                   => 'Banani Commercial Floor 2500 sft',
        'listing_type'            => 'resale',
        'property_category'       => 'commercial',
        'location'                => 'Banani 11',
        'landlord_seller_name'    => 'Mr. Seller Khan',
        'landlord_seller_phone'   => '+880 1911 223344',
        'asking_price'            => 30000000,
        'commission_rate_percent' => 2.00,
        'expected_commission'     => 600000,
        'status'                  => 'active',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post('/erp/agency/deals', [
        'listing_id'                         => $listing->id,
        'customer_id'                        => $this->customer->id,
        'agent_id'                           => $this->admin->id,
        'deal_type'                          => 'resale',
        'closing_price'                      => 29000000,
        'company_commission_amount'          => 580000,
        'agent_commission_percent'           => 25.00,
        'external_broker_name'               => 'Co-Broker Express',
        'external_broker_phone'              => '+880 1611 000000',
        'external_broker_commission_amount'  => 50000,
        'deal_date'                          => '2026-11-03',
        'notes'                              => 'Closed at 2.9 Crore',
    ]);

    $response->assertSessionHasNoErrors();

    $deal = AgencyDeal::where('listing_id', $listing->id)->first();
    expect($deal)->not->toBeNull()
        ->and($deal->deal_code)->toStartWith('DEA-')
        ->and((float) $deal->closing_price)->toEqual(29000000.0)
        ->and((float) $deal->company_commission_amount)->toEqual(580000.0)
        ->and((float) $deal->agent_commission_amount)->toEqual(145000.0) // 25% of 580,000
        ->and((float) $deal->external_broker_commission_amount)->toEqual(50000.0)
        ->and($deal->status)->toBe('pending_settlement');

    // Listing should be marked as closed_deal
    expect($listing->fresh()->status)->toBe('closed_deal');
});

test('admin can settle a brokerage deal', function () {
    $listing = AgencyListing::create([
        'company_id'              => $this->company->id,
        'branch_id'               => $this->branch->id,
        'listing_code'            => 'LST-TEST-02',
        'title'                   => 'Dhanmondi Plot 5 Katha',
        'listing_type'            => 'land_sale',
        'property_category'       => 'land',
        'location'                => 'Dhanmondi 9A',
        'landlord_seller_name'    => 'Mrs. Farida',
        'landlord_seller_phone'   => '+880 1711 334455',
        'asking_price'            => 50000000,
        'status'                  => 'closed_deal',
    ]);

    $deal = AgencyDeal::create([
        'company_id'                => $this->company->id,
        'branch_id'                 => $this->branch->id,
        'deal_code'                 => 'DEA-TEST-01',
        'listing_id'                => $listing->id,
        'customer_id'               => $this->customer->id,
        'agent_id'                  => $this->admin->id,
        'deal_type'                 => 'resale',
        'closing_price'             => 50000000,
        'company_commission_amount' => 1000000,
        'deal_date'                 => '2026-11-03',
        'status'                    => 'pending_settlement',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post("/erp/agency/deals/{$deal->id}/settle");
    $response->assertSessionHasNoErrors();

    expect($deal->fresh()->status)->toBe('settled');
});

test('admin can issue a rental lease agreement for a rental listing', function () {
    $listing = AgencyListing::create([
        'company_id'              => $this->company->id,
        'branch_id'               => $this->branch->id,
        'listing_code'            => 'LST-RENT-01',
        'title'                   => 'Baridhara Diplomatic Zone Penthouse',
        'listing_type'            => 'rental',
        'property_category'       => 'apartment',
        'location'                => 'Baridhara, Dhaka',
        'landlord_seller_name'    => 'Ambassador Hossain',
        'landlord_seller_phone'   => '+880 1711 778899',
        'asking_price'            => 180000,
        'status'                  => 'active',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post('/erp/agency/leases', [
        'listing_id'              => $listing->id,
        'tenant_id'               => $this->customer->id,
        'monthly_rent'            => 175000,
        'security_deposit_amount' => 350000, // 2 months
        'monthly_service_charge'  => 15000,
        'lease_start_date'        => '2026-12-01',
        'lease_end_date'          => '2027-11-30',
        'rent_due_day'            => 7,
        'key_handover_date'       => '2026-11-28',
        'special_terms'           => 'Diplomatic standard maintenance included.',
    ]);

    $response->assertSessionHasNoErrors();

    $lease = AgencyTenantLease::where('listing_id', $listing->id)->first();
    expect($lease)->not->toBeNull()
        ->and($lease->lease_code)->toStartWith('LSE-')
        ->and((float) $lease->monthly_rent)->toEqual(175000.0)
        ->and((float) $lease->security_deposit_amount)->toEqual(350000.0)
        ->and($lease->rent_due_day)->toBe(7)
        ->and($lease->status)->toBe('active');

    expect($listing->fresh()->status)->toBe('closed_deal');
});

test('admin can terminate a lease and restore listing status to active', function () {
    $listing = AgencyListing::create([
        'company_id'              => $this->company->id,
        'branch_id'               => $this->branch->id,
        'listing_code'            => 'LST-RENT-02',
        'title'                   => 'Uttara Sector 3 Duplex',
        'listing_type'            => 'rental',
        'property_category'       => 'house',
        'location'                => 'Uttara Sector 3',
        'landlord_seller_name'    => 'Mr. Alam',
        'landlord_seller_phone'   => '+880 1819 112233',
        'asking_price'            => 90000,
        'status'                  => 'closed_deal',
    ]);

    $lease = AgencyTenantLease::create([
        'company_id'              => $this->company->id,
        'branch_id'               => $this->branch->id,
        'lease_code'              => 'LSE-TEST-02',
        'listing_id'              => $listing->id,
        'tenant_id'               => $this->customer->id,
        'monthly_rent'            => 90000,
        'security_deposit_amount' => 180000,
        'lease_start_date'        => '2026-01-01',
        'lease_end_date'          => '2026-12-31',
        'rent_due_day'            => 5,
        'status'                  => 'active',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post("/erp/agency/leases/{$lease->id}/terminate");
    $response->assertSessionHasNoErrors();

    expect($lease->fresh()->status)->toBe('terminated');
    expect($listing->fresh()->status)->toBe('active');
});
