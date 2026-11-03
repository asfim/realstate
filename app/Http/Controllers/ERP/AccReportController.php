<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\AccChartOfAccount;
use App\Models\ERP\AccJournalEntry;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class AccReportController extends Controller
{
    public function __construct(protected CompanyBranchContext $context)
    {
    }

    public function trialBalance(Request $request)
    {
        $currentCompany = $this->context->currentCompany();

        $accounts = AccChartOfAccount::where('company_id', $currentCompany?->id)
            ->with(['journalEntries' => function ($q) use ($request) {
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $q->whereHas('journal', function ($jq) use ($request) {
                        $jq->whereBetween('entry_date', [$request->start_date, $request->end_date]);
                    });
                }
            }])
            ->orderBy('account_code')
            ->get();

        $trialRows = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $acc) {
            $dr = $acc->journalEntries->sum('debit');
            $cr = $acc->journalEntries->sum('credit');

            if ($dr > 0 || $cr > 0) {
                $trialRows[] = [
                    'code'           => $acc->account_code,
                    'name'           => $acc->account_name,
                    'type'           => $acc->account_type,
                    'normal_balance' => $acc->normal_balance,
                    'debit'          => $dr,
                    'credit'         => $cr,
                ];

                $totalDebit += $dr;
                $totalCredit += $cr;
            }
        }

        return view('erp.accounting.reports.trial_balance', compact('trialRows', 'totalDebit', 'totalCredit'));
    }

    public function profitAndLoss(Request $request)
    {
        $currentCompany = $this->context->currentCompany();

        $revenueAccounts = AccChartOfAccount::where('company_id', $currentCompany?->id)
            ->where('account_type', 'revenue')
            ->with('journalEntries')
            ->get();

        $expenseAccounts = AccChartOfAccount::where('company_id', $currentCompany?->id)
            ->where('account_type', 'expense')
            ->with('journalEntries')
            ->get();

        $totalRevenue = 0;
        $revenues = [];
        foreach ($revenueAccounts as $acc) {
            $net = $acc->journalEntries->sum('credit') - $acc->journalEntries->sum('debit');
            $revenues[] = ['code' => $acc->account_code, 'name' => $acc->account_name, 'amount' => $net];
            $totalRevenue += $net;
        }

        $totalExpense = 0;
        $expenses = [];
        foreach ($expenseAccounts as $acc) {
            $net = $acc->journalEntries->sum('debit') - $acc->journalEntries->sum('credit');
            $expenses[] = ['code' => $acc->account_code, 'name' => $acc->account_name, 'amount' => $net];
            $totalExpense += $net;
        }

        $netProfit = $totalRevenue - $totalExpense;

        return view('erp.accounting.reports.profit_loss', compact('revenues', 'expenses', 'totalRevenue', 'totalExpense', 'netProfit'));
    }

    public function balanceSheet(Request $request)
    {
        $currentCompany = $this->context->currentCompany();

        $assetAccounts = AccChartOfAccount::where('company_id', $currentCompany?->id)->where('account_type', 'asset')->with('journalEntries')->get();
        $liabilityAccounts = AccChartOfAccount::where('company_id', $currentCompany?->id)->where('account_type', 'liability')->with('journalEntries')->get();
        $equityAccounts = AccChartOfAccount::where('company_id', $currentCompany?->id)->where('account_type', 'equity')->with('journalEntries')->get();

        $totalAssets = 0;
        $assets = [];
        foreach ($assetAccounts as $acc) {
            $bal = $acc->journalEntries->sum('debit') - $acc->journalEntries->sum('credit');
            $assets[] = ['code' => $acc->account_code, 'name' => $acc->account_name, 'amount' => $bal];
            $totalAssets += $bal;
        }

        $totalLiabilities = 0;
        $liabilities = [];
        foreach ($liabilityAccounts as $acc) {
            $bal = $acc->journalEntries->sum('credit') - $acc->journalEntries->sum('debit');
            $liabilities[] = ['code' => $acc->account_code, 'name' => $acc->account_name, 'amount' => $bal];
            $totalLiabilities += $bal;
        }

        $totalEquity = 0;
        $equities = [];
        foreach ($equityAccounts as $acc) {
            $bal = $acc->journalEntries->sum('credit') - $acc->journalEntries->sum('debit');
            $equities[] = ['code' => $acc->account_code, 'name' => $acc->account_name, 'amount' => $bal];
            $totalEquity += $bal;
        }

        return view('erp.accounting.reports.balance_sheet', compact('assets', 'liabilities', 'equities', 'totalAssets', 'totalLiabilities', 'totalEquity'));
    }
}
