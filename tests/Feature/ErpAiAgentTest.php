<?php

use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\CrmCustomer;
use App\Models\ERP\CrmLead;
use App\Models\ERP\DevBookingInstallment;
use App\Models\ERP\DevProject;
use App\Models\ERP\DevProjectUnit;
use App\Models\ERP\DevSalesBooking;
use App\Services\ERP\ErpAiAgentService;
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

    CrmLead::firstOrCreate(
        ['lead_code' => 'LED-TEST-001'],
        [
            'company_id'        => $this->company->id,
            'branch_id'         => $this->branch->id,
            'assigned_agent_id' => $this->admin->id,
            'name'              => 'Hasan Parvez',
            'phone'             => '01866700047',
            'source'            => 'web_form',
            'stage'             => 'new',
            'priority'          => 'high',
        ]
    );
});

test('ai assistant index page loads successfully for authenticated admin', function () {
    $response = $this->actingAs($this->admin, 'admin')
        ->withSession([
            'erp_company_id' => $this->company->id,
            'erp_branch_id'  => $this->branch->id,
        ])
        ->get(route('erp.ai-assistant.index'));

    $response->assertStatus(200);
    $response->assertSee('AI Database Assistant');
    $response->assertSee('রিয়েল এস্টেট ইআরপি');
});

test('ai service detects intent and answers bangla queries accurately', function () {
    $service = app(ErpAiAgentService::class);

    // Sales today
    $res1 = $service->ask('আজকের সেল কয়টা হয়েছে?');
    expect($res1['intent'])->toBe('sales_today')
        ->and($res1['answer'])->toContain('আজকের সেল');

    // Profit today
    $res2 = $service->ask('আজকের লাভ কত?');
    expect($res2['intent'])->toBe('profit_today')
        ->and($res2['answer'])->toContain('আজকের লাভ');

    // Dues
    $res3 = $service->ask('কত টাকা বকেয়া আছে?');
    expect($res3['intent'])->toBe('dues')
        ->and($res3['answer'])->toContain('বকেয়া');

    // Inventory
    $res4 = $service->ask('ইউনিট কয়টা বাকি আছে?');
    expect($res4['intent'])->toBe('inventory')
        ->and($res4['answer'])->toContain('ইনভেন্টরি');
});

test('ai service accurately handles colloquial banglish queries', function () {
    $service = app(ErpAiAgentService::class);

    // Banglish sales query with typo as in user prompt
    $res1 = $service->ask('ajaker sell koyta hoise');
    expect($res1['intent'])->toBe('sales_today');

    // Banglish profit query
    $res2 = $service->ask('ajker profit koto');
    expect($res2['intent'])->toBe('profit_today');

    // Banglish due query
    $res3 = $service->ask('due ase koto');
    expect($res3['intent'])->toBe('dues');

    // Banglish labor query
    $res4 = $service->ask('ajker labor koyta');
    expect($res4['intent'])->toBe('labor');

    // Company overview query
    $res5 = $service->ask('company er obostha kemon');
    expect($res5['intent'])->toBe('company_overview');

    // Specific booking code query
    $res6 = $service->ask('BKG-2026-00001 er details ki?');
    expect($res6['intent'])->toBe('search_code');

    // Projects query
    $res7 = $service->ask('amader project koyta');
    expect($res7['intent'])->toBe('projects_info')
        ->and($res7['answer'])->toContain('প্রকল্প');

    // Customers query
    $res8 = $service->ask('customer koyjon');
    expect($res8['intent'])->toBe('customers_info');

    // Vendors query
    $res9 = $service->ask('vendor list');
    expect($res9['intent'])->toBe('vendors_info');

    // Smart name search query
    $res10 = $service->ask('Hasan Parvez');
    expect($res10['intent'])->toBe('smart_entity_search')
        ->and($res10['answer'])->toContain('Hasan');
});

test('ai service corrects spelling mistakes and typos with accurate answers', function () {
    $service = app(ErpAiAgentService::class);

    // Typo in sales
    expect($service->ask('ajkr sel koyta')['intent'])->toBe('sales_today');

    // Typo in today profit
    expect($service->ask('ajkr profitt koto')['intent'])->toBe('profit_today');

    // Typo in overall profit
    expect($service->ask('profitt koto')['intent'])->toBe('profit_summary');

    // Typo in dues
    expect($service->ask('dieu koto ase')['intent'])->toBe('dues');

    // Typo in project
    expect($service->ask('projct koyta')['intent'])->toBe('projects_info');

    // Typo in customer
    expect($service->ask('custmar koyjon')['intent'])->toBe('customers_info');

    // Typo in vendor
    expect($service->ask('vendur list')['intent'])->toBe('vendors_info');

    // Typo in cement
    expect($service->ask('semnt koto ase')['intent'])->toBe('materials_stock');

    // Typo in name search (Hasam instead of Hasan)
    $resName = $service->ask('Hasam Parvez');
    expect($resName['intent'])->toBe('smart_entity_search')
        ->and($resName['answer'])->toContain('Hasan');

    // Typo in staff
    expect($service->ask('staf list')['intent'])->toBe('employees');

    // Typo in jomi
    expect($service->ask('jomii koyta')['intent'])->toBe('land_bank');
});

