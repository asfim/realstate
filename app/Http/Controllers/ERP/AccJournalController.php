<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\AccChartOfAccount;
use App\Models\ERP\AccJournal;
use App\Models\ERP\AccJournalEntry;
use App\Models\ERP\CostCenter;
use App\Models\ERP\FiscalYear;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccJournalController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected DocumentSequenceService $sequenceService,
        protected AuditLogService $auditLog
    ) {
    }

    public function index(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = AccJournal::where('company_id', $currentCompany?->id)
            ->with(['branch', 'fiscalYear', 'creator', 'entries.account'])
            ->latest('entry_date');

        if ($request->filled('voucher_type')) {
            $query->where('voucher_type', $request->voucher_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('voucher_code', 'like', "%{$search}%")
                    ->orWhere('narration', 'like', "%{$search}%");
            });
        }

        $journals = $query->paginate(15)->withQueryString();

        return view('erp.accounting.journals.index', compact('journals'));
    }

    public function create()
    {
        $currentCompany = $this->context->currentCompany();
        $accounts = AccChartOfAccount::where('company_id', $currentCompany?->id)->where('status', 'active')->orderBy('account_code')->get();
        $costCenters = CostCenter::where('company_id', $currentCompany?->id)->where('status', 'active')->get();
        $activeFy = FiscalYear::where('company_id', $currentCompany?->id)->where('is_current', true)->first();

        return view('erp.accounting.journals.create', compact('accounts', 'costCenters', 'activeFy'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'voucher_type'           => 'required|string|in:JV,CPV,CRV,BPV,BRV',
            'entry_date'             => 'required|date',
            'narration'              => 'required|string|max:1000',
            'lines'                  => 'required|array|min:2',
            'lines.*.account_id'     => 'required|exists:acc_chart_of_accounts,id',
            'lines.*.cost_center_id' => 'nullable|exists:cost_centers,id',
            'lines.*.debit'          => 'nullable|numeric|min:0',
            'lines.*.credit'         => 'nullable|numeric|min:0',
            'lines.*.entry_narration'=> 'nullable|string',
        ]);

        // Find active fiscal year
        $fy = FiscalYear::where('company_id', $currentCompany->id)
            ->where('start_date', '<=', $validated['entry_date'])
            ->where('end_date', '>=', $validated['entry_date'])
            ->first();

        if ($fy && $fy->is_closed) {
            return back()->withInput()->with('error', "Fiscal Period {$fy->name} is closed. Transactions cannot be posted.");
        }

        // Validate Double-Entry Balance: Sum Debits == Sum Credits
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($validated['lines'] as $line) {
            $totalDebit += (float) ($line['debit'] ?? 0);
            $totalCredit += (float) ($line['credit'] ?? 0);
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->with('error', "Double-entry out of balance! Total Debits (৳" . number_format($totalDebit, 2) . ") must exactly equal Total Credits (৳" . number_format($totalCredit, 2) . ").");
        }

        if ($totalDebit <= 0) {
            return back()->withInput()->with('error', "Voucher total must be greater than zero.");
        }

        return DB::transaction(function () use ($validated, $currentCompany, $currentBranch, $fy, $totalDebit) {
            $voucherCode = $this->sequenceService->getNextNumber('journal', $currentCompany->id, $currentBranch?->id);

            $journal = AccJournal::create([
                'company_id'     => $currentCompany->id,
                'branch_id'      => $currentBranch?->id,
                'fiscal_year_id' => $fy?->id,
                'voucher_code'   => $voucherCode,
                'voucher_type'   => $validated['voucher_type'],
                'entry_date'     => $validated['entry_date'],
                'total_amount'   => $totalDebit,
                'narration'      => $validated['narration'],
                'status'         => 'posted',
                'created_by'     => auth('admin')->id(),
            ]);

            foreach ($validated['lines'] as $line) {
                $dr = (float) ($line['debit'] ?? 0);
                $cr = (float) ($line['credit'] ?? 0);

                if ($dr > 0 || $cr > 0) {
                    AccJournalEntry::create([
                        'journal_id'      => $journal->id,
                        'account_id'      => $line['account_id'],
                        'cost_center_id'  => $line['cost_center_id'] ?? null,
                        'debit'           => $dr,
                        'credit'          => $cr,
                        'entry_narration' => $line['entry_narration'] ?? null,
                    ]);
                }
            }

            $this->auditLog->log('acc.journal_posted', $journal, null, $journal->toArray(), $currentCompany->id);

            return redirect()->route('erp.acc-journals.show', $journal)->with('success', "Journal Voucher {$journal->voucher_code} posted successfully.");
        });
    }

    public function show(AccJournal $accJournal)
    {
        $accJournal->load(['branch', 'fiscalYear', 'creator', 'entries.account', 'entries.costCenter']);
        return view('erp.accounting.journals.show', compact('accJournal'));
    }
}

