<?php

namespace App\Services\ERP;

use App\Models\Admin;
use App\Models\ERP\Account;
use App\Models\ERP\Employee;
use App\Models\ERP\PayrollItem;
use App\Models\ERP\PayrollRun;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollService
{
    public function __construct(
        protected AccountingService $accountingService
    ) {}

    /**
     * Generate monthly payroll run for all active employees.
     */
    public function generatePayroll(int $companyId, int $year, int $month, ?Admin $actor = null): PayrollRun
    {
        return DB::transaction(function () use ($companyId, $year, $month, $actor) {
            $existing = PayrollRun::where('company_id', $companyId)
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            if ($existing) {
                throw ValidationException::withMessages([
                    'month' => "Payroll for {$year}-{$month} has already been generated.",
                ]);
            }

            $employees = Employee::where('company_id', $companyId)
                ->where('status', 'active')
                ->get();

            if ($employees->isEmpty()) {
                throw ValidationException::withMessages([
                    'employees' => 'No active employees found to generate payroll.',
                ]);
            }

            $monthName = date('F', mktime(0, 0, 0, $month, 10));
            $title = "Payroll for {$monthName} {$year}";

            $payrollRun = PayrollRun::create([
                'company_id' => $companyId,
                'year' => $year,
                'month' => $month,
                'title' => $title,
                'total_gross' => 0,
                'total_deductions' => 0,
                'total_net' => 0,
                'status' => 'approved',
                'created_by' => $actor?->id,
                'approved_by' => $actor?->id,
            ]);

            $totalGross = 0;
            $totalDeductions = 0;
            $totalNet = 0;

            foreach ($employees as $emp) {
                $basic = (float) $emp->basic_salary;
                $allowances = (float) $emp->house_rent + (float) $emp->medical_allowance + (float) $emp->other_allowance;
                $deductions = (float) $emp->tax_deduction + (float) $emp->provident_fund;
                $net = max(0, ($basic + $allowances) - $deductions);

                PayrollItem::create([
                    'payroll_run_id' => $payrollRun->id,
                    'employee_id' => $emp->id,
                    'basic_salary' => $basic,
                    'allowances' => $allowances,
                    'deductions' => $deductions,
                    'net_salary' => $net,
                    'payment_status' => 'paid',
                    'payment_method' => 'bank_transfer',
                ]);

                $totalGross += ($basic + $allowances);
                $totalDeductions += $deductions;
                $totalNet += $net;
            }

            $payrollRun->update([
                'total_gross' => $totalGross,
                'total_deductions' => $totalDeductions,
                'total_net' => $totalNet,
            ]);

            // Post Salary Expense in Accounting: Dr Salaries Expense (6100), Cr Bank Account (1020)
            $salaryExpenseAcc = Account::where('company_id', $companyId)->where('code', '6100')->first();
            $bankAcc = Account::where('company_id', $companyId)->where('code', '1020')->first();

            if ($salaryExpenseAcc && $bankAcc) {
                $journal = $this->accountingService->createJournal([
                    'company_id' => $companyId,
                    'date' => now()->toDateString(),
                    'voucher_type' => 'payment',
                    'reference' => "PR-{$year}{$month}",
                    'narration' => "Salary & compensation disbursement for {$monthName} {$year}",
                    'posted_by' => $actor?->id,
                ], [
                    [
                        'account_id' => $salaryExpenseAcc->id,
                        'debit' => $totalNet,
                        'credit' => 0,
                        'description' => "Monthly staff salary for {$monthName} {$year}",
                    ],
                    [
                        'account_id' => $bankAcc->id,
                        'debit' => 0,
                        'credit' => $totalNet,
                        'description' => "Salary payment disbursement via Bank",
                    ],
                ]);

                $payrollRun->update(['journal_id' => $journal->id]);
            }

            return $payrollRun;
        });
    }
}

