<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Payroll;
use App\Models\AccountCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        // Total Incomes
        $totalIncome = Transaction::income()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // Total Expenses
        $totalExpense = Transaction::expense()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // Total Payroll (Paid)
        $totalPayrollPaid = Payroll::where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('net_salary');

        // Net Balance
        $netBalance = $totalIncome - $totalExpense;

        // Lifetime stats
        $lifetimeIncome = Transaction::income()->sum('amount');
        $lifetimeExpense = Transaction::expense()->sum('amount');
        $lifetimePayroll = Payroll::where('status', 'paid')->sum('net_salary');
        $lifetimeBalance = $lifetimeIncome - $lifetimeExpense;

        // Recent Transactions
        $recentTransactions = Transaction::with(['category', 'property', 'creator'])
            ->latest('transaction_date')
            ->latest('id')
            ->take(8)
            ->get();

        // Recent Payrolls
        $recentPayrolls = Payroll::with('teamMember')
            ->latest('payment_date')
            ->latest('id')
            ->take(5)
            ->get();

        // Income by Category
        $incomeByCategory = Transaction::income()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('account_category_id, sum(amount) as total')
            ->groupBy('account_category_id')
            ->with('category')
            ->get();

        // Expense by Category
        $expenseByCategory = Transaction::expense()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('account_category_id, sum(amount) as total')
            ->groupBy('account_category_id')
            ->with('category')
            ->get();

        return view('backend.accounts.index', compact(
            'startDate',
            'endDate',
            'totalIncome',
            'totalExpense',
            'totalPayrollPaid',
            'netBalance',
            'lifetimeIncome',
            'lifetimeExpense',
            'lifetimePayroll',
            'lifetimeBalance',
            'recentTransactions',
            'recentPayrolls',
            'incomeByCategory',
            'expenseByCategory'
        ));
    }
}

