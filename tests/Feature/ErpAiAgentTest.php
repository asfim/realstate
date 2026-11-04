<?php

use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\CrmCustomer;
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