test('ai service handles english queries correctly', function () {
    $service = app(ErpAiAgentService::class);

    $res1 = $service->ask('how many sales bookings today?');
    expect($res1['intent'])->toBe('sales_today')
        ->and($res1['language'])->toBe('en');

    $res2 = $service->ask('what is our total dues?');
    expect($res2['intent'])->toBe('dues');

    $res3 = $service->ask('how much is today profit?');
    expect($res3['intent'])->toBe('profit_today');
});

test('ai query json endpoint returns formatted response with markdown answer', function () {
    $response = $this->actingAs($this->admin, 'admin')
        ->withSession([
            'erp_company_id' => $this->company->id,
            'erp_branch_id'  => $this->branch->id,
        ])
        ->postJson(route('erp.ai-assistant.query'), [
            'query' => 'ajker sell koyta hoise',
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'question',
            'intent',
            'answer',
            'metrics',
            'suggestions',
            'powered_by',
        ],
    ]);

    $data = $response->json('data');
    expect($data['intent'])->toBe('sales_today')
        ->and($data['answer'])->toBeString();
});

test('ai query endpoint validates required query parameter', function () {
    $response = $this->actingAs($this->admin, 'admin')
        ->withSession([
            'erp_company_id' => $this->company->id,
            'erp_branch_id'  => $this->branch->id,
        ])
        ->postJson(route('erp.ai-assistant.query'), [
            'query' => '',
        ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['query']);
});

test('ai quick overview endpoint returns real-time metrics', function () {
    $response = $this->actingAs($this->admin, 'admin')
        ->withSession([
            'erp_company_id' => $this->company->id,
            'erp_branch_id'  => $this->branch->id,
        ])
        ->getJson(route('erp.ai-assistant.overview'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'metrics' => [
            'date',
            'today_sales_count',
            'today_sales_value',
            'today_income',
            'today_expense',
            'today_profit',
            'total_dues',
            'overdue_dues',
            'total_units',
            'available_units',
            'today_leads',
            'total_leads',
        ],
    ]);
});

test('ai service answers specific unit pricing and details query', function () {
    $project = DevProject::firstOrCreate(
        ['project_code' => 'PRJ-TEST-UNIT'],
        [
            'company_id'   => $this->company->id,
            'branch_id'    => $this->branch->id,
            'name'         => 'Aurelia Grand Tower',
            'project_type' => 'residential',
            'status'       => 'under_construction',
        ]
    );

    $unit = DevProjectUnit::firstOrCreate(
        ['project_id' => $project->id, 'unit_number' => '5A'],
        [
            'floor_label'       => '5th Floor',
            'unit_type'         => 'apartment',
            'gross_area_sqft'   => 1850,
            'bedrooms'          => 3,
            'bathrooms'         => 3,
            'grand_total_price' => 12500000,
            'status'            => 'available',
        ]
    );

    $service = app(ErpAiAgentService::class);
    $res = $service->ask('5A flat er dam koto?');

    expect($res['intent'])->toBe('specific_unit')
        ->and($res['answer'])->toContain('5A')
        ->and($res['answer'])->toContain('12,500,000');
});

test('ai service answers branches, departments, and admins queries accurately', function () {
    $service = app(ErpAiAgentService::class);

    // Branches query
    $resBranch = $service->ask('branch koyta ache');
    expect($resBranch['intent'])->toBe('branches_info')
        ->and($resBranch['answer'])->toContain('Dhaka Corporate HQ');

    // Departments query
    $resDept = $service->ask('department koyta');
    expect($resDept['intent'])->toBe('departments_info')
        ->and($resDept['answer'])->toContain('Executive');

    // Admins query
    $resAdmin = $service->ask('admin koyjon');
    expect($resAdmin['intent'])->toBe('admins_info')
        ->and($resAdmin['answer'])->toContain('Super Admin');
});

test('ai service extracts specific field in smart entity search', function () {
    $service = app(ErpAiAgentService::class);

    $resPhone = $service->ask('Hasan Parvez er phone number ki?');
    expect($resPhone['intent'])->toBe('smart_entity_search')
        ->and($resPhone['answer'])->toContain('01866700047');
});

test('ai service handles highest due and highest price unit queries', function () {
    $service = app(ErpAiAgentService::class);

    $resDue = $service->ask('shobcheye beshi baki kar?');
    expect($resDue['intent'])->toBe('highest_due_customer');

    $resPrice = $service->ask('shobcheye dami flat konta?');
    expect($resPrice['intent'])->toBe('highest_price_unit');
});

