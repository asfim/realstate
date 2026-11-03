<?php

use App\Models\Admin;
use App\Models\ERP\AccChartOfAccount;
use App\Models\ERP\AccJournal;
use App\Models\ERP\AgencyDeal;
use App\Models\ERP\AgencyListing;
use App\Models\ERP\AgencyTenantLease;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\ConMaterialRequisition;
use App\Models\ERP\ConVendor;
use App\Models\ERP\CostCenter;
use App\Models\ERP\CrmCustomer;
use App\Models\ERP\CrmLead;
use App\Models\ERP\Department;
use App\Models\ERP\Designation;
use App\Models\ERP\DevProject;
use App\Models\ERP\DevProjectUnit;
use App\Models\ERP\DevSalesBooking;
use App\Models\ERP\FiscalYear;
use App\Models\ERP\HrEmployee;
use App\Models\ERP\HrPayroll;
use App\Models\ERP\LandParcel;
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

    // Set active company/branch in session
    session([
        'erp_active_company_id' => $this->company->id,
        'erp_active_branch_id'  => $this->branch->id,
    ]);
});

/* =========================================================================
 * 1. ORGANIZATION MANAGEMENT CRUD
 * ========================================================================= */

test('erp company: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.companies.store'), [
        'code'              => 'AUDIT_CO',
        'legal_name'        => 'Audit Corporation Ltd',
        'trade_name'        => 'Audit Co',
        'base_currency'     => 'BDT',
        'fiscal_year_start' => 'July',
        'timezone'          => 'Asia/Dhaka',
        'status'            => 'active',
    ]);
    $createResponse->assertRedirect(route('erp.companies.index'));
    $this->assertDatabaseHas('companies', ['code' => 'AUDIT_CO']);

    $company = Company::where('code', 'AUDIT_CO')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.companies.update', $company), [
        'code'              => 'AUDIT_CO',
        'legal_name'        => 'Audit Corporation International Ltd',
        'trade_name'        => 'Audit Corp Int',
        'base_currency'     => 'BDT',
        'fiscal_year_start' => 'July',
        'timezone'          => 'Asia/Dhaka',
        'status'            => 'active',
    ]);
    $updateResponse->assertRedirect(route('erp.companies.index'));
    $this->assertDatabaseHas('companies', ['legal_name' => 'Audit Corporation International Ltd']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.companies.destroy', $company));
    $deleteResponse->assertRedirect(route('erp.companies.index'));
    $this->assertDatabaseMissing('companies', ['id' => $company->id]);
});

test('erp branch: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.branches.store'), [
        'code'           => 'BAR-AUDIT',
        'name'           => 'Barishal Audit Branch',
        'status'         => 'active',
        'is_head_office' => false,
    ]);
    $createResponse->assertRedirect(route('erp.branches.index'));
    $this->assertDatabaseHas('branches', ['code' => 'BAR-AUDIT']);

    $branch = Branch::where('code', 'BAR-AUDIT')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.branches.update', $branch), [
        'code'           => 'BAR-AUDIT',
        'name'           => 'Barishal Main Hub',
        'status'         => 'active',
        'is_head_office' => false,
    ]);
    $updateResponse->assertRedirect(route('erp.branches.index'));
    $this->assertDatabaseHas('branches', ['name' => 'Barishal Main Hub']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.branches.destroy', $branch));
    $deleteResponse->assertRedirect(route('erp.branches.index'));
    $this->assertDatabaseMissing('branches', ['id' => $branch->id]);
});

test('erp department: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.departments.store'), [
        'name'   => 'Quality Assurance & Audit',
        'code'   => 'QA',
        'status' => 'active',
    ]);
    $createResponse->assertRedirect(route('erp.departments.index'));
    $this->assertDatabaseHas('departments', ['code' => 'QA']);

    $department = Department::where('code', 'QA')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.departments.update', $department), [
        'name'   => 'Quality Assurance & Compliance',
        'code'   => 'QA-COMP',
        'status' => 'active',
    ]);
    $updateResponse->assertRedirect(route('erp.departments.index'));
    $this->assertDatabaseHas('departments', ['name' => 'Quality Assurance & Compliance']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.departments.destroy', $department));
    $deleteResponse->assertRedirect(route('erp.departments.index'));
    $this->assertDatabaseMissing('departments', ['id' => $department->id]);
});

