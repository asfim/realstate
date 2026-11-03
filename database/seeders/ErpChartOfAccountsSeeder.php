<?php

namespace Database\Seeders;

use App\Models\ERP\AccChartOfAccount;
use App\Models\ERP\Company;
use Illuminate\Database\Seeder;

class ErpChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        $standardCoa = [
            // Assets (1000s)
            ['account_code' => '1010', 'account_name' => 'Cash in Hand (Petty Cash)', 'account_type' => 'asset', 'normal_balance' => 'debit'],
            ['account_code' => '1020', 'account_name' => 'Cash at Bank (BRAC / City Bank)', 'account_type' => 'asset', 'normal_balance' => 'debit'],
            ['account_code' => '1030', 'account_name' => 'Accounts Receivable (Buyers & Tenants)', 'account_type' => 'asset', 'normal_balance' => 'debit'],
            ['account_code' => '1040', 'account_name' => 'WIP Construction Asset Inventory', 'account_type' => 'asset', 'normal_balance' => 'debit'],
            ['account_code' => '1050', 'account_name' => 'Finished Unsold Property Inventory', 'account_type' => 'asset', 'normal_balance' => 'debit'],
            ['account_code' => '1060', 'account_name' => 'Advances Paid to Landowners (Signing Money)', 'account_type' => 'asset', 'normal_balance' => 'debit'],

            // Liabilities (2000s)
            ['account_code' => '2010', 'account_name' => 'Accounts Payable (Material Vendors & Subcontractors)', 'account_type' => 'liability', 'normal_balance' => 'credit'],
            ['account_code' => '2020', 'account_name' => 'Customer Advance Booking Deposits', 'account_type' => 'liability', 'normal_balance' => 'credit'],
            ['account_code' => '2030', 'account_name' => 'Tenant Caution & Security Deposits Held', 'account_type' => 'liability', 'normal_balance' => 'credit'],
            ['account_code' => '2040', 'account_name' => 'VAT & Withholding Tax Payable', 'account_type' => 'liability', 'normal_balance' => 'credit'],

            // Equity (3000s)
            ['account_code' => '3010', 'account_name' => 'Paid-up Share Capital', 'account_type' => 'equity', 'normal_balance' => 'credit'],
            ['account_code' => '3020', 'account_name' => 'Retained Earnings', 'account_type' => 'equity', 'normal_balance' => 'credit'],

            // Revenue (4000s)
            ['account_code' => '4010', 'account_name' => 'Developer Apartment Sales Revenue', 'account_type' => 'revenue', 'normal_balance' => 'credit'],
            ['account_code' => '4020', 'account_name' => 'Dedicated Parking Bay Sales Revenue', 'account_type' => 'revenue', 'normal_balance' => 'credit'],
            ['account_code' => '4030', 'account_name' => 'Brokerage & Agency Commission Revenue', 'account_type' => 'revenue', 'normal_balance' => 'credit'],
            ['account_code' => '4040', 'account_name' => 'Property Rental & Lease Revenue', 'account_type' => 'revenue', 'normal_balance' => 'credit'],

            // Expenses (5000s)
            ['account_code' => '5010', 'account_name' => 'Direct Construction Material & Civil Costs', 'account_type' => 'expense', 'normal_balance' => 'debit'],
            ['account_code' => '5020', 'account_name' => 'Subcontractor & Daily Labor Wages', 'account_type' => 'expense', 'normal_balance' => 'debit'],
            ['account_code' => '5030', 'account_name' => 'Land Acquisition & Legal Vetting Fees', 'account_type' => 'expense', 'normal_balance' => 'debit'],
            ['account_code' => '5040', 'account_name' => 'Sales Commission & Agent Incentive Expense', 'account_type' => 'expense', 'normal_balance' => 'debit'],
            ['account_code' => '5050', 'account_name' => 'Executive Salaries & Administrative Overhead', 'account_type' => 'expense', 'normal_balance' => 'debit'],
        ];

        foreach ($companies as $comp) {
            foreach ($standardCoa as $item) {
                AccChartOfAccount::firstOrCreate(
                    [
                        'company_id'   => $comp->id,
                        'account_code' => $item['account_code'],
                    ],
                    [
                        'account_name'      => $item['account_name'],
                        'account_type'      => $item['account_type'],
                        'normal_balance'    => $item['normal_balance'],
                        'is_system_locked'  => true,
                        'status'            => 'active',
                    ]
                );
            }
        }
    }
}
