<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\AccChartOfAccount;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class AccCoaController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    public function index(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = AccChartOfAccount::where('company_id', $currentCompany?->id)->orderBy('account_code');

        if ($request->filled('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_code', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%");
            });
        }

        $accounts = $query->get();

        $accountsByType = [
            'asset'     => $accounts->where('account_type', 'asset'),
            'liability' => $accounts->where('account_type', 'liability'),
            'equity'    => $accounts->where('account_type', 'equity'),
            'revenue'   => $accounts->where('account_type', 'revenue'),
            'expense'   => $accounts->where('account_type', 'expense'),
        ];

        return view('erp.accounting.coa.index', compact('accounts', 'accountsByType'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();

        $validated = $request->validate([
            'account_code'   => 'required|string|max:50|unique:acc_chart_of_accounts,account_code,NULL,id,company_id,' . $currentCompany->id,
            'account_name'   => 'required|string|max:255',
            'account_type'   => 'required|string|in:asset,liability,equity,revenue,expense',
            'normal_balance' => 'required|string|in:debit,credit',
            'description'    => 'nullable|string',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $account = AccChartOfAccount::create($validated);

        $this->auditLog->log('acc.coa_account_created', $account, null, $account->toArray(), $currentCompany->id);

        return back()->with('success', "Account {$account->account_code} - {$account->account_name} created.");
    }
}