test('erp designation: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.designations.store'), [
        'name'  => 'Senior Structural Inspector',
        'grade' => 'Grade-5',
    ]);
    $createResponse->assertRedirect(route('erp.designations.index'));
    $this->assertDatabaseHas('designations', ['name' => 'Senior Structural Inspector']);

    $designation = Designation::where('name', 'Senior Structural Inspector')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.designations.update', $designation), [
        'name'  => 'Lead Structural Inspector',
        'grade' => 'Grade-4',
    ]);
    $updateResponse->assertRedirect(route('erp.designations.index'));
    $this->assertDatabaseHas('designations', ['name' => 'Lead Structural Inspector']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.designations.destroy', $designation));
    $deleteResponse->assertRedirect(route('erp.designations.index'));
    $this->assertDatabaseMissing('designations', ['id' => $designation->id]);
});

test('erp cost center: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.cost-centers.store'), [
        'code'   => 'CC-AUDIT',
        'name'   => 'Audit Cost Center',
        'status' => 'active',
    ]);
    $createResponse->assertRedirect(route('erp.cost-centers.index'));
    $this->assertDatabaseHas('cost_centers', ['code' => 'CC-AUDIT']);

    $costCenter = CostCenter::where('code', 'CC-AUDIT')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.cost-centers.update', $costCenter), [
        'code'   => 'CC-AUDIT-2',
        'name'   => 'Audit and Review Center',
        'status' => 'active',
    ]);
    $updateResponse->assertRedirect(route('erp.cost-centers.index'));
    $this->assertDatabaseHas('cost_centers', ['name' => 'Audit and Review Center']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.cost-centers.destroy', $costCenter));
    $deleteResponse->assertRedirect(route('erp.cost-centers.index'));
    $this->assertDatabaseMissing('cost_centers', ['id' => $costCenter->id]);
});

test('erp fiscal year: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.fiscal-years.store'), [
        'name'       => 'FY 2029-2030',
        'start_date' => '2029-07-01',
        'end_date'   => '2030-06-30',
        'status'     => 'active',
        'is_current' => false,
    ]);
    $createResponse->assertRedirect(route('erp.fiscal-years.index'));
    $this->assertDatabaseHas('fiscal_years', ['name' => 'FY 2029-2030']);

    $fy = FiscalYear::where('name', 'FY 2029-2030')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.fiscal-years.update', $fy), [
        'name'       => 'FY 2029-2030 Extended',
        'start_date' => '2029-07-01',
        'end_date'   => '2030-06-30',
        'status'     => 'active',
        'is_current' => false,
    ]);
    $updateResponse->assertRedirect(route('erp.fiscal-years.index'));
    $this->assertDatabaseHas('fiscal_years', ['name' => 'FY 2029-2030 Extended']);

    // 3. DELETE
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.fiscal-years.destroy', $fy));
    $deleteResponse->assertRedirect(route('erp.fiscal-years.index'));
    $this->assertDatabaseMissing('fiscal_years', ['id' => $fy->id]);
});

/* =========================================================================
 * 2. CRM & LEADS / CUSTOMERS CRUD
 * ========================================================================= */

test('erp crm lead: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.leads.store'), [
        'name'     => 'Test Prospective Buyer',
        'phone'    => '+880 1711 001122',
        'email'    => 'buyer.test@example.com',
        'source'   => 'website_inquiry',
        'stage'    => 'new',
        'priority' => 'high',
    ]);
    $this->assertDatabaseHas('crm_leads', ['phone' => '+880 1711 001122']);

    $lead = CrmLead::where('phone', '+880 1711 001122')->first();
    $createResponse->assertRedirect(route('erp.leads.show', $lead));

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.leads.update', $lead), [
        'name'     => 'Updated Prospective Buyer',
        'phone'    => '+880 1711 001122',
        'email'    => 'buyer.updated@example.com',
        'source'   => 'referral',
        'stage'    => 'qualified',
        'priority' => 'urgent',
    ]);
    $updateResponse->assertRedirect(route('erp.leads.show', $lead));
    $this->assertDatabaseHas('crm_leads', ['name' => 'Updated Prospective Buyer', 'stage' => 'qualified']);

    // 3. DELETE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.leads.destroy', $lead));
    $deleteResponse->assertRedirect(route('erp.leads.index'));
    $this->assertSoftDeleted('crm_leads', ['id' => $lead->id]);
    expect(CrmLead::find($lead->id))->toBeNull();
});

