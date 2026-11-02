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
            'Rental Income',
            'Consultation Fees',
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
            'Utility Bills & Internet',
            'Legal & Documentation',
            'Maintenance & Repairs',
            'Office Supplies',
            'Travel & Conveyance',
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

