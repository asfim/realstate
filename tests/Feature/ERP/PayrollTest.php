<?php

use App\Models\Admin;
use App\Models\ERP\Company;
use App\Models\ERP\Department;
use App\Models\ERP\Employee;
use App\Models\ERP\PayrollRun;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ERPPhase1Seeder;
use Database\Seeders\ERPAccountingAndHRSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(ERPPhase1Seeder::class);
    $this->seed(ERPAccountingAndHRSeeder::class);
});

test('super admin can view staff directory and employee profile', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $employee = Employee::first();

    $response = $this->actingAs($admin, 'admin')->get(route('erp.employees.index'));
    $response->assertStatus(200);
    $response->assertSee('Employee Directory');
    $response->assertSee($employee->name);

    $showResponse = $this->actingAs($admin, 'admin')->get(route('erp.employees.show', $employee));
    $showResponse->assertStatus(200);
    $showResponse->assertSee($employee->employee_code);
    $showResponse->assertSee($employee->designation);
});

test('can enroll a new employee with full salary and deduction structure', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $company = Company::first();
    $dept = Department::first();

    $payload = [
        'company_id' => $company->id,
        'department_id' => $dept?->id,
        'employee_code' => 'EMP-TEST-999',
        'name' => 'Fahim Chowdhury',
        'phone' => '+880 1799-001122',
        'email' => 'fahim.test@aureliaestates.com',
        'designation' => 'Assistant Project Architect',
        'joining_date' => '2026-05-01',
        'basic_salary' => 45000.00,
        'house_rent' => 18000.00,
        'medical_allowance' => 5000.00,
        'other_allowance' => 2000.00,
        'tax_deduction' => 3000.00,
        'provident_fund' => 4500.00,
        'bank_name' => 'City Bank Ltd',
        'bank_account_no' => '220199485712',
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.employees.store'), $payload);
    $response->assertRedirect();

    $this->assertDatabaseHas('erp_employees', [
        'employee_code' => 'EMP-TEST-999',
        'name' => 'Fahim Chowdhury',
        'gross_salary' => 70000.00, // 45k + 18k + 5k + 2k
    ]);
});

test('can generate monthly payroll run and automatically post salary voucher to general ledger', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $company = Company::first();

    $payload = [
        'company_id' => $company->id,
        'year' => 2026,
        'month' => 11, // November 2026
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.payroll.generate'), $payload);
    $response->assertRedirect();

    $payrollRun = PayrollRun::where('company_id', $company->id)
        ->where('year', 2026)
        ->where('month', 11)
        ->first();

    expect($payrollRun)->not->toBeNull();
    expect($payrollRun->items()->count())->toBe(Employee::where('status', 'active')->count());
    expect($payrollRun->total_net)->toBeGreaterThan(0);
    expect($payrollRun->journal_id)->not->toBeNull();

    // Check linked Journal
    $this->assertDatabaseHas('erp_journals', [
        'id' => $payrollRun->journal_id,
        'voucher_type' => 'payment',
    ]);

    $journal = $payrollRun->journal;
    expect($journal->isBalanced())->toBeTrue();
});

test('cannot generate duplicate payroll run for same year and month', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $company = Company::first();

    // October 2026 was already seeded
    $payload = [
        'company_id' => $company->id,
        'year' => 2026,
        'month' => 10,
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.payroll.generate'), $payload);
    $response->assertSessionHasErrors(['month']);
});
