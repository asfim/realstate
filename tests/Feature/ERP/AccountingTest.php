<?php

use App\Models\Admin;
use App\Models\ERP\Account;
use App\Models\ERP\Company;
use App\Models\ERP\Customer;
use App\Models\ERP\FiscalPeriod;
use App\Models\ERP\Journal;
use App\Services\ERP\AccountingService;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ERPPhase1Seeder;
use Database\Seeders\ERPAccountingAndHRSeeder;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(ERPPhase1Seeder::class);
    $this->seed(ERPAccountingAndHRSeeder::class);
});

test('super admin can view chart of accounts', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($admin, 'admin')->get(route('erp.accounting.accounts'));
    $response->assertStatus(200);
    $response->assertSee('Chart of Accounts');
    $response->assertSee('Cash in Hand');
    $response->assertSee('Residential Apartment Sales Revenue');
});

test('can view journals list and voucher entries', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();

    $response = $this->actingAs($admin, 'admin')->get(route('erp.accounting.journals'));
    $response->assertStatus(200);
    $response->assertSee('General Journal');
    $response->assertSee('SV-202610-0001');
});

test('cannot create unbalanced journal voucher (dr != cr)', function () {
    $service = app(AccountingService::class);
    $company = Company::first();
    $period = FiscalPeriod::first();
    $cashAcc = Account::where('code', '1010')->first();
    $salesAcc = Account::where('code', '4010')->first();

    expect(fn () => $service->createJournal([
        'company_id' => $company->id,
        'fiscal_period_id' => $period->id,
        'journal_no' => 'UNBALANCED-01',
        'date' => '2026-11-01',
        'voucher_type' => 'journal',
        'narration' => 'Test unbalanced entry',
    ], [
        [
            'account_id' => $cashAcc->id,
            'debit' => 10000.00,
            'credit' => 0.00,
        ],
        [
            'account_id' => $salesAcc->id,
            'debit' => 0.00,
            'credit' => 9000.00, // Debits != Credits
        ],
    ]))->toThrow(ValidationException::class, 'Journal lines do not balance');
});

test('can create balanced journal voucher through controller', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $company = Company::first();
    $cashAcc = Account::where('code', '1010')->first();
    $bankAcc = Account::where('code', '1020')->first();

    $payload = [
        'company_id' => $company->id,
        'date' => '2026-11-01',
        'voucher_type' => 'contra',
        'reference' => 'CHQ-CASH-DEP',
        'narration' => 'Bank cash deposit contra voucher',
        'lines' => [
            [
                'account_id' => $bankAcc->id,
                'debit' => 50000.00,
                'credit' => 0.00,
                'description' => 'Deposit cash into bank',
            ],
            [
                'account_id' => $cashAcc->id,
                'debit' => 0.00,
                'credit' => 50000.00,
                'description' => 'Cash withdrawn for bank deposit',
            ],
        ],
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.accounting.journals.store'), $payload);
    $response->assertRedirect(route('erp.accounting.journals'));

    $this->assertDatabaseHas('erp_journals', [
        'company_id' => $company->id,
        'voucher_type' => 'contra',
        'total_debit' => 50000.00,
        'total_credit' => 50000.00,
    ]);

    $journal = Journal::where('voucher_type', 'contra')->latest('id')->first();
    expect($journal->isBalanced())->toBeTrue();
});

test('profit and loss report calculates gross profit and net profit accurately', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $company = Company::first();

    $response = $this->actingAs($admin, 'admin')->get(route('erp.accounting.profit-and-loss', [
        'company_id' => $company->id,
        'from_date' => '2026-07-01',
        'to_date' => '2027-06-30',
    ]));

    $response->assertStatus(200);
    $response->assertSee('GROSS PROFIT');
    $response->assertSee('NET PROFIT');
    $response->assertSee('Total Operating Revenue');

    $service = app(AccountingService::class);
    $pnl = $service->getProfitAndLoss($company->id, '2026-07-01', '2027-06-30');

    expect($pnl['total_revenue'])->toBeGreaterThan(0);
    expect($pnl['gross_profit'])->toBe($pnl['total_revenue'] - $pnl['total_direct_cost']);
    expect($pnl['net_profit'])->toBe($pnl['gross_profit'] - $pnl['total_operating_expense']);
});

test('trial balance debits equal credits invariant holds', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $company = Company::first();

    $response = $this->actingAs($admin, 'admin')->get(route('erp.accounting.trial-balance', [
        'company_id' => $company->id,
        'as_of_date' => '2027-06-30',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Trial Balance');

    $service = app(AccountingService::class);
    $tb = $service->getTrialBalance($company->id, '2027-06-30');

    expect($tb['is_balanced'])->toBeTrue();
    expect((float) $tb['total_debit'])->toEqual((float) $tb['total_credit']);
});

test('customer subledger displays receivables and payments correctly', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $customer = Customer::first();

    $response = $this->actingAs($admin, 'admin')->get(route('erp.accounting.customer-ledger', [
        'customer_id' => $customer->id,
    ]));

    $response->assertStatus(200);
    $response->assertSee('Customer Subledger');
    $response->assertSee($customer->name);
});
