<?php

use App\Models\Admin;
use App\Models\ERP\AccChartOfAccount;
use App\Models\ERP\AccJournal;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\CostCenter;
use App\Models\ERP\FiscalYear;
use Database\Seeders\ErpChartOfAccountsSeeder;
use Database\Seeders\ErpFoundationSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(ErpFoundationSeeder::class);
    $this->seed(ErpChartOfAccountsSeeder::class);

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
    $this->costCenter = CostCenter::where('company_id', $this->company->id)->first();
    $this->fy = FiscalYear::where('company_id', $this->company->id)->where('is_current', true)->first();
});

test('admin can view chart of accounts, journals index, trial balance, profit & loss, and balance sheet', function () {
    $r1 = $this->actingAs($this->admin, 'admin')->get('/erp/accounting/coa');
    $r1->assertStatus(200);
    $r1->assertSee('Chart of Accounts');

    $r2 = $this->actingAs($this->admin, 'admin')->get('/erp/accounting/journals');
    $r2->assertStatus(200);
    $r2->assertSee('Journal Vouchers');

    $r3 = $this->actingAs($this->admin, 'admin')->get('/erp/accounting/reports/trial-balance');
    $r3->assertStatus(200);
    $r3->assertSee('Trial Balance');

    $r4 = $this->actingAs($this->admin, 'admin')->get('/erp/accounting/reports/profit-loss');
    $r4->assertStatus(200);
    $r4->assertSee('Income Statement');

    $r5 = $this->actingAs($this->admin, 'admin')->get('/erp/accounting/reports/balance-sheet');
    $r5->assertStatus(200);
    $r5->assertSee('Balance Sheet');
});

test('admin can add a custom account to chart of accounts', function () {
    $response = $this->actingAs($this->admin, 'admin')->post('/erp/accounting/coa', [
        'account_code'   => '1025',
        'account_name'   => 'City Bank Escrow Account (RERA)',
        'account_type'   => 'asset',
        'normal_balance' => 'debit',
        'description'    => 'Dedicated escrow bank account for client advance deposits',
    ]);

    $response->assertSessionHasNoErrors();

    $account = AccChartOfAccount::where('company_id', $this->company->id)->where('account_code', '1025')->first();
    expect($account)->not->toBeNull()
        ->and($account->account_name)->toBe('City Bank Escrow Account (RERA)')
        ->and($account->account_type)->toBe('asset');
});

test('journal voucher fails posting when debits do not equal credits', function () {
    $bankAcc = AccChartOfAccount::where('company_id', $this->company->id)->where('account_code', '1020')->first();
    $salesAcc = AccChartOfAccount::where('company_id', $this->company->id)->where('account_code', '4010')->first();

    $response = $this->actingAs($this->admin, 'admin')->post('/erp/accounting/journals', [
        'voucher_type' => 'JV',
        'entry_date'   => '2026-11-03',
        'narration'    => 'Unbalanced booking test',
        'lines'        => [
            [
                'account_id' => $bankAcc->id,
                'debit'      => 500000,
                'credit'     => 0,
            ],
            [
                'account_id' => $salesAcc->id,
                'debit'      => 0,
                'credit'     => 450000, // Discrepancy of 50,000!
            ],
        ],
    ]);

    // Should return with session error and no journal created
    $response->assertSessionHas('error');
    expect(AccJournal::where('narration', 'Unbalanced booking test')->count())->toBe(0);
});

test('journal voucher posts successfully when debits equal credits with auto voucher code', function () {
    $bankAcc = AccChartOfAccount::where('company_id', $this->company->id)->where('account_code', '1020')->first();
    $vendorAcc = AccChartOfAccount::where('company_id', $this->company->id)->where('account_code', '2010')->first();

    $response = $this->actingAs($this->admin, 'admin')->post('/erp/accounting/journals', [
        'voucher_type' => 'BPV',
        'entry_date'   => '2026-11-03',
        'narration'    => 'Payment disbursed to vendor BSRM for Banani site',
        'lines'        => [
            [
                'account_id'     => $vendorAcc->id,
                'cost_center_id' => $this->costCenter->id,
                'debit'          => 1200000,
                'credit'         => 0,
            ],
            [
                'account_id'     => $bankAcc->id,
                'cost_center_id' => null,
                'debit'          => 0,
                'credit'         => 1200000,
            ],
        ],
    ]);

    $response->assertSessionHasNoErrors();

    $journal = AccJournal::where('company_id', $this->company->id)->where('voucher_type', 'BPV')->first();
    expect($journal)->not->toBeNull()
        ->and($journal->voucher_code)->toStartWith('JRN-')
        ->and((float) $journal->total_amount)->toEqual(1200000.0)
        ->and($journal->entries()->count())->toBe(2);

    expect((float) $journal->entries()->sum('debit'))->toEqual(1200000.0)
        ->and((float) $journal->entries()->sum('credit'))->toEqual(1200000.0);
});

test('trial balance and profit loss compute correctly from posted vouchers', function () {
    $bankAcc = AccChartOfAccount::where('company_id', $this->company->id)->where('account_code', '1020')->first();
    $revenueAcc = AccChartOfAccount::where('company_id', $this->company->id)->where('account_code', '4030')->first(); // Brokerage revenue
    $expenseAcc = AccChartOfAccount::where('company_id', $this->company->id)->where('account_code', '5050')->first(); // Overhead expense

    // 1. Post Revenue: Debit Bank 500,000 / Credit Brokerage Revenue 500,000
    $j1 = AccJournal::create([
        'company_id'   => $this->company->id,
        'voucher_code' => 'JRN-REV-01',
        'voucher_type' => 'BRV',
        'entry_date'   => '2026-11-03',
        'total_amount' => 500000,
        'narration'    => 'Brokerage commission received',
        'status'       => 'posted',
    ]);
    $j1->entries()->create(['account_id' => $bankAcc->id, 'debit' => 500000, 'credit' => 0]);
    $j1->entries()->create(['account_id' => $revenueAcc->id, 'debit' => 0, 'credit' => 500000]);

    // 2. Post Expense: Debit Office Expense 120,000 / Credit Bank 120,000
    $j2 = AccJournal::create([
        'company_id'   => $this->company->id,
        'voucher_code' => 'JRN-EXP-01',
        'voucher_type' => 'BPV',
        'entry_date'   => '2026-11-03',
        'total_amount' => 120000,
        'narration'    => 'Administrative office expense',
        'status'       => 'posted',
    ]);
    $j2->entries()->create(['account_id' => $expenseAcc->id, 'debit' => 120000, 'credit' => 0]);
    $j2->entries()->create(['account_id' => $bankAcc->id, 'debit' => 0, 'credit' => 120000]);

    // Verify Trial Balance: Total Debits = 620,000, Total Credits = 620,000
    $tbResponse = $this->actingAs($this->admin, 'admin')->get('/erp/accounting/reports/trial-balance');
    $tbResponse->assertStatus(200);
    $tbResponse->assertSee('৳ 620,000.00');

    // Verify P&L: Revenue = 500,000, Expense = 120,000, Net Profit = 380,000
    $plResponse = $this->actingAs($this->admin, 'admin')->get('/erp/accounting/reports/profit-loss');
    $plResponse->assertStatus(200);
    $plResponse->assertSee('৳ 500,000.00');
    $plResponse->assertSee('৳ 120,000.00');
    $plResponse->assertSee('৳ 380,000.00');
});
