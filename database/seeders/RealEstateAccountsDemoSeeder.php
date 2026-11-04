<?php

namespace Database\Seeders;

use App\Models\AccountCategory;
use App\Models\Admin;
use App\Models\ERP\AccChartOfAccount;
use App\Models\ERP\Company;
use App\Models\Payroll;
use App\Models\Property;
use App\Models\TeamMember;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RealEstateAccountsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Categories first
        $this->call(AccountCategorySeeder::class);

        $admin = Admin::first();
        $properties = Property::all();
        $prop1 = $properties->first();
        $prop2 = $properties->skip(1)->first();

        // 2. Sample Incomes
        $catBooking = AccountCategory::where('name', 'Booking Money')->first();
        $catInstallment = AccountCategory::where('name', 'Installment Collection')->first();
        $catCommission = AccountCategory::where('name', 'Property Sales Commission')->first();
        $catRental = AccountCategory::where('name', 'Rental Income')->first();
        $catParking = AccountCategory::where('name', 'Parking Space Allotment')->first();
        $catConsultation = AccountCategory::where('name', 'Consultation Fees')->first();

        $incomes = [
            [
                'type'                => 'income',
                'account_category_id' => $catBooking?->id,
                'property_id'         => $prop1?->id,
                'title'               => 'Advance Booking Deposit - Aurelia Heights Luxury Suite',
                'amount'              => 2500000.00,
                'transaction_date'    => Carbon::now()->subDays(12)->format('Y-m-d'),
                'payment_method'      => 'bank_transfer',
                'reference_no'        => 'BRAC-TXN-88410',
                'note'                => 'Token booking deposit received via online bank transfer.',
                'created_by'          => $admin?->id,
            ],
            [
                'type'                => 'income',
                'account_category_id' => $catInstallment?->id,
                'property_id'         => $prop1?->id,
                'title'               => '1st Installment Milestone - Tower Suite 4A',
                'amount'              => 1800000.00,
                'transaction_date'    => Carbon::now()->subDays(8)->format('Y-m-d'),
                'payment_method'      => 'cheque',
                'reference_no'        => 'CHQ-009124',
                'note'                => 'Standard construction milestone installment cleared.',
                'created_by'          => $admin?->id,
            ],
            [
                'type'                => 'income',
                'account_category_id' => $catCommission?->id,
                'property_id'         => $prop2?->id,
                'title'               => 'Brokerage Agency Fee - Banani Diplomatic Zone Resale',
                'amount'              => 650000.00,
                'transaction_date'    => Carbon::now()->subDays(5)->format('Y-m-d'),
                'payment_method'      => 'bank_transfer',
                'reference_no'        => 'CITY-TXN-5521',
                'note'                => '2% secondary market sales commission settled.',
                'created_by'          => $admin?->id,
            ],
            [
                'type'                => 'income',
                'account_category_id' => $catRental?->id,
                'property_id'         => $prop2?->id,
                'title'               => 'Commercial Floor Monthly Rental - Floor 6 Corporate Lease',
                'amount'              => 350000.00,
                'transaction_date'    => Carbon::now()->subDays(2)->format('Y-m-d'),
                'payment_method'      => 'bank_transfer',
                'reference_no'        => 'RENT-NOV-2026',
                'note'                => 'Monthly commercial tenant advance rent collected.',
                'created_by'          => $admin?->id,
            ],
            [
                'type'                => 'income',
                'account_category_id' => $catParking?->id,
                'property_id'         => $prop1?->id,
                'title'               => 'Basement Dedicated Parking Bay B-08 Allotment',
                'amount'              => 450000.00,
                'transaction_date'    => Carbon::now()->subDay()->format('Y-m-d'),
                'payment_method'      => 'bank_transfer',
                'reference_no'        => 'PARK-AL-08',
                'note'                => 'Covered parking space allocation charges.',
                'created_by'          => $admin?->id,
            ],
            [
                'type'                => 'income',
                'account_category_id' => $catConsultation?->id,
                'property_id'         => null,
                'title'               => 'Architectural Advisory & Joint Venture Feasibility Consultation',
                'amount'              => 120000.00,
                'transaction_date'    => Carbon::now()->format('Y-m-d'),
                'payment_method'      => 'mobile_banking',
                'reference_no'        => 'BKASH-9X8821',
                'note'                => 'Consultation fee for landowner partnership analysis.',
                'created_by'          => $admin?->id,
            ],
        ];

        foreach ($incomes as $inc) {
            Transaction::firstOrCreate(
                ['reference_no' => $inc['reference_no']],
                $inc
            );
        }

        // 3. Sample Expenses
        $catRent = AccountCategory::where('name', 'Office Rent')->first();
        $catMarketing = AccountCategory::where('name', 'Marketing & Advertising')->first();
        $catLegal = AccountCategory::where('name', 'Legal & Documentation Fees')->first();
        $catUtility = AccountCategory::where('name', 'Utility Bills & Internet')->first();
        $catConsult = AccountCategory::where('name', 'Architecture & Soil Testing Consultancy')->first();
        $catPetty = AccountCategory::where('name', 'Office Supplies & Stationery')->first();

        $expenses = [
            [
                'type'                => 'expense',
                'account_category_id' => $catRent?->id,
                'property_id'         => null,
                'title'               => 'Corporate Head Office Rent - Gulshan Avenue Level 10',
                'amount'              => 320000.00,
                'transaction_date'    => Carbon::now()->subDays(15)->format('Y-m-d'),
                'payment_method'      => 'bank_transfer',
                'reference_no'        => 'RENT-EXP-1102',
                'note'                => 'Monthly commercial lease payment for HQ operations.',
                'created_by'          => $admin?->id,
            ],
            [
                'type'                => 'expense',
                'account_category_id' => $catMarketing?->id,
                'property_id'         => $prop1?->id,
                'title'               => 'Digital Marketing & Facebook/Google Ad Campaign',
                'amount'              => 85000.00,
                'transaction_date'    => Carbon::now()->subDays(10)->format('Y-m-d'),
                'payment_method'      => 'card',
                'reference_no'        => 'META-ADS-2026-11',
                'note'                => 'Luxury housing lead generation digital campaign.',
                'created_by'          => $admin?->id,
            ],
            [
                'type'                => 'expense',
                'account_category_id' => $catConsult?->id,
                'property_id'         => $prop1?->id,
                'title'               => 'Geotechnical Soil Investigation & Structural Vetting',
                'amount'              => 95000.00,
                'transaction_date'    => Carbon::now()->subDays(7)->format('Y-m-d'),
                'payment_method'      => 'bank_transfer',
                'reference_no'        => 'SOIL-SURVEY-99',
                'note'                => 'Borehole soil bearing capacity testing certified report.',
                'created_by'          => $admin?->id,
            ],
            [
                'type'                => 'expense',
                'account_category_id' => $catLegal?->id,
                'property_id'         => null,
                'title'               => 'Supreme Court Search & Land Title Deed Verification',
                'amount'              => 45000.00,
                'transaction_date'    => Carbon::now()->subDays(4)->format('Y-m-d'),
                'payment_method'      => 'cash',
                'reference_no'        => 'LEGAL-VET-001',
                'note'                => 'Legal clearance report from legal counsel.',
                'created_by'          => $admin?->id,
            ],
            [
                'type'                => 'expense',
                'account_category_id' => $catUtility?->id,
                'property_id'         => null,
                'title'               => 'DESCO Commercial Electricity & Optical Fiber Internet',
                'amount'              => 28500.00,
                'transaction_date'    => Carbon::now()->subDays(3)->format('Y-m-d'),
                'payment_method'      => 'mobile_banking',
                'reference_no'        => 'DESCO-WASA-11',
                'note'                => 'Utility bills for office building.',
                'created_by'          => $admin?->id,
            ],
            [
                'type'                => 'expense',
                'account_category_id' => $catPetty?->id,
                'property_id'         => null,
                'title'               => 'Office Stationery & Client Meeting Refreshments',
                'amount'              => 15200.00,
                'transaction_date'    => Carbon::now()->subDay()->format('Y-m-d'),
                'payment_method'      => 'cash',
                'reference_no'        => 'PETTY-11-04',
                'note'                => 'Routine office supplies & admin refreshments.',
                'created_by'          => $admin?->id,
            ],
        ];

        foreach ($expenses as $exp) {
            Transaction::firstOrCreate(
                ['reference_no' => $exp['reference_no']],
                $exp
            );
        }

        // 4. Sample Staff Payroll Records
        $teamMembers = TeamMember::all();
        $tm1 = $teamMembers->first();
        $tm2 = $teamMembers->skip(1)->first();

        $payrolls = [
            [
                'team_member_id' => $tm1?->id,
                'staff_name'     => 'Engr. Tanvir Ahmed',
                'staff_role'     => 'Senior Project Engineer',
                'salary_month'   => Carbon::now()->format('F Y'),
                'basic_salary'   => 85000.00,
                'allowance'      => 15000.00,
                'deduction'      => 3000.00,
                'net_salary'     => 97000.00,
                'payment_date'   => Carbon::now()->subDays(3)->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'status'         => 'paid',
                'note'           => 'Monthly salary transferred to BRAC Bank salary account.',
                'created_by'     => $admin?->id,
            ],
            [
                'team_member_id' => $tm2?->id,
                'staff_name'     => 'Ar. Nusrat Jahan',
                'staff_role'     => 'Principal Architect',
                'salary_month'   => Carbon::now()->format('F Y'),
                'basic_salary'   => 75000.00,
                'allowance'      => 12000.00,
                'deduction'      => 2500.00,
                'net_salary'     => 84500.00,
                'payment_date'   => Carbon::now()->subDays(3)->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'status'         => 'paid',
                'note'           => 'Monthly salary transferred to City Bank salary account.',
                'created_by'     => $admin?->id,
            ],
            [
                'team_member_id' => null,
                'staff_name'     => 'Mahmudul Hasan',
                'staff_role'     => 'Head of Real Estate Sales',
                'salary_month'   => Carbon::now()->format('F Y'),
                'basic_salary'   => 65000.00,
                'allowance'      => 20000.00,
                'deduction'      => 2000.00,
                'net_salary'     => 83000.00,
                'payment_date'   => Carbon::now()->subDays(2)->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'status'         => 'paid',
                'note'           => 'Salary with monthly performance conveyance allowance.',
                'created_by'     => $admin?->id,
            ],
            [
                'team_member_id' => null,
                'staff_name'     => 'Sadia Sultana',
                'staff_role'     => 'Accounts & Compliance Executive',
                'salary_month'   => Carbon::now()->format('F Y'),
                'basic_salary'   => 50000.00,
                'allowance'      => 8000.00,
                'deduction'      => 1500.00,
                'net_salary'     => 56500.00,
                'payment_date'   => Carbon::now()->subDays(2)->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'status'         => 'paid',
                'note'           => 'Disbursed via automated corporate payroll wire.',
                'created_by'     => $admin?->id,
            ],
        ];

        foreach ($payrolls as $pr) {
            Payroll::firstOrCreate(
                ['staff_name' => $pr['staff_name'], 'salary_month' => $pr['salary_month']],
                $pr
            );
        }

        // 5. Enhance ERP Chart of Accounts with specialized real estate bank accounts & overheads
        $company = Company::first();
        if ($company) {
            $extraCoa = [
                ['account_code' => '1021', 'account_name' => 'BRAC Bank Ltd (Corporate A/C - 150120)', 'account_type' => 'asset', 'normal_balance' => 'debit'],
                ['account_code' => '1022', 'account_name' => 'The City Bank Ltd (Principal Branch - 11029)', 'account_type' => 'asset', 'normal_balance' => 'debit'],
                ['account_code' => '1023', 'account_name' => 'Dutch-Bangla Bank Ltd (Corporate - 10512)', 'account_type' => 'asset', 'normal_balance' => 'debit'],
                ['account_code' => '1024', 'account_name' => 'bKash / Nagad Corporate Merchant Account', 'account_type' => 'asset', 'normal_balance' => 'debit'],
                ['account_code' => '1070', 'account_name' => 'Security Deposits with Utilities (DESCO/WASA/TITAS)', 'account_type' => 'asset', 'normal_balance' => 'debit'],
                ['account_code' => '2050', 'account_name' => 'Staff Salaries & Provident Fund Payable', 'account_type' => 'liability', 'normal_balance' => 'credit'],
                ['account_code' => '5060', 'account_name' => 'Corporate Office Rent & Facility Overheads', 'account_type' => 'expense', 'normal_balance' => 'debit'],
                ['account_code' => '5070', 'account_name' => 'Digital Marketing & High-Intent Lead Acquisition', 'account_type' => 'expense', 'normal_balance' => 'debit'],
                ['account_code' => '5080', 'account_name' => 'City Corporation Taxes & Legal Trade Licensing', 'account_type' => 'expense', 'normal_balance' => 'debit'],
            ];

            foreach ($extraCoa as $item) {
                AccChartOfAccount::firstOrCreate(
                    ['company_id' => $company->id, 'account_code' => $item['account_code']],
                    [
                        'account_name'     => $item['account_name'],
                        'account_type'     => $item['account_type'],
                        'normal_balance'   => $item['normal_balance'],
                        'is_system_locked' => false,
                        'status'           => 'active',
                        'description'      => $item['account_name'] . ' for real estate accounting',
                    ]
                );
            }
        }
    }
}
