<?php

use App\Models\Admin;
use App\Models\AccountCategory;
use App\Models\Transaction;
use App\Models\Payroll;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\AccountCategorySeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(AccountCategorySeeder::class);

    $this->admin = Admin::firstOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
        ]
    );

    if (!$this->admin->hasRole('Super Admin')) {
        $this->admin->assignRole('Super Admin');
    }
});

test('super admin can access accounts overview', function () {
    $response = $this->actingAs($this->admin, 'admin')->get(route('admin.accounts.index'));

    $response->assertStatus(200);
    $response->assertSee('Financial Overview');
    $response->assertSee('Total Revenue / Income');
    $response->assertSee('Total Expenses');
    $response->assertSee('Staff Salaries Paid');
});

test('super admin can create an income entry', function () {
    $category = AccountCategory::where('type', 'income')->first();

    $response = $this->actingAs($this->admin, 'admin')->post(route('admin.incomes.store'), [
        'title' => 'Villa Aurelia Booking Fee',
        'amount' => 15000.00,
        'transaction_date' => now()->format('Y-m-d'),
        'account_category_id' => $category->id,
        'payment_method' => 'bank_transfer',
        'reference_no' => 'REC-TEST-001',
        'note' => 'Client initial advance payment',
    ]);

    $response->assertRedirect(route('admin.incomes.index'));
    $this->assertDatabaseHas('transactions', [
        'type' => 'income',
        'title' => 'Villa Aurelia Booking Fee',
        'amount' => 15000.00,
        'reference_no' => 'REC-TEST-001',
    ]);
});

test('super admin can create an expense entry', function () {
    $category = AccountCategory::where('type', 'expense')->first();

    $response = $this->actingAs($this->admin, 'admin')->post(route('admin.expenses.store'), [
        'title' => 'Head Office Monthly Rent',
        'amount' => 3500.00,
        'transaction_date' => now()->format('Y-m-d'),
        'account_category_id' => $category->id,
        'payment_method' => 'bank_transfer',
        'reference_no' => 'RENT-2026-11',
        'note' => 'Commercial space rental payment',
    ]);

    $response->assertRedirect(route('admin.expenses.index'));
    $this->assertDatabaseHas('transactions', [
        'type' => 'expense',
        'title' => 'Head Office Monthly Rent',
        'amount' => 3500.00,
    ]);
});

test('super admin can disburse staff salary with automatic expense option', function () {
    $teamMember = TeamMember::create([
        'name' => 'Alexander Wright',
        'role' => 'Senior Architect',
        'email' => 'alexander@example.com',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post(route('admin.payrolls.store'), [
        'team_member_id' => $teamMember->id,
        'staff_name' => 'Alexander Wright',
        'staff_role' => 'Senior Architect',
        'salary_month' => 'November 2026',
        'basic_salary' => 5000.00,
        'allowance' => 500.00,
        'deduction' => 200.00,
        'payment_date' => now()->format('Y-m-d'),
        'payment_method' => 'bank_transfer',
        'status' => 'paid',
        'record_as_expense' => 1,
        'note' => 'Regular monthly payroll',
    ]);

    $response->assertRedirect(route('admin.payrolls.index'));

    // Check Payroll record (5000 + 500 - 200 = 5300 net)
    $this->assertDatabaseHas('payrolls', [
        'staff_name' => 'Alexander Wright',
        'basic_salary' => 5000.00,
        'allowance' => 500.00,
        'deduction' => 200.00,
        'net_salary' => 5300.00,
        'status' => 'paid',
    ]);

    // Check automatically created expense
    $this->assertDatabaseHas('transactions', [
        'type' => 'expense',
        'amount' => 5300.00,
    ]);
});
