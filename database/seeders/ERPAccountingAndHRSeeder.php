<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\ERP\Account;
use App\Models\ERP\Booking;
use App\Models\ERP\Company;
use App\Models\ERP\Customer;
use App\Models\ERP\Department;
use App\Models\ERP\Employee;
use App\Models\ERP\FiscalPeriod;
use App\Models\ERP\Installment;
use App\Models\ERP\Journal;
use App\Models\ERP\Payment;
use App\Models\ERP\PaymentAllocation;
use App\Models\ERP\PayrollItem;
use App\Models\ERP\PayrollRun;
use App\Models\ERP\Project;
use App\Models\ERP\Unit;
use App\Services\ERP\AccountingService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ERPAccountingAndHRSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'admin';

        // 1. Spatie Permissions
        $newPermissions = [
            'erp.customers.view',
            'erp.customers.create',
            'erp.bookings.view',
            'erp.bookings.create',
            'erp.payments.view',
            'erp.payments.collect',
            'erp.accounting.view',
            'erp.accounting.manage',
            'erp.accounting.reports',
            'erp.hrm.view',
            'erp.hrm.manage',
            'erp.payroll.view',
            'erp.payroll.manage',
        ];

        foreach ($newPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => $guard]);
        }

        $superAdmin = Role::where(['name' => 'Super Admin', 'guard_name' => $guard])->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($newPermissions);
        }

        $manager = Role::where(['name' => 'Manager', 'guard_name' => $guard])->first();
        if ($manager) {
            $manager->givePermissionTo($newPermissions);
        }

        $admin = Admin::first();
        $adminId = $admin?->id;
        $company = Company::first();
        if (!$company) return;

        // 2. Fiscal Period
        $fiscalPeriod = FiscalPeriod::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'FY 2026-2027'],
            [
                'start_date' => '2026-07-01',
                'end_date' => '2027-06-30',
                'is_closed' => false,
            ]
        );

        // 3. Chart of Accounts (COA)
        $accountsData = [
            // Assets (1000)
            ['code' => '1010', 'name' => 'Cash in Hand', 'type' => 'asset', 'opening_balance' => 500000.00],
            ['code' => '1020', 'name' => 'BRAC Bank Current A/C (Gulshan Branch)', 'type' => 'asset', 'opening_balance' => 45000000.00],
            ['code' => '1200', 'name' => 'Customer Accounts Receivable', 'type' => 'asset', 'opening_balance' => 0.00],
            ['code' => '1300', 'name' => 'Land & Development Work-in-Progress (WIP)', 'type' => 'asset', 'opening_balance' => 35000000.00],
            ['code' => '1500', 'name' => 'Office Equipment & Computing Hardware', 'type' => 'asset', 'opening_balance' => 2500000.00],

            // Liabilities (2000)
            ['code' => '2010', 'name' => 'Accounts Payable — Contractors & Vendors', 'type' => 'liability', 'opening_balance' => 4000000.00],
            ['code' => '2020', 'name' => 'Salaries & Staff Benefits Payable', 'type' => 'liability', 'opening_balance' => 0.00],
            ['code' => '2100', 'name' => 'Customer Advance Booking Money', 'type' => 'liability', 'opening_balance' => 0.00],

            // Equity (3000)
            ['code' => '3010', 'name' => 'Paid-up Share Capital', 'type' => 'equity', 'opening_balance' => 75000000.00],
            ['code' => '3020', 'name' => 'Retained Earnings', 'type' => 'equity', 'opening_balance' => 4000000.00],

            // Revenue (4000)
            ['code' => '4010', 'name' => 'Residential Apartment Sales Revenue', 'type' => 'revenue', 'opening_balance' => 0.00],
            ['code' => '4020', 'name' => 'Plot / Land Sales Revenue', 'type' => 'revenue', 'opening_balance' => 0.00],
            ['code' => '4030', 'name' => 'Commercial Space Sales Revenue', 'type' => 'revenue', 'opening_balance' => 0.00],
            ['code' => '4090', 'name' => 'Transfer Fees & Documentation Income', 'type' => 'revenue', 'opening_balance' => 0.00],

            // Direct Costs (5000)
            ['code' => '5010', 'name' => 'Civil Construction Material Cost (Steel, Rod, Cement)', 'type' => 'expense', 'opening_balance' => 0.00],
            ['code' => '5020', 'name' => 'Subcontractor Structural & Piling Bills', 'type' => 'expense', 'opening_balance' => 0.00],
            ['code' => '5030', 'name' => 'Architectural, Soil Test & Engineering Fees', 'type' => 'expense', 'opening_balance' => 0.00],

            // Operating & HR Expenses (6000)
            ['code' => '6100', 'name' => 'Staff Salaries & Executive Allowances', 'type' => 'expense', 'opening_balance' => 0.00],
            ['code' => '6150', 'name' => 'Sales Agent & Realtor Commission', 'type' => 'expense', 'opening_balance' => 0.00],
            ['code' => '6200', 'name' => 'Office Space Rental & Utility Bills', 'type' => 'expense', 'opening_balance' => 0.00],
            ['code' => '6300', 'name' => 'Marketing, Billboards & Digital Ads', 'type' => 'expense', 'opening_balance' => 0.00],
            ['code' => '6400', 'name' => 'Municipal, RAJUK & Legal Vetting Fees', 'type' => 'expense', 'opening_balance' => 0.00],
        ];

        $accountModels = [];
        foreach ($accountsData as $acc) {
            $accountModels[$acc['code']] = Account::firstOrCreate(
                ['company_id' => $company->id, 'code' => $acc['code']],
                [
                    'name' => $acc['name'],
                    'type' => $acc['type'],
                    'opening_balance' => $acc['opening_balance'],
                    'current_balance' => $acc['opening_balance'],
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
        }

        // 4. Sample Customers
        $customer1 = Customer::firstOrCreate(
            ['company_id' => $company->id, 'customer_code' => 'CUST-001'],
            [
                'name' => 'Engr. Tanvir Ahmed',
                'phone' => '+880 1711-223344',
                'email' => 'tanvir.ahmed@example.com',
                'nid_or_passport' => '19842691234567890',
                'tin' => 'TIN-45892147',
                'address' => 'House 14, Road 5, Dhanmondi, Dhaka',
                'emergency_contact' => 'Mrs. Nazia Tanvir (+880 1711-998877)',
                'status' => 'active',
                'created_by' => $adminId,
            ]
        );

        $customer2 = Customer::firstOrCreate(
            ['company_id' => $company->id, 'customer_code' => 'CUST-002'],
            [
                'name' => 'Dr. Shahedur Rahman',
                'phone' => '+880 1819-556677',
                'email' => 'shahed.rahman@example.com',
                'nid_or_passport' => '19792691987654321',
                'tin' => 'TIN-89542136',
                'address' => 'Sector 7, Uttara, Dhaka',
                'status' => 'active',
                'created_by' => $adminId,
            ]
        );

        // 5. Sample Booking & Installments (Aurelia Skyline Flat 3A)
        $unit = Unit::where('unit_code', 'SKY-3A')->first();
        if ($unit) {
            $booking = Booking::firstOrCreate(
                ['company_id' => $company->id, 'booking_no' => 'BKG-202611-0001'],
                [
                    'unit_id' => $unit->id,
                    'customer_id' => $customer1->id,
                    'booking_date' => '2026-10-15',
                    'contract_price' => 37000000.00,
                    'booking_money' => 2000000.00,
                    'down_payment' => 5000000.00,
                    'discount_amount' => 0.00,
                    'balance_amount' => 30000000.00,
                    'status' => 'confirmed',
                    'notes' => 'Confirmed booking with lakeview apartment contract',
                    'created_by' => $adminId,
                ]
            );

            // Create installments
            for ($i = 1; $i <= 12; $i++) {
                Installment::firstOrCreate(
                    ['booking_id' => $booking->id, 'installment_no' => $i],
                    [
                        'company_id' => $company->id,
                        'title' => "Monthly Installment {$i} of 12",
                        'due_date' => date('Y-m-d', strtotime("2026-10-15 +{$i} month")),
                        'amount' => 2500000.00,
                        'paid_amount' => ($i === 1) ? 2500000.00 : 0,
                        'status' => ($i === 1) ? 'paid' : 'unpaid',
                    ]
                );
            }

            // Collection Payment (Down payment + 1st installment = 7,500,000)
            $payment = Payment::firstOrCreate(
                ['company_id' => $company->id, 'receipt_no' => 'RCT-202610-0001'],
                [
                    'booking_id' => $booking->id,
                    'amount' => 7000000.00,
                    'payment_date' => '2026-10-20',
                    'method' => 'bank_transfer',
                    'status' => 'verified',
                    'bank_name' => 'BRAC Bank',
                    'cheque_or_trans_no' => 'TXN-98421589',
                    'collected_by' => $adminId,
                    'verified_by' => $adminId,
                    'notes' => 'Token booking money & down payment deposit',
                ]
            );
        }

        // 6. Sample Staff & HR Directory
        $deptSales = Department::where('company_id', $company->id)->where('code', 'SALES')->first();
        $deptEng = Department::where('company_id', $company->id)->where('code', 'ENG')->first();
        $deptFin = Department::where('company_id', $company->id)->where('code', 'FIN')->first();

        $employees = [
            [
                'code' => 'EMP-001',
                'name' => 'Engr. Md. Rafiqul Islam',
                'designation' => 'Chief Project Engineer',
                'dept_id' => $deptEng?->id,
                'phone' => '+880 1711-112233',
                'email' => 'rafiq.eng@aureliaestates.com',
                'joining_date' => '2024-01-01',
                'basic' => 90000.00,
                'house' => 30000.00,
                'med' => 10000.00,
                'other' => 10000.00,
                'tax' => 10000.00,
                'pf' => 9000.00,
            ],
            [
                'code' => 'EMP-002',
                'name' => 'Nasrin Sultana',
                'designation' => 'Senior Sales & CRM Manager',
                'dept_id' => $deptSales?->id,
                'phone' => '+880 1711-445566',
                'email' => 'nasrin.sales@aureliaestates.com',
                'joining_date' => '2024-03-15',
                'basic' => 60000.00,
                'house' => 20000.00,
                'med' => 10000.00,
                'other' => 5000.00,
                'tax' => 5000.00,
                'pf' => 6000.00,
            ],
            [
                'code' => 'EMP-003',
                'name' => 'Kamrul Hasan',
                'designation' => 'Assistant Site Engineer',
                'dept_id' => $deptEng?->id,
                'phone' => '+880 1711-778899',
                'email' => 'kamrul.site@aureliaestates.com',
                'joining_date' => '2024-06-01',
                'basic' => 40000.00,
                'house' => 15000.00,
                'med' => 5000.00,
                'other' => 5000.00,
                'tax' => 2000.00,
                'pf' => 4000.00,
            ],
            [
                'code' => 'EMP-004',
                'name' => 'Farhana Akter',
                'designation' => 'Lead Accountant',
                'dept_id' => $deptFin?->id,
                'phone' => '+880 1711-889900',
                'email' => 'farhana.accounts@aureliaestates.com',
                'joining_date' => '2024-02-01',
                'basic' => 50000.00,
                'house' => 20000.00,
                'med' => 5000.00,
                'other' => 5000.00,
                'tax' => 4000.00,
                'pf' => 5000.00,
            ],
        ];

        $employeeModels = [];
        foreach ($employees as $e) {
            $gross = $e['basic'] + $e['house'] + $e['med'] + $e['other'];
            $employeeModels[] = Employee::firstOrCreate(
                ['company_id' => $company->id, 'employee_code' => $e['code']],
                [
                    'name' => $e['name'],
                    'designation' => $e['designation'],
                    'department_id' => $e['dept_id'],
                    'phone' => $e['phone'],
                    'email' => $e['email'],
                    'joining_date' => $e['joining_date'],
                    'basic_salary' => $e['basic'],
                    'house_rent' => $e['house'],
                    'medical_allowance' => $e['med'],
                    'other_allowance' => $e['other'],
                    'tax_deduction' => $e['tax'],
                    'provident_fund' => $e['pf'],
                    'gross_salary' => $gross,
                    'bank_name' => 'BRAC Bank Ltd',
                    'bank_account_no' => '150120' . rand(100000, 999999),
                    'status' => 'active',
                ]
            );
        }

        // 7. Seed Initial Balanced Vouchers into Accounting
        $accountingService = app(AccountingService::class);
        $project = Project::first();

        // Voucher 1: Sales Recognition (Dr Customer Receivable, Cr Apartment Revenue)
        $accountingService->createJournal([
            'company_id' => $company->id,
            'fiscal_period_id' => $fiscalPeriod->id,
            'project_id' => $project?->id,
            'journal_no' => 'SV-202610-0001',
            'date' => '2026-10-15',
            'voucher_type' => 'sales',
            'reference' => 'BKG-202611-0001',
            'narration' => 'Sales contract booking for Flat 3A (Aurelia Skyline Tower)',
            'posted_by' => $adminId,
        ], [
            [
                'account_id' => $accountModels['1200']->id,
                'project_id' => $project?->id,
                'debit' => 37000000.00,
                'credit' => 0.00,
                'description' => 'Receivable from Engr. Tanvir Ahmed',
            ],
            [
                'account_id' => $accountModels['4010']->id,
                'project_id' => $project?->id,
                'debit' => 0.00,
                'credit' => 37000000.00,
                'description' => 'Recognized sales revenue on confirmed booking',
            ],
        ]);

        // Voucher 2: Cash/Bank Collection (Dr Bank, Cr Customer Receivable)
        $accountingService->createJournal([
            'company_id' => $company->id,
            'fiscal_period_id' => $fiscalPeriod->id,
            'project_id' => $project?->id,
            'journal_no' => 'RV-202610-0001',
            'date' => '2026-10-20',
            'voucher_type' => 'receipt',
            'reference' => 'RCT-202610-0001',
            'narration' => 'Payment received from Engr. Tanvir Ahmed (TXN-98421589)',
            'posted_by' => $adminId,
        ], [
            [
                'account_id' => $accountModels['1020']->id,
                'project_id' => $project?->id,
                'debit' => 7000000.00,
                'credit' => 0.00,
                'description' => 'Collection deposited into BRAC Bank',
            ],
            [
                'account_id' => $accountModels['1200']->id,
                'project_id' => $project?->id,
                'debit' => 0.00,
                'credit' => 7000000.00,
                'description' => 'Receivable reduction for receipt RCT-202610-0001',
            ],
        ]);

        // Voucher 3: Construction Direct Cost (Dr Civil Material Cost, Cr Bank)
        $accountingService->createJournal([
            'company_id' => $company->id,
            'fiscal_period_id' => $fiscalPeriod->id,
            'project_id' => $project?->id,
            'journal_no' => 'PV-202610-0001',
            'date' => '2026-10-22',
            'voucher_type' => 'payment',
            'reference' => 'CHQ-778841',
            'narration' => 'Procurement of 500 Tons BSRM Steel Rebar for Tower A foundation',
            'posted_by' => $adminId,
        ], [
            [
                'account_id' => $accountModels['5010']->id,
                'project_id' => $project?->id,
                'debit' => 4500000.00,
                'credit' => 0.00,
                'description' => 'Direct structural material cost',
            ],
            [
                'account_id' => $accountModels['1020']->id,
                'project_id' => $project?->id,
                'debit' => 0.00,
                'credit' => 4500000.00,
                'description' => 'Paid via BRAC Bank Cheque',
            ],
        ]);

        // Voucher 4: Operating Expenses - Office Rent & Marketing (Dr Rent & Ads, Cr Bank)
        $accountingService->createJournal([
            'company_id' => $company->id,
            'fiscal_period_id' => $fiscalPeriod->id,
            'journal_no' => 'PV-202610-0002',
            'date' => '2026-10-25',
            'voucher_type' => 'payment',
            'reference' => 'ONLINE-5412',
            'narration' => 'Corporate office rent and digital lead acquisition ads',
            'posted_by' => $adminId,
        ], [
            [
                'account_id' => $accountModels['6200']->id,
                'debit' => 350000.00,
                'credit' => 0.00,
                'description' => 'Banani corporate office monthly rental',
            ],
            [
                'account_id' => $accountModels['6300']->id,
                'debit' => 250000.00,
                'credit' => 0.00,
                'description' => 'Digital ad campaign for Skyline Tower launch',
            ],
            [
                'account_id' => $accountModels['1020']->id,
                'debit' => 0.00,
                'credit' => 600000.00,
                'description' => 'Disbursed via BRAC Bank',
            ],
        ]);

        // 8. Seed Processed Payroll Run for October 2026
        $payrollRun = PayrollRun::firstOrCreate(
            ['company_id' => $company->id, 'year' => 2026, 'month' => 10],
            [
                'title' => 'Payroll for October 2026',
                'total_gross' => 365000.00,
                'total_deductions' => 45000.00,
                'total_net' => 320000.00,
                'status' => 'approved',
                'created_by' => $adminId,
                'approved_by' => $adminId,
            ]
        );

        foreach ($employeeModels as $emp) {
            $basic = (float) $emp->basic_salary;
            $allowances = (float) $emp->house_rent + (float) $emp->medical_allowance + (float) $emp->other_allowance;
            $deductions = (float) $emp->tax_deduction + (float) $emp->provident_fund;
            $net = ($basic + $allowances) - $deductions;

            PayrollItem::firstOrCreate(
                ['payroll_run_id' => $payrollRun->id, 'employee_id' => $emp->id],
                [
                    'basic_salary' => $basic,
                    'allowances' => $allowances,
                    'deductions' => $deductions,
                    'net_salary' => $net,
                    'payment_status' => 'paid',
                    'payment_method' => 'bank_transfer',
                ]
            );
        }

        // Voucher 5: Salary Disbursement Journal
        $salaryJournal = $accountingService->createJournal([
            'company_id' => $company->id,
            'fiscal_period_id' => $fiscalPeriod->id,
            'journal_no' => 'PV-202610-0003',
            'date' => '2026-10-31',
            'voucher_type' => 'payment',
            'reference' => 'PAYROLL-202610',
            'narration' => 'Salary disbursement for October 2026 across all departments',
            'posted_by' => $adminId,
        ], [
            [
                'account_id' => $accountModels['6100']->id,
                'debit' => 320000.00,
                'credit' => 0.00,
                'description' => 'Monthly staff salary & compensation',
            ],
            [
                'account_id' => $accountModels['1020']->id,
                'debit' => 0.00,
                'credit' => 320000.00,
                'description' => 'Electronic direct deposit from BRAC Bank',
            ],
        ]);

        $payrollRun->update(['journal_id' => $salaryJournal->id]);
    }
}

