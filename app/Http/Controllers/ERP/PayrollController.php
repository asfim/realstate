<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\PayrollRun;
use App\Services\ERP\PayrollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {}

    public function index(Request $request): View
    {
        $companyId = $request->input('company_id', 1);

        $payrollRuns = PayrollRun::where('company_id', $companyId)
            ->withCount('items')
            ->latest('year')
            ->latest('month')
            ->paginate(12);

        return view('erp.payroll.index', compact('payrollRuns'));
    }

    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:erp_companies,id'],
            'year' => ['required', 'integer', 'min:2020', 'max:2035'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $payrollRun = $this->payrollService->generatePayroll(
            $validated['company_id'],
            $validated['year'],
            $validated['month'],
            auth('admin')->user()
        );

        return redirect()->route('erp.payroll.show', $payrollRun)
            ->with('success', "Payroll generated and salary journal entry posted to general ledger.");
    }

    public function show(PayrollRun $payrollRun): View
    {
        $payrollRun->load(['items.employee.department', 'journal.lines.account']);

        return view('erp.payroll.show', compact('payrollRun'));
    }
}
