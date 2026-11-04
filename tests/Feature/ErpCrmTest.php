<?php

use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\CrmCustomer;
use App\Models\ERP\CrmLead;
use App\Models\ERP\CrmSiteVisit;
use App\Models\Submission;
use App\Services\ERP\LeadRoutingService;
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

test('admin can view crm leads pipeline', function () {
    $response = $this->actingAs($this->admin, 'admin')->get('/erp/crm/leads');
    $response->assertStatus(200);
    $response->assertSee('Lead Pipeline');
});

test('admin can create a new lead and calculates initial score', function () {
    $response = $this->actingAs($this->admin, 'admin')->post('/erp/crm/leads', [
        'name'                   => 'Tariqul Islam',
        'phone'                  => '+880 1711 999888',
        'email'                  => 'tariqul@example.com',
        'source'                 => 'walk_in',
        'stage'                  => 'new',
        'priority'               => 'high',
        'min_budget'             => 15000000,
        'max_budget'             => 25000000,
        'preferred_location'     => 'Gulshan 2',
        'property_type_interest' => 'Apartment',
    ]);

    $lead = CrmLead::where('phone', '+880 1711 999888')->first();
    expect($lead)->not->toBeNull()
        ->and($lead->lead_code)->toStartWith('LED-')
        ->and($lead->score)->toBeGreaterThan(0);

    $response->assertRedirect(route('erp.leads.show', $lead));
});

test('admin can log activity and update lead stage', function () {
    $lead = CrmLead::create([
        'company_id' => $this->company->id,
        'lead_code'  => 'LED-TEST-001',
        'name'       => 'Nazmul Hasan',
        'phone'      => '+880 1912 345678',
        'source'     => 'phone_call',
        'stage'      => 'new',
        'priority'   => 'medium',
        'score'      => 30,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post(route('erp.leads.activities.store', $lead), [
        'type'          => 'call',
        'subject'       => 'Initial Consultation Call',
        'description'   => 'Buyer is looking for ready-to-move apartment in Banani.',
        'activity_date' => now()->format('Y-m-d H:i:s'),
        'outcome'       => 'interested',
    ]);

    $response->assertSessionHas('success');
    expect($lead->activities()->count())->toBe(1);

    // Update Stage
    $stageResponse = $this->actingAs($this->admin, 'admin')->post(route('erp.leads.update-stage', $lead), [
        'stage' => 'contacted',
    ]);

    $stageResponse->assertSessionHas('success');
    expect($lead->fresh()->stage)->toBe('contacted');
});

test('admin can schedule and complete site visit with rating', function () {
    $lead = CrmLead::create([
        'company_id' => $this->company->id,
        'lead_code'  => 'LED-TEST-002',
        'name'       => 'Farhana Chowdhury',
        'phone'      => '+880 1819 876543',
        'source'     => 'walk_in',
        'stage'      => 'qualified',
        'priority'   => 'high',
        'score'      => 45,
    ]);

    // Schedule visit
    $this->actingAs($this->admin, 'admin')->post(route('erp.leads.site-visits.store', $lead), [
        'scheduled_at'  => now()->addDays(2)->format('Y-m-d H:i:s'),
        'agent_remarks' => 'Client requested afternoon tour.',
    ]);

    $visit = CrmSiteVisit::where('lead_id', $lead->id)->first();
    expect($visit)->not->toBeNull()
        ->and($visit->status)->toBe('scheduled')
        ->and($lead->fresh()->stage)->toBe('site_visit_scheduled');

    // Complete visit
    $this->actingAs($this->admin, 'admin')->put(route('erp.site-visits.update', $visit), [
        'status'          => 'completed',
        'client_rating'   => 5,
        'client_feedback' => 'Loved the lake view and floor plan.',
    ]);

    expect($visit->fresh()->status)->toBe('completed')
        ->and($visit->fresh()->client_rating)->toBe(5)
        ->and($lead->fresh()->stage)->toBe('site_visit_completed');
});

test('lead routing service can ingest web inquiries', function () {
    $submission = Submission::create([
        'name'    => 'Anisul Haque',
        'phone'   => '+880 1612 000111',
        'email'   => 'anisul@example.com',
        'message' => 'Interested in 4BHK penthouse pricing.',
    ]);

    $routingService = app(LeadRoutingService::class);
    $lead = $routingService->ingestFromSubmission($submission, $this->company->id, $this->branch->id);

    expect($lead)->not->toBeNull()
        ->and($lead->name)->toBe('Anisul Haque')
        ->and($lead->submission_id)->toBe($submission->id)
        ->and($lead->lead_code)->toStartWith('LED-');
});

test('admin can convert lead to customer with kyc details', function () {
    $lead = CrmLead::create([
        'company_id' => $this->company->id,
        'lead_code'  => 'LED-TEST-003',
        'name'       => 'Kamrul Ahsan',
        'phone'      => '+880 1712 555666',
        'email'      => 'kamrul@example.com',
        'source'     => 'web_form',
        'stage'      => 'negotiation',
        'priority'   => 'urgent',
        'score'      => 85,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post(route('erp.leads.convert', $lead), [
        'name'             => 'Kamrul Ahsan',
        'phone'            => '+880 1712 555666',
        'email'            => 'kamrul@example.com',
        'nid_number'       => '1987654321098',
        'tin_number'       => '9876543210',
        'present_address'  => 'House 14, Road 5, Dhanmondi, Dhaka',
        'nominee_name'     => 'Shahana Ahsan',
        'nominee_relation' => 'Spouse',
        'nominee_nid'      => '1990876543211',
    ]);

    $customer = CrmCustomer::where('phone', '+880 1712 555666')->first();
    expect($customer)->not->toBeNull()
        ->and($customer->customer_code)->toStartWith('CUST-')
        ->and($customer->nid_number)->toBe('1987654321098')
        ->and($customer->converted_from_lead_id)->toBe($lead->id)
        ->and($lead->fresh()->stage)->toBe('booked');

    $response->assertRedirect(route('erp.customers.show', $customer));
});

test('admin can import website submissions as crm leads without duplicate code exception', function () {
    CrmLead::firstOrCreate([
        'lead_code' => 'LED-2026-00001',
    ], [
        'company_id' => $this->company->id,
        'branch_id' => $this->branch->id,
        'name' => 'Existing Customer',
        'phone' => '01700000001',
        'source' => 'web_form',
    ]);

    $submission = Submission::create([
        'name'    => 'Sub Imported User',
        'phone'   => '01899999999',
        'email'   => 'imported@example.com',
        'message' => 'Please call me back.',
    ]);

    $response = $this->actingAs($this->admin, 'admin')
        ->post(route('erp.leads.import-submissions'));

    $response->assertRedirect(route('erp.leads.index'));
    $response->assertSessionHas('success');

    $importedLead = CrmLead::where('submission_id', $submission->id)->first();
    expect($importedLead)->not->toBeNull()
        ->and($importedLead->lead_code)->not->toBe('LED-2026-00001')
        ->and($importedLead->name)->toBe('Sub Imported User');
});