test('erp crm customer: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.customers.store'), [
        'name'   => 'Engr. Test Customer',
        'phone'  => '+880 1912 334455',
        'email'  => 'customer.test@example.com',
        'status' => 'active',
    ]);
    $this->assertDatabaseHas('crm_customers', ['phone' => '+880 1912 334455']);

    $customer = CrmCustomer::where('phone', '+880 1912 334455')->first();
    $createResponse->assertRedirect(route('erp.customers.show', $customer));

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.customers.update', $customer), [
        'name'   => 'Engr. Test Customer CIP',
        'phone'  => '+880 1912 334455',
        'email'  => 'customer.cip@example.com',
        'status' => 'active',
    ]);
    $updateResponse->assertRedirect(route('erp.customers.show', $customer));
    $this->assertDatabaseHas('crm_customers', ['name' => 'Engr. Test Customer CIP']);

    // 3. DELETE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.customers.destroy', $customer));
    $deleteResponse->assertRedirect(route('erp.customers.index'));
    $this->assertSoftDeleted('crm_customers', ['id' => $customer->id]);
    expect(CrmCustomer::find($customer->id))->toBeNull();
});

/* =========================================================================
 * 3. LAND ACQUISITION & JOINT VENTURE CRUD
 * ========================================================================= */

test('erp land parcel: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.land-parcels.store'), [
        'title'            => 'Purbachal Sector 18 Commercial Plot',
        'mouza'            => 'Purbachal Mouza',
        'dag_no'           => 'CS-4421, RS-8891',
        'total_area_katha' => 20.00,
        'acquisition_type' => 'outright_purchase',
        'status'           => 'under_vetting',
    ]);
    $this->assertDatabaseHas('land_parcels', ['title' => 'Purbachal Sector 18 Commercial Plot']);

    $parcel = LandParcel::where('title', 'Purbachal Sector 18 Commercial Plot')->first();
    $createResponse->assertRedirect(route('erp.land-parcels.show', $parcel));

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.land-parcels.update', $parcel), [
        'title'            => 'Purbachal Sector 18 Prime Plot',
        'mouza'            => 'Purbachal Mouza',
        'dag_no'           => 'CS-4421, RS-8891',
        'total_area_katha' => 22.50,
        'acquisition_type' => 'outright_purchase',
        'status'           => 'ready_for_design',
    ]);
    $updateResponse->assertRedirect(route('erp.land-parcels.show', $parcel));
    $this->assertDatabaseHas('land_parcels', ['title' => 'Purbachal Sector 18 Prime Plot', 'status' => 'ready_for_design']);

    // 3. DELETE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.land-parcels.destroy', $parcel));
    $deleteResponse->assertRedirect(route('erp.land-parcels.index'));
    $this->assertSoftDeleted('land_parcels', ['id' => $parcel->id]);
    expect(LandParcel::find($parcel->id))->toBeNull();
});

/* =========================================================================
 * 4. DEVELOPER SALES & PROJECTS CRUD
 * ========================================================================= */

test('erp dev project: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.dev-projects.store'), [
        'name'         => 'Audit Test Residence',
        'project_type' => 'residential',
        'location'     => 'Gulshan 1, Dhaka',
        'total_floors' => 12,
        'total_units'  => 24,
        'status'       => 'planning',
    ]);
    $this->assertDatabaseHas('dev_projects', ['name' => 'Audit Test Residence']);

    $project = DevProject::where('name', 'Audit Test Residence')->first();
    $createResponse->assertRedirect(route('erp.dev-projects.show', $project));

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.dev-projects.update', $project), [
        'name'            => 'Audit Luxury Residence',
        'project_type'    => 'residential',
        'location'        => 'Gulshan 1, Dhaka',
        'total_floors'    => 14,
        'total_basements' => 1,
        'total_units'     => 28,
        'status'          => 'under_construction',
    ]);
    $updateResponse->assertRedirect(route('erp.dev-projects.show', $project));
    $this->assertDatabaseHas('dev_projects', ['name' => 'Audit Luxury Residence', 'total_floors' => 14]);

    // 3. DELETE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.dev-projects.destroy', $project));
    $deleteResponse->assertRedirect(route('erp.dev-projects.index'));
    $this->assertSoftDeleted('dev_projects', ['id' => $project->id]);
    expect(DevProject::find($project->id))->toBeNull();
});

