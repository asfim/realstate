<?php

use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\Department;
use App\Models\ERP\Designation;
use App\Models\ERP\DevProject;
use App\Models\ERP\HrDailyLaborer;
use App\Models\ERP\HrEmployee;
use App\Models\ERP\HrLaborMusterRoll;
use App\Models\ERP\HrPayroll;
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
    $this->department = Department::where('company_id', $this->company->id)->first();
    $this->designation = Designation::where('company_id', $this->company->id)->first();

    // Create a dummy project for labor assignments
    $this->project = DevProject::firstOrCreate(
        ['project_code' => 'PRJ-HR-001'],
        [
            'company_id'      => $this->company->id,
            'branch_id'       => $this->branch->id,
            'name'            => 'Aurelia Crown Residence',
            'project_type'    => 'residential',
            'status'          => 'under_construction',
            'land_area_katha' => 10.0,
            'total_floors'    => 14,
            'city'            => 'Dhaka',
            'location'        => 'Gulshan 2',
        ]
    );
});

test('admin can view hr employee index, payroll index, labor index, and daily muster roll', function () {
    $r1 = $this->actingAs($this->admin, 'admin')->get('/erp/hr/employees');
    $r1->assertStatus(200);
    $r1->assertSee('Permanent Staff');

    $r2 = $this->actingAs($this->admin, 'admin')->get('/erp/hr/payrolls');
    $r2->assertStatus(200);
    $r2->assertSee('Monthly Disbursements');

    $r3 = $this->actingAs($this->admin, 'admin')->get('/erp/hr/labor');
    $r3->assertStatus(200);
    $r3->assertSee('Daily Laborers');

    $r4 = $this->actingAs($this->admin, 'admin')->get('/erp/hr/labor/muster');
    $r4->assertStatus(200);
    $r4->assertSee('Daily Labor Attendance');
});

test('admin can onboard permanent employee with gross salary auto-calculation', function () {
    $payload = [
        'name'                 => 'Engr. Mahmudul Hasan',
        'phone'                => '+8801712000111',
        'email'                => 'mahmud@example.com',
        'nid_no'               => '19901234567890',
        'joining_date'         => '2026-01-01',
        'employment_type'      => 'permanent',
        'branch_id'            => $this->branch->id,
        'department_id'        => $this->department->id,
        'designation_id'       => $this->designation->id,
        'basic_salary'         => 60000.00,
        'house_rent_allowance' => 25000.00,
        'medical_allowance'    => 5000.00,
        'conveyance_allowance' => 5000.00,
        'bank_name'            => 'Dutch-Bangla Bank PLC',
        'bank_account_no'      => '115.120.334455',
        'status'               => 'active',
    ];

    $response = $this->actingAs($this->admin, 'admin')->post('/erp/hr/employees', $payload);
    $response->assertSessionHasNoErrors();

    $employee = HrEmployee::where('email', 'mahmud@example.com')->first();
    expect($employee)->not->toBeNull()
        ->and($employee->employee_code)->toStartWith('EMP-')
        ->and((float) $employee->gross_salary)->toEqual(95000.0);

    $response->assertRedirect('/erp/hr/employees/' . $employee->id);
});

