<?php

use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\FiscalYear;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\DocumentSequenceService;
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

test('guest is redirected from erp dashboard', function () {
    $response = $this->get('/erp');
    $response->assertRedirect('/admin/login');
});

test('authenticated admin can access erp dashboard', function () {
    $response = $this->actingAs($this->admin, 'admin')->get('/erp');
    $response->assertStatus(200);
    $response->assertSee('ENTERPRISE REAL ESTATE ERP');
    $response->assertSee('Aurelia Estates');
});

test('admin can switch company and branch context', function () {
    $secondBranch = Branch::firstOrCreate(
        ['company_id' => $this->company->id, 'code' => 'CTG-01'],
        [
            'name'           => 'Chattogram Regional Branch',
            'is_head_office' => false,
            'status'         => 'active',
        ]
    );

    $response = $this->actingAs($this->admin, 'admin')
        ->post('/erp/switch-context', [
            'company_id' => $this->company->id,
            'branch_id'  => $secondBranch->id,
        ]);

    $response->assertSessionHas('erp_active_company_id', $this->company->id);
    $response->assertSessionHas('erp_active_branch_id', $secondBranch->id);
});

test('document sequence service generates atomic codes', function () {
    $fy = FiscalYear::where('company_id', $this->company->id)->first();
    $seqService = app(DocumentSequenceService::class);

    $firstNumber = $seqService->getNextNumber('quotation', $this->company->id, $this->branch->id, $fy->id);
    $secondNumber = $seqService->getNextNumber('quotation', $this->company->id, $this->branch->id, $fy->id);

    expect($firstNumber)->toStartWith('QTE-')
        ->and($secondNumber)->toStartWith('QTE-')
        ->and($firstNumber)->not->toBe($secondNumber);
});

test('audit log service records action', function () {
    $this->actingAs($this->admin, 'admin');

    $auditService = app(AuditLogService::class);
    $log = $auditService->log(
        'test.action',
        $this->company,
        ['status' => 'inactive'],
        ['status' => 'active'],
        $this->company->id,
        $this->branch->id
    );

    $this->assertDatabaseHas('audit_logs', [
        'id'         => $log->id,
        'action'     => 'test.action',
        'company_id' => $this->company->id,
        'branch_id'  => $this->branch->id,
    ]);
});

test('admin can view companies list', function () {
    $response = $this->actingAs($this->admin, 'admin')->get('/erp/organization/companies');
    $response->assertStatus(200);
    $response->assertSee($this->company->legal_name);
});

test('admin can view branches list', function () {
    $response = $this->actingAs($this->admin, 'admin')->get('/erp/organization/branches');
    $response->assertStatus(200);
    $response->assertSee($this->branch->name);
});