test('erp dev unit: create, update, and delete work properly', function () {
    $project = DevProject::create([
        'company_id'   => $this->company->id,
        'branch_id'    => $this->branch->id,
        'project_code' => 'PRJ-UNIT-TEST',
        'name'         => 'Unit Test Residence',
        'project_type' => 'residential',
        'location'     => 'Dhanmondi, Dhaka',
        'total_floors' => 8,
        'total_units'  => 16,
        'status'       => 'planning',
    ]);

    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.dev-units.store', $project), [
        'unit_number'      => 'B-3',
        'floor_label'      => '3rd Floor',
        'unit_type'        => '3BHK',
        'allocation_share' => 'developer_share',
        'gross_area_sqft'  => 1800,
        'base_price_sqft'  => 12000,
        'status'           => 'available',
    ]);
    $createResponse->assertSessionHasNoErrors();
    $this->assertDatabaseHas('dev_project_units', ['project_id' => $project->id, 'unit_number' => 'B-3']);

    $unit = DevProjectUnit::where('project_id', $project->id)->where('unit_number', 'B-3')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.dev-units.update', $unit), [
        'unit_number'      => 'B-3',
        'floor_label'      => '3rd Floor',
        'unit_type'        => '3BHK Superior',
        'allocation_share' => 'developer_share',
        'gross_area_sqft'  => 1950,
        'base_price_sqft'  => 12500,
        'status'           => 'available',
    ]);
    $updateResponse->assertSessionHasNoErrors();
    $this->assertDatabaseHas('dev_project_units', ['id' => $unit->id, 'gross_area_sqft' => 1950]);

    // 3. DELETE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.dev-units.destroy', $unit));
    $deleteResponse->assertSessionHasNoErrors();
    $this->assertSoftDeleted('dev_project_units', ['id' => $unit->id]);
    expect(DevProjectUnit::find($unit->id))->toBeNull();

    $project->forceDelete();
});

test('erp dev sales booking: create, update, and delete work properly', function () {
    $project = DevProject::create([
        'company_id'   => $this->company->id,
        'branch_id'    => $this->branch->id,
        'project_code' => 'PRJ-BK-TEST',
        'name'         => 'Booking Test Tower',
        'project_type' => 'residential',
        'location'     => 'Banani, Dhaka',
        'total_floors' => 10,
        'total_units'  => 10,
        'status'       => 'under_construction',
    ]);

    $unit = DevProjectUnit::create([
        'project_id'        => $project->id,
        'unit_number'       => 'C-1',
        'floor_label'       => '1st Floor',
        'unit_type'         => '3BHK',
        'allocation_share'  => 'developer_share',
        'gross_area_sqft'   => 1500,
        'base_price_sqft'   => 10000,
        'total_unit_price'  => 15000000,
        'grand_total_price' => 15000000,
        'status'            => 'available',
    ]);

    $customer = CrmCustomer::create([
        'company_id'    => $this->company->id,
        'branch_id'     => $this->branch->id,
        'customer_code' => 'CUST-BK-01',
        'name'          => 'Booking Customer',
        'phone'         => '+880 1811 002233',
        'status'        => 'active',
    ]);

    // 1. CREATE BOOKING
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.dev-bookings.store'), [
        'project_id'           => $project->id,
        'customer_id'          => $customer->id,
        'unit_id'              => $unit->id,
        'booking_date'         => '2026-03-01',
        'agreed_price_sqft'    => 10000,
        'downpayment_amount'   => 3000000,
        'downpayment_due_date' => '2026-03-15',
        'payment_plan_type'    => 'construction_linked',
        'total_installments'   => 12,
    ]);
    $this->assertDatabaseHas('dev_sales_bookings', ['unit_id' => $unit->id, 'customer_id' => $customer->id]);

    $booking = DevSalesBooking::where('unit_id', $unit->id)->first();
    $createResponse->assertRedirect(route('erp.dev-bookings.show', $booking));

    // 2. UPDATE BOOKING
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.dev-bookings.update', $booking), [
        'special_conditions' => 'Updated negotiation agreement',
        'notes'              => 'First review confirmed',
    ]);
    $updateResponse->assertRedirect(route('erp.dev-bookings.show', $booking));
    $this->assertDatabaseHas('dev_sales_bookings', ['id' => $booking->id, 'special_conditions' => 'Updated negotiation agreement']);

    // Cancel first so destroy guard passes
    $booking->update(['status' => 'cancelled']);

    // 3. DELETE BOOKING (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.dev-bookings.destroy', $booking));
    $deleteResponse->assertRedirect(route('erp.dev-bookings.index'));
    $this->assertSoftDeleted('dev_sales_bookings', ['id' => $booking->id]);
    expect(DevSalesBooking::find($booking->id))->toBeNull();

    $unit->forceDelete();
    $customer->forceDelete();
    $project->forceDelete();
});

/* =========================================================================
 * 5. AGENCY / BROKERAGE CRUD
 * ========================================================================= */

