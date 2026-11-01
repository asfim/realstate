<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Account;
use App\Models\ERP\Company;
use App\Models\ERP\Customer;
use App\Models\ERP\FiscalPeriod;
use App\Models\ERP\Journal;
use App\Models\ERP\Project;
use App\Services\ERP\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingController extends Controller
{
    public function __construct(
        protected AccountingService $accountingService
    ) {}

    /**
     * Chart of Accounts
     */
    public function accounts(Request $request): View
    {
        $companyId = $request->input('company_id', 1);
        $accounts = Account::where('company_id', $companyId)->orderBy('code')->get();

        $accountsByType = [
            'asset' => $accounts->where('type', 'asset'),
            'liability' => $accounts->where('type', 'liability'),
            'equity' => $accounts->where('type', 'equity'),
            'revenue' => $accounts->where('type', 'revenue'),
            'expense' => $accounts->where('type', 'expense'),
        ];

        return view('erp.accounting.accounts', compact('accounts', 'accountsByType'));
    }

    /**
     * Store new account in COA
     */
    public function storeAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:erp_companies,id'],
            'code' => ['required', 'string', 'max:50', 'unique:erp_accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'opening_balance' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['current_balance'] = $validated['opening_balance'] ?? 0;
        Account::create($validated);

        return back()->with('success', 'Account added to Chart of Accounts.');
    }

    /**
     * Journal Vouchers List
     */
    public function journals(Request $request): View
    {
        $companyId = $request->input('company_id', 1);

        $query = Journal::where('company_id', $companyId)->with(['lines.account', 'project']);

        if ($request->filled('voucher_type')) {
            $query->where('voucher_type', $request->string('voucher_type'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('journal_no', 'like', "%{$search}%")
                  ->orWhere('narration', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $journals = $query->latest('date')->paginate(15)->withQueryString();

        return view('erp.accounting.journals', compact('journals'));
    }

    /**
     * Show create journal form
     */
    public function createJournal(Request $request): View
    {
        $companyId = $request->input('company_id', 1);
        $accounts = Account::where('company_id', $companyId)->where('is_active', true)->orderBy('code')->get();
        $projects = Project::where('company_id', $companyId)->where('is_active', true)->get();

        return view('erp.accounting.create_journal', compact('accounts', 'projects'));
    }

    /**
     * Store manual journal entry
     */
    public function storeJournal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:erp_companies,id'],
            'date' => ['required', 'date'],
            'voucher_type' => ['required', 'in:journal,receipt,payment,sales,contra'],
            'project_id' => ['nullable', 'exists:erp_projects,id'],
            'reference' => ['nullable', 'string', 'max:100'],
            'narration' => ['required', 'string', 'max:500'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:erp_accounts,id'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.description' => ['nullable', 'string'],
        ]);

        $this->accountingService->createJournal([
            'company_id' => $validated['company_id'],
            'project_id' => $validated['project_id'] ?? null,
            'date' => $validated['date'],
            'voucher_type' => $validated['voucher_type'],
            'reference' => $validated['reference'] ?? null,
            'narration' => $validated['narration'],
            'posted_by' => auth('admin')->id(),
        ], $validated['lines']);

        return redirect()->route('erp.accounting.journals')
            ->with('success', 'Balanced journal entry posted successfully.');
    }

    /**
     * Profit & Loss (P&L) Statement
     */
    public function profitAndLoss(Request $request): View
    {
        $companyId = $request->input('company_id', 1);
        $company = Company::find($companyId) ?? Company::first();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $projectId = $request->input('project_id');

        $projects = Project::where('company_id', $company?->id)->get();
        $pnl = $this->accountingService->getProfitAndLoss($company?->id ?? 1, $startDate, $endDate, $projectId);

        return view('erp.accounting.profit_and_loss', compact('company', 'pnl', 'projects', 'startDate', 'endDate', 'projectId'));
    }

    /**
     * Trial Balance
     */
    public function trialBalance(Request $request): View
    {
        $companyId = $request->input('company_id', 1);
        $company = Company::find($companyId) ?? Company::first();

        $trialBalance = $this->accountingService->getTrialBalance($company?->id ?? 1);

        return view('erp.accounting.trial_balance', compact('company', 'trialBalance'));
    }

    /**
     * Balance Sheet
     */
    public function balanceSheet(Request $request): View
    {
        $companyId = $request->input('company_id', 1);
        $company = Company::find($companyId) ?? Company::first();

        $balanceSheet = $this->accountingService->getBalanceSheet($company?->id ?? 1);

        return view('erp.accounting.balance_sheet', compact('company', 'balanceSheet'));
    }

    /**
     * Customer Subledger
     */
    public function customerLedger(Request $request): View
    {
        $companyId = $request->input('company_id', 1);
        $customerId = $request->input('customer_id');

        $customers = Customer::where('company_id', $companyId)->get();
        $selectedCustomer = $customerId ? Customer::with(['bookings.unit', 'bookings.payments'])->find($customerId) : null;

        return view('erp.accounting.customer_ledger', compact('customers', 'selectedCustomer'));
    }
}