test('admin can view employee profile and edit employee information', function () {
    $employee = HrEmployee::create([
        'company_id'           => $this->company->id,
        'branch_id'            => $this->branch->id,
        'employee_code'        => 'EMP-2026-099',
        'name'                 => 'Shahriar Kabir',
        'phone'                => '01811223344',
        'email'                => 'kabir@example.com',
        'joining_date'         => '2025-06-01',
        'employment_type'      => 'permanent',
        'basic_salary'         => 50000.00,
        'house_rent_allowance' => 20000.00,
        'gross_salary'         => 70000.00,
        'status'               => 'active',
    ]);

    $viewRes = $this->actingAs($this->admin, 'admin')->get("/erp/hr/employees/{$employee->id}");
    $viewRes->assertStatus(200);
    $viewRes->assertSee('Shahriar Kabir');
    $viewRes->assertSee('EMP-2026-099');

    $editRes = $this->actingAs($this->admin, 'admin')->get("/erp/hr/employees/{$employee->id}/edit");
    $editRes->assertStatus(200);

    $updateRes = $this->actingAs($this->admin, 'admin')->put("/erp/hr/employees/{$employee->id}", [
        'name'                 => 'Shahriar Kabir Chowdhury',
        'phone'                => '01811223344',
        'email'                => 'kabir@example.com',
        'joining_date'         => '2025-06-01',
        'employment_type'      => 'permanent',
        'basic_salary'         => 55000.00,
        'house_rent_allowance' => 25000.00,
        'medical_allowance'    => 5000.00,
        'conveyance_allowance' => 5000.00,
        'status'               => 'active',
    ]);
    $updateRes->assertSessionHasNoErrors();
    $updateRes->assertRedirect("/erp/hr/employees/{$employee->id}");

    $employee->refresh();
    expect($employee->name)->toBe('Shahriar Kabir Chowdhury')
        ->and((float) $employee->gross_salary)->toEqual(90000.0);
});