test('erp agency listing: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.agency-listings.store'), [
        'title'                   => 'Audit Listing Penthouse',
        'listing_type'            => 'resale',
        'property_category'       => 'apartment',
        'location'                => 'Gulshan 2, Dhaka',
        'size_sqft'               => 3200,
        'landlord_seller_name'    => 'Mr. Landlord',
        'landlord_seller_phone'   => '+880 1711 776655',
        'mandate_type'            => 'exclusive',
        'asking_price'            => 45000000,
        'commission_rate_percent' => 2.00,
        'status'                  => 'active',
    ]);
    $this->assertDatabaseHas('agency_listings', ['title' => 'Audit Listing Penthouse']);

    $listing = AgencyListing::where('title', 'Audit Listing Penthouse')->first();
    $createResponse->assertRedirect(route('erp.agency-listings.show', $listing));

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.agency-listings.update', $listing), [
        'title'                   => 'Audit Luxury Penthouse with Terrace',
        'listing_type'            => 'resale',
        'property_category'       => 'apartment',
        'location'                => 'Gulshan 2, Dhaka',
        'size_sqft'               => 3500,
        'landlord_seller_name'    => 'Mr. Landlord',
        'landlord_seller_phone'   => '+880 1711 776655',
        'mandate_type'            => 'exclusive',
        'asking_price'            => 48000000,
        'commission_rate_percent' => 2.00,
        'status'                  => 'active',
    ]);
    $updateResponse->assertRedirect(route('erp.agency-listings.show', $listing));
    $this->assertDatabaseHas('agency_listings', ['title' => 'Audit Luxury Penthouse with Terrace', 'asking_price' => 48000000]);

    // 3. DELETE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.agency-listings.destroy', $listing));
    $deleteResponse->assertRedirect(route('erp.agency-listings.index'));
    $this->assertSoftDeleted('agency_listings', ['id' => $listing->id]);
    expect(AgencyListing::find($listing->id))->toBeNull();
});

test('erp agency deal: create, update, and delete work properly', function () {
    $listing = AgencyListing::create([
        'company_id'              => $this->company->id,
        'branch_id'               => $this->branch->id,
        'listing_code'            => 'LST-DEAL-AUDIT',
        'title'                   => 'Deal Audit Listing Flat',
        'listing_type'            => 'resale',
        'property_category'       => 'apartment',
        'location'                => 'Baridhara, Dhaka',
        'landlord_seller_name'    => 'Seller Ahmed',
        'landlord_seller_phone'   => '+880 1711 334411',
        'asking_price'            => 25000000,
        'commission_rate_percent' => 2.00,
        'expected_commission'     => 500000,
        'status'                  => 'active',
    ]);

    $customer = CrmCustomer::create([
        'company_id'    => $this->company->id,
        'branch_id'     => $this->branch->id,
        'customer_code' => 'CUST-DEAL-AUDIT',
        'name'          => 'Deal Buyer',
        'phone'         => '+880 1811 556677',
        'status'        => 'active',
    ]);

    // 1. CREATE DEAL
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.agency-deals.store'), [
        'listing_id'                => $listing->id,
        'customer_id'               => $customer->id,
        'deal_type'                 => 'resale',
        'closing_price'             => 24500000,
        'company_commission_amount' => 490000,
        'deal_date'                 => '2026-03-01',
    ]);
    $this->assertDatabaseHas('agency_deals', ['listing_id' => $listing->id, 'customer_id' => $customer->id]);

    $deal = AgencyDeal::where('listing_id', $listing->id)->first();
    $createResponse->assertRedirect(route('erp.agency-deals.show', $deal));

    // 2. UPDATE DEAL
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.agency-deals.update', $deal), [
        'closing_price'             => 24800000,
        'company_commission_amount' => 496000,
        'deal_date'                 => '2026-03-02',
        'status'                    => 'in_progress',
        'notes'                     => 'Closing revised',
    ]);
    $updateResponse->assertRedirect(route('erp.agency-deals.show', $deal));
    $this->assertDatabaseHas('agency_deals', ['id' => $deal->id, 'closing_price' => 24800000]);

    // 3. DELETE DEAL (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.agency-deals.destroy', $deal));
    $deleteResponse->assertRedirect(route('erp.agency-deals.index'));
    $this->assertSoftDeleted('agency_deals', ['id' => $deal->id]);
    expect(AgencyDeal::find($deal->id))->toBeNull();

    $customer->forceDelete();
    $listing->forceDelete();
});

