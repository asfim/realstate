<?php

namespace Database\Seeders;

use App\Models\AccountCategory;
use Illuminate\Database\Seeder;

class AccountCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incomes = [
            'Property Sales Commission',
            'Booking Money',
            'Installment Collection',
            'Rental Income',
            'Parking Space Allotment',
            'Consultation Fees',
            'Transfer & Documentation Fees',
            'Service Charges',
            'Other Income',
        ];

        foreach ($incomes as $name) {
            AccountCategory::firstOrCreate(
                ['name' => $name, 'type' => 'income'],
                ['is_active' => true, 'description' => $name . ' for real estate operations']
            );
        }

        $expenses = [
            'Office Rent',
            'Marketing & Advertising',
            'Staff Salary & Payroll',
            'Direct Construction & Materials',
            'Site Contractor & Labor Wages',
            'Utility Bills & Internet',
            'Legal & Documentation Fees',
            'Maintenance & Repairs',
            'Architecture & Soil Testing Consultancy',
            'Office Supplies & Stationery',
            'Travel & Site Conveyance',
            'IT & Software Subscriptions',
            'Other Expense',
        ];

        foreach ($expenses as $name) {
            AccountCategory::firstOrCreate(
                ['name' => $name, 'type' => 'expense'],
                ['is_active' => true, 'description' => $name . ' expenses']
            );
        }
    }
}

