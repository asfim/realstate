<?php

namespace App\Services\ERP;

use App\Models\ERP\Account;
use App\Models\ERP\Journal;
use App\Models\ERP\JournalLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountingService
{
    /**
     * Create and post a balanced double-entry journal voucher.
     * Enforces Debits = Credits strictly.
     */
    public function createJournal(array $journalData, array $lines): Journal
    {
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($lines as $line) {
            $totalDebit += (float) ($line['debit'] ?? 0);
            $totalCredit += (float) ($line['credit'] ?? 0);
        }

        if (bccomp((string)$totalDebit, (string)$totalCredit, 2) !== 0) {
            throw ValidationException::withMessages([
                'journal' => "Journal lines do not balance! Total Debit: ৳" . number_format($totalDebit, 2) . ", Total Credit: ৳" . number_format($totalCredit, 2),
            ]);
        }

        return DB::transaction(function () use ($journalData, $lines, $totalDebit, $totalCredit) {
            $companyId = $journalData['company_id'];

            if (empty($journalData['journal_no'])) {
                $prefix = match($journalData['voucher_type'] ?? 'journal') {
                    'receipt' => 'RV',
                    'payment' => 'PV',
                    'sales' => 'SV',
                    'contra' => 'CV',
                    default => 'JV'
                };
                $count = Journal::where('company_id', $companyId)->count() + 1;
                $journalData['journal_no'] = sprintf('%s-%s-%04d', $prefix, date('Ym'), $count);
            }

            $journalData['total_debit'] = $totalDebit;
            $journalData['total_credit'] = $totalCredit;
            $journalData['status'] = 'posted';
            $journalData['posted_at'] = now();

            $journal = Journal::create($journalData);

            foreach ($lines as $lineData) {
                $lineData['journal_id'] = $journal->id;
                JournalLine::create($lineData);

                // Update Account current balance
                $account = Account::find($lineData['account_id']);
                if ($account) {
                    $debit = (float)($lineData['debit'] ?? 0);
                    $credit = (float)($lineData['credit'] ?? 0);

                    if ($account->isDebitNormal()) {
                        $account->current_balance += ($debit - $credit);
                    } else {
                        $account->current_balance += ($credit - $debit);
                    }
                    $account->save();
                }
            }

            return $journal->load('lines.account');
        });
    }

    /**
     * Compute Profit and Loss (P&L) Statement.
     */
    public function getProfitAndLoss(int $companyId, ?string $startDate = null, ?string $endDate = null, ?int $projectId = null): array
    {
        $revenueAccounts = Account::where('company_id', $companyId)
            ->where('type', 'revenue')
            ->where('is_active', true)
            ->with(['journalLines' => function ($q) use ($startDate, $endDate, $projectId) {
                $q->whereHas('journal', function ($j) use ($startDate, $endDate, $projectId) {
                    $j->where('status', 'posted');
                    if ($startDate) $j->where('date', '>=', $startDate);
                    if ($endDate) $j->where('date', '<=', $endDate);
                    if ($projectId) $j->where('project_id', $projectId);
                });
            }])
            ->get();

        $expenseAccounts = Account::where('company_id', $companyId)
            ->where('type', 'expense')
            ->where('is_active', true)
            ->with(['journalLines' => function ($q) use ($startDate, $endDate, $projectId) {
                $q->whereHas('journal', function ($j) use ($startDate, $endDate, $projectId) {
                    $j->where('status', 'posted');
                    if ($startDate) $j->where('date', '>=', $startDate);
                    if ($endDate) $j->where('date', '<=', $endDate);
                    if ($projectId) $j->where('project_id', $projectId);
                });
            }])
            ->get();

        $revenues = [];
        $totalRevenue = 0;
        foreach ($revenueAccounts as $acc) {
            $net = $acc->journalLines->sum('credit') - $acc->journalLines->sum('debit');
            if ($net != 0 || $acc->is_system) {
                $revenues[] = [
                    'code' => $acc->code,
                    'name' => $acc->name,
                    'amount' => $net,
                ];
                $totalRevenue += $net;
            }
        }

        $directCosts = [];
        $totalDirectCost = 0;
        $operatingExpenses = [];
        $totalOperatingExpense = 0;

        foreach ($expenseAccounts as $acc) {
            $net = $acc->journalLines->sum('debit') - $acc->journalLines->sum('credit');
            if ($net != 0 || $acc->is_system) {
                // Check if account is direct construction / development cost or operational
                if (str_starts_with($acc->code, '5')) {
                    $directCosts[] = [
                        'code' => $acc->code,
                        'name' => $acc->name,
                        'amount' => $net,
                    ];
                    $totalDirectCost += $net;
                } else {
                    $operatingExpenses[] = [
                        'code' => $acc->code,
                        'name' => $acc->name,
                        'amount' => $net,
                    ];
                    $totalOperatingExpense += $net;
                }
            }
        }

        $grossProfit = $totalRevenue - $totalDirectCost;
        $netProfit = $grossProfit - $totalOperatingExpense;

        return [
            'revenues' => $revenues,
            'total_revenue' => $totalRevenue,
            'direct_costs' => $directCosts,
            'total_direct_cost' => $totalDirectCost,
            'gross_profit' => $grossProfit,
            'operating_expenses' => $operatingExpenses,
            'total_operating_expense' => $totalOperatingExpense,
            'net_profit' => $netProfit,
        ];
    }

    /**
     * Compute Trial Balance.
     */
    public function getTrialBalance(int $companyId): array
    {
        $accounts = Account::where('company_id', $companyId)
            ->where('is_active', true)
            ->with(['journalLines' => function ($q) {
                $q->whereHas('journal', function ($j) {
                    $j->where('status', 'posted');
                });
            }])
            ->orderBy('code')
            ->get();

        $rows = [];
        $totalDebitSum = 0;
        $totalCreditSum = 0;

        foreach ($accounts as $acc) {
            $debit = (float)$acc->journalLines->sum('debit');
            $credit = (float)$acc->journalLines->sum('credit');

            if ($acc->isDebitNormal()) {
                $balance = (float)$acc->opening_balance + ($debit - $credit);
                $dr = $balance > 0 ? $balance : 0;
                $cr = $balance < 0 ? abs($balance) : 0;
            } else {
                $balance = (float)$acc->opening_balance + ($credit - $debit);
                $cr = $balance > 0 ? $balance : 0;
                $dr = $balance < 0 ? abs($balance) : 0;
            }

            if ($dr != 0 || $cr != 0 || $acc->is_system) {
                $rows[] = [
                    'code' => $acc->code,
                    'name' => $acc->name,
                    'type' => $acc->type,
                    'debit' => $dr,
                    'credit' => $cr,
                ];
                $totalDebitSum += $dr;
                $totalCreditSum += $cr;
            }
        }

        return [
            'rows' => $rows,
            'total_debit' => $totalDebitSum,
            'total_credit' => $totalCreditSum,
            'is_balanced' => bccomp((string)$totalDebitSum, (string)$totalCreditSum, 2) === 0,
        ];
    }

    /**
     * Compute Balance Sheet (Assets = Liabilities + Equity).
     */
    public function getBalanceSheet(int $companyId): array
    {
        $trialBalance = $this->getTrialBalance($companyId);
        $pnl = $this->getProfitAndLoss($companyId);

        $assets = [];
        $totalAssets = 0;
        $liabilities = [];
        $totalLiabilities = 0;
        $equity = [];
        $totalEquity = 0;

        foreach ($trialBalance['rows'] as $row) {
            if ($row['type'] === 'asset') {
                $val = $row['debit'] - $row['credit'];
                $assets[] = ['code' => $row['code'], 'name' => $row['name'], 'amount' => $val];
                $totalAssets += $val;
            } elseif ($row['type'] === 'liability') {
                $val = $row['credit'] - $row['debit'];
                $liabilities[] = ['code' => $row['code'], 'name' => $row['name'], 'amount' => $val];
                $totalLiabilities += $val;
            } elseif ($row['type'] === 'equity') {
                $val = $row['credit'] - $row['debit'];
                $equity[] = ['code' => $row['code'], 'name' => $row['name'], 'amount' => $val];
                $totalEquity += $val;
            }
        }

        // Add Current Period Net Profit into Equity
        $currentEarnings = $pnl['net_profit'];
        $equity[] = [
            'code' => '3999',
            'name' => 'Current Period Net Profit / (Loss)',
            'amount' => $currentEarnings,
        ];
        $totalEquity += $currentEarnings;

        return [
            'assets' => $assets,
            'total_assets' => $totalAssets,
            'liabilities' => $liabilities,
            'total_liabilities' => $totalLiabilities,
            'equity' => $equity,
            'total_equity' => $totalEquity,
            'total_liabilities_and_equity' => $totalLiabilities + $totalEquity,
            'is_balanced' => bccomp((string)$totalAssets, (string)($totalLiabilities + $totalEquity), 2) === 0,
        ];
    }
}