test('erp agency lease: create, update, and delete work properly', function () {
    $listing = AgencyListing::create([
        'company_id'              => $this->company->id,
        'branch_id'               => $this->branch->id,
        'listing_code'            => 'LST-LEASE-AUDIT',
        'title'                   => 'Rental Audit Commercial Space',
        'listing_type'            => 'rental',
        'property_category'       => 'commercial',
        'location'                => 'Mohakhali, Dhaka',
        'landlord_seller_name'    => 'Landlord Chowdhury',
        'landlord_seller_phone'   => '+880 1711 889900',
        'asking_price'            => 120000,
        'commission_rate_percent' => 100.00,
        'expected_commission'     => 120000,
        'status'                  => 'active',
    ]);

    $tenant = CrmCustomer::create([
        'company_id'    => $this->company->id,
        'branch_id'     => $this->branch->id,
        'customer_code' => 'CUST-LEASE-AUDIT',
        'name'          => 'Corporate Tenant Ltd',
        'phone'         => '+880 1911 332211',
        'status'        => 'active',
    ]);

    // 1. CREATE LEASE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.agency-leases.store'), [
        'listing_id'              => $listing->id,
        'tenant_id'               => $tenant->id,
        'monthly_rent'            => 120000,
        'security_deposit_amount' => 360000,
        'lease_start_date'        => '2026-04-01',
        'lease_end_date'          => '2028-03-31',
        'rent_due_day'            => 5,
        'commission_amount'       => 120000,
    ]);
    $this->assertDatabaseHas('agency_tenant_leases', ['listing_id' => $listing->id, 'tenant_id' => $tenant->id]);

    $lease = AgencyTenantLease::where('listing_id', $listing->id)->first();
    $createResponse->assertRedirect(route('erp.agency-leases.show', $lease));

    // 2. UPDATE LEASE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.agency-leases.update', $lease), [
        'monthly_rent'            => 125000,
        'security_deposit_amount' => 375000,
        'lease_start_date'        => '2026-04-01',
        'lease_end_date'          => '2028-03-31',
        'rent_due_day'            => 7,
        'status'                  => 'active',
        'special_terms'           => 'Updated maintenance clause',
    ]);
    $updateResponse->assertRedirect(route('erp.agency-leases.show', $lease));
    $this->assertDatabaseHas('agency_tenant_leases', ['id' => $lease->id, 'monthly_rent' => 125000]);

    // 3. DELETE LEASE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.agency-leases.destroy', $lease));
    $deleteResponse->assertRedirect(route('erp.agency-leases.index'));
    $this->assertSoftDeleted('agency_tenant_leases', ['id' => $lease->id]);
    expect(AgencyTenantLease::find($lease->id))->toBeNull();

    $tenant->forceDelete();
    $listing->forceDelete();
});

/* =========================================================================
 * 6. CONSTRUCTION & PROCUREMENT CRUD
 * ========================================================================= */

test('erp construction vendor: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.con-vendors.store'), [
        'name'           => 'Audit Steel & Cement Traders',
        'contact_person' => 'Md. Faruk',
        'phone'          => '+880 1711 112233',
        'status'         => 'active',
    ]);
    $this->assertDatabaseHas('con_vendors', ['phone' => '+880 1711 112233']);

    $vendor = ConVendor::where('phone', '+880 1711 112233')->first();
    $createResponse->assertRedirect(route('erp.con-vendors.index'));

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.con-vendors.update', $vendor), [
        'name'           => 'Audit Premier Steel & Cement',
        'contact_person' => 'Md. Faruk Ahmed',
        'phone'          => '+880 1711 112233',
        'status'         => 'active',
    ]);
    $updateResponse->assertRedirect(route('erp.con-vendors.index'));
    $this->assertDatabaseHas('con_vendors', ['name' => 'Audit Premier Steel & Cement']);

    // 3. DELETE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.con-vendors.destroy', $vendor));
    $deleteResponse->assertRedirect(route('erp.con-vendors.index'));
    $this->assertSoftDeleted('con_vendors', ['id' => $vendor->id]);
    expect(ConVendor::find($vendor->id))->toBeNull();
});