test('admin can generate monthly payroll voucher for employee with bonus and deductions', function () {
    $employee = HrEmployee::create([
        'company_id'           => $this->company->id,
        'branch_id'            => $this->branch->id,
        'employee_code'        => 'EMP-2026-077',
        'name'                 => 'Tariq Anam',
        'phone'                => '01700998877',
        'joining_date'         => '2026-01-15',
        'employment_type'      => 'permanent',
        'basic_salary'         => 40000.00,
        'house_rent_allowance' => 15000.00,
        'medical_allowance'    => 5000.00,
        'gross_salary'         => 60000.00,
        'status'               => 'active',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post('/erp/hr/payrolls', [
        'employee_id'  => $employee->id,
        'salary_month' => 10,
        'salary_year'  => 2026,
        'bonus'        => 5000.00,
        'deductions'   => 2000.00,
    ]);

    $response->assertSessionHasNoErrors();

    $payroll = HrPayroll::where('employee_id', $employee->id)->where('salary_month', 10)->first();
    expect($payroll)->not->toBeNull()
        ->and($payroll->payroll_code)->toStartWith('PRL-')
        ->and((float) $payroll->gross_salary)->toEqual(60000.0)
        ->and((float) $payroll->bonus)->toEqual(5000.0)
        ->and((float) $payroll->deductions)->toEqual(2000.0)
        ->and((float) $payroll->net_payable)->toEqual(63000.0) // 60,000 + 5,000 - 2,000
        ->and($payroll->payment_status)->toBe('unpaid');

    $response->assertRedirect('/erp/hr/payrolls/' . $payroll->id);
});

test('admin cannot process duplicate payroll for same employee and period', function () {
    $employee = HrEmployee::create([
        'company_id'           => $this->company->id,
        'branch_id'            => $this->branch->id,
        'employee_code'        => 'EMP-2026-088',
        'name'                 => 'Farzana Haque',
        'phone'                => '01711224455',
        'joining_date'         => '2026-01-01',
        'employment_type'      => 'permanent',
        'basic_salary'         => 50000.00,
        'gross_salary'         => 50000.00,
        'status'               => 'active',
    ]);

    HrPayroll::create([
        'company_id'     => $this->company->id,
        'branch_id'      => $this->branch->id,
        'employee_id'    => $employee->id,
        'payroll_code'   => 'PAY-2026-0001',
        'salary_month'   => 10,
        'salary_year'    => 2026,
        'gross_salary'   => 50000.00,
        'bonus'          => 0,
        'deductions'     => 0,
        'net_payable'    => 50000.00,
        'payment_status' => 'unpaid',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post('/erp/hr/payrolls', [
        'employee_id'  => $employee->id,
        'salary_month' => 10,
        'salary_year'  => 2026,
        'bonus'        => 0,
        'deductions'   => 0,
    ]);

    $response->assertSessionHas('error');
    expect(HrPayroll::where('employee_id', $employee->id)->where('salary_month', 10)->count())->toBe(1);
});

test('admin can disburse payroll voucher and mark it paid', function () {
    $employee = HrEmployee::create([
        'company_id'           => $this->company->id,
        'branch_id'            => $this->branch->id,
        'employee_code'        => 'EMP-2026-033',
        'name'                 => 'Anisur Rahman',
        'phone'                => '01911223344',
        'joining_date'         => '2026-01-01',
        'employment_type'      => 'permanent',
        'basic_salary'         => 45000.00,
        'gross_salary'         => 45000.00,
        'status'               => 'active',
    ]);

    $payroll = HrPayroll::create([
        'company_id'     => $this->company->id,
        'branch_id'      => $this->branch->id,
        'employee_id'    => $employee->id,
        'payroll_code'   => 'PAY-2026-0002',
        'salary_month'   => 10,
        'salary_year'    => 2026,
        'gross_salary'   => 45000.00,
        'bonus'          => 0,
        'deductions'     => 0,
        'net_payable'    => 45000.00,
        'payment_status' => 'unpaid',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post("/erp/hr/payrolls/{$payroll->id}/pay", [
        'payment_date'   => '2026-10-31',
        'payment_method' => 'bank_transfer',
    ]);

    $response->assertSessionHasNoErrors();
    $payroll->refresh();

    expect($payroll->payment_status)->toBe('paid')
        ->and($payroll->payment_method)->toBe('bank_transfer')
        ->and($payroll->payment_date->format('Y-m-d'))->toBe('2026-10-31');
});

test('admin can register daily site laborer and log attendance and overtime on muster roll', function () {
    // 1. Register Laborer
    $laborerRes = $this->actingAs($this->admin, 'admin')->post('/erp/hr/labor', [
        'assigned_project_id'  => $this->project->id,
        'name'                 => 'Md. Kalam Hossain',
        'phone'                => '01600112233',
        'nid_no'               => '8273648291',
        'trade'                => 'mason',
        'daily_wage_rate'      => 800.00,
        'overtime_hourly_rate' => 150.00,
    ]);

    $laborerRes->assertSessionHasNoErrors();
    $laborer = HrDailyLaborer::where('name', 'Md. Kalam Hossain')->first();
    expect($laborer)->not->toBeNull()
        ->and($laborer->laborer_code)->toStartWith('LAB-')
        ->and((float) $laborer->daily_wage_rate)->toEqual(800.0)
        ->and((float) $laborer->overtime_hourly_rate)->toEqual(150.0);

    // 2. Log Muster Roll Attendance (8 regular hours + 2 overtime hours)
    // Regular wage = 8 * (800 / 8) = 800
    // Overtime wage = 2 * 150 = 300
    // Total wage = 800 + 300 = 1100
    $musterRes = $this->actingAs($this->admin, 'admin')->post('/erp/hr/labor/muster', [
        'project_id'     => $this->project->id,
        'work_date'      => '2026-11-03',
        'laborer_id'     => $laborer->id,
        'is_present'     => 1,
        'regular_hours'  => 8.0,
        'overtime_hours' => 2.0,
    ]);

    $musterRes->assertSessionHasNoErrors();

    $entry = HrLaborMusterRoll::where('laborer_id', $laborer->id)->first();

    expect($entry)->not->toBeNull()
        ->and($entry->is_present)->toBeTrue()
        ->and((float) $entry->regular_wage)->toEqual(800.0)
        ->and((float) $entry->overtime_wage)->toEqual(300.0)
        ->and((float) $entry->total_wage)->toEqual(1100.0);
});