test('erp material requisition: create, update, and delete work properly', function () {
    $project = DevProject::create([
        'company_id'   => $this->company->id,
        'branch_id'    => $this->branch->id,
        'project_code' => 'PRJ-REQ-AUDIT',
        'name'         => 'Requisition Audit Tower',
        'project_type' => 'residential',
        'location'     => 'Mirpur, Dhaka',
        'total_floors' => 10,
        'total_units'  => 10,
        'status'       => 'under_construction',
    ]);

    // 1. CREATE REQUISITION
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.con-requisitions.store'), [
        'project_id'    => $project->id,
        'required_date' => '2026-04-15',
        'urgency'       => 'medium',
        'items'         => [
            [
                'material_name'   => 'BSRM 16mm Deformed Rods',
                'unit_of_measure' => 'MT',
                'requested_qty'   => 25.5,
                'remarks'         => 'Foundation column pouring',
            ],
        ],
    ]);
    $this->assertDatabaseHas('con_material_requisitions', ['project_id' => $project->id]);

    $req = ConMaterialRequisition::where('project_id', $project->id)->first();
    $createResponse->assertRedirect(route('erp.con-requisitions.show', $req));

    // 2. UPDATE REQUISITION
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.con-requisitions.update', $req), [
        'required_date'       => '2026-04-20',
        'urgency'             => 'high',
        'site_location_notes' => 'Mirpur Site Section B',
    ]);
    $updateResponse->assertRedirect(route('erp.con-requisitions.show', $req));
    $this->assertDatabaseHas('con_material_requisitions', ['id' => $req->id, 'urgency' => 'high']);

    // 3. DELETE REQUISITION (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.con-requisitions.destroy', $req));
    $deleteResponse->assertRedirect(route('erp.con-requisitions.index'));
    $this->assertSoftDeleted('con_material_requisitions', ['id' => $req->id]);
    expect(ConMaterialRequisition::find($req->id))->toBeNull();

    $project->forceDelete();
});

/* =========================================================================
 * 7. ACCOUNTS & DOUBLE-ENTRY JOURNAL CRUD
 * ========================================================================= */

test('erp chart of accounts: create, update, and delete work properly', function () {
    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.acc-coa.store'), [
        'account_code'   => '1099',
        'account_name'   => 'Prepaid Audit Security Deposits',
        'account_type'   => 'asset',
        'normal_balance' => 'debit',
        'description'    => 'Temporary security deposit holding',
    ]);
    $createResponse->assertSessionHasNoErrors();
    $this->assertDatabaseHas('acc_chart_of_accounts', ['account_code' => '1099']);

    $account = AccChartOfAccount::where('account_code', '1099')->first();

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.acc-coa.update', $account), [
        'account_name'   => 'Prepaid Security & Utility Deposits',
        'account_type'   => 'asset',
        'normal_balance' => 'debit',
        'description'    => 'Security deposit and utility deposit holding',
    ]);
    $updateResponse->assertSessionHasNoErrors();
    $this->assertDatabaseHas('acc_chart_of_accounts', ['id' => $account->id, 'account_name' => 'Prepaid Security & Utility Deposits']);

    // 3. DELETE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.acc-coa.destroy', $account));
    $deleteResponse->assertSessionHasNoErrors();
    $this->assertSoftDeleted('acc_chart_of_accounts', ['id' => $account->id]);
    expect(AccChartOfAccount::find($account->id))->toBeNull();
});

test('erp journal voucher: create, show, and delete work properly', function () {
    $debitAcc = AccChartOfAccount::firstOrCreate(
        ['company_id' => $this->company->id, 'account_code' => '5999'],
        ['account_name' => 'General Audit Expense', 'account_type' => 'expense', 'normal_balance' => 'debit']
    );

    $creditAcc = AccChartOfAccount::firstOrCreate(
        ['company_id' => $this->company->id, 'account_code' => '1001'],
        ['account_name' => 'Petty Cash In Hand', 'account_type' => 'asset', 'normal_balance' => 'debit']
    );

    // 1. CREATE JOURNAL
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.acc-journals.store'), [
        'voucher_type' => 'JV',
        'entry_date'   => now()->format('Y-m-d'),
        'narration'    => 'Audit journal verification entry',
        'lines'        => [
            [
                'account_id' => $debitAcc->id,
                'debit'      => 5000,
                'credit'     => 0,
            ],
            [
                'account_id' => $creditAcc->id,
                'debit'      => 0,
                'credit'     => 5000,
            ],
        ],
    ]);
    $this->assertDatabaseHas('acc_journals', ['narration' => 'Audit journal verification entry']);

    $journal = AccJournal::where('narration', 'Audit journal verification entry')->first();
    $createResponse->assertRedirect(route('erp.acc-journals.show', $journal));

    // 2. SHOW
    $showResponse = $this->actingAs($this->admin, 'admin')->get(route('erp.acc-journals.show', $journal));
    $showResponse->assertStatus(200);
    $showResponse->assertSee($journal->voucher_code);

    // 3. DELETE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.acc-journals.destroy', $journal));
    $deleteResponse->assertRedirect(route('erp.acc-journals.index'));
    $this->assertSoftDeleted('acc_journals', ['id' => $journal->id]);
    expect(AccJournal::find($journal->id))->toBeNull();
});

/* =========================================================================
 * 8. HR & PAYROLL CRUD
 * ========================================================================= */

test('erp hr employee: create, update, and delete work properly', function () {
    $dept = Department::firstOrCreate(
        ['company_id' => $this->company->id, 'code' => 'ENG'],
        ['name' => 'Engineering Department', 'status' => 'active']
    );

    $desig = Designation::firstOrCreate(
        ['company_id' => $this->company->id, 'name' => 'Site Supervisor'],
        ['department_id' => $dept->id]
    );

    // 1. CREATE
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.hr-employees.store'), [
        'name'            => 'Audit Site Officer',
        'phone'           => '+880 1712 998877',
        'email'           => 'officer.audit@example.com',
        'joining_date'    => '2026-01-01',
        'employment_type' => 'permanent',
        'department_id'   => $dept->id,
        'designation_id'  => $desig->id,
        'basic_salary'    => 35000,
        'status'          => 'active',
    ]);
    $this->assertDatabaseHas('hr_employees', ['phone' => '+880 1712 998877']);

    $employee = HrEmployee::where('phone', '+880 1712 998877')->first();
    $createResponse->assertRedirect(route('erp.hr-employees.show', $employee));

    // 2. UPDATE
    $updateResponse = $this->actingAs($this->admin, 'admin')->put(route('erp.hr-employees.update', $employee), [
        'name'            => 'Audit Lead Site Officer',
        'phone'           => '+880 1712 998877',
        'email'           => 'lead.audit@example.com',
        'joining_date'    => '2026-01-01',
        'employment_type' => 'permanent',
        'department_id'   => $dept->id,
        'designation_id'  => $desig->id,
        'basic_salary'    => 40000,
        'status'          => 'active',
    ]);
    $updateResponse->assertRedirect(route('erp.hr-employees.show', $employee));
    $this->assertDatabaseHas('hr_employees', ['id' => $employee->id, 'name' => 'Audit Lead Site Officer', 'basic_salary' => 40000]);

    // 3. DELETE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.hr-employees.destroy', $employee));
    $deleteResponse->assertRedirect(route('erp.hr-employees.index'));
    $this->assertSoftDeleted('hr_employees', ['id' => $employee->id]);
    expect(HrEmployee::find($employee->id))->toBeNull();
});

test('erp hr payroll: create, show, and delete work properly', function () {
    $employee = HrEmployee::create([
        'company_id'      => $this->company->id,
        'branch_id'       => $this->branch->id,
        'employee_code'   => 'EMP-AUDIT-PR',
        'name'            => 'Payroll Audit Employee',
        'phone'           => '+880 1819 778899',
        'joining_date'    => '2026-01-01',
        'employment_type' => 'permanent',
        'basic_salary'    => 30000,
        'gross_salary'    => 30000,
        'status'          => 'active',
    ]);

    // 1. CREATE PAYROLL
    $createResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.hr-payrolls.store'), [
        'employee_id'  => $employee->id,
        'salary_month' => 9,
        'salary_year'  => 2026,
        'bonus'        => 5000,
        'deductions'   => 1000,
    ]);
    $this->assertDatabaseHas('hr_payrolls', ['employee_id' => $employee->id, 'salary_month' => 9, 'salary_year' => 2026]);

    $payroll = HrPayroll::where('employee_id', $employee->id)->first();
    $createResponse->assertRedirect(route('erp.hr-payrolls.show', $payroll));

    // 2. SHOW
    $showResponse = $this->actingAs($this->admin, 'admin')->get(route('erp.hr-payrolls.show', $payroll));
    $showResponse->assertStatus(200);
    $showResponse->assertSee($payroll->payroll_code);

    // 3. DELETE (Soft Delete)
    $deleteResponse = $this->actingAs($this->admin, 'admin')->delete(route('erp.hr-payrolls.destroy', $payroll));
    $deleteResponse->assertRedirect(route('erp.hr-payrolls.index'));
    $this->assertSoftDeleted('hr_payrolls', ['id' => $payroll->id]);
    expect(HrPayroll::find($payroll->id))->toBeNull();

    $employee->forceDelete();
});
