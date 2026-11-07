<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Company;
use App\Models\ERP\FiscalYear;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class FiscalYearController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    public function index()
    {
        $currentCompany = $this->resolveCompany();
        $fiscalYears = FiscalYear::where('company_id', $currentCompany?->id)
            ->latest('start_date')
            ->paginate(15);

        return view('erp.organization.fiscal_years.index', compact('fiscalYears'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->resolveCompany(true);
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'status'     => 'required|in:active,closed',
            'is_current' => 'nullable|boolean',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $companyId = $validated['company_id'] ?? $currentCompany->id;
        $validated['company_id'] = $companyId;
        $validated['is_current'] = $request->boolean('is_current');

        if ($validated['is_current']) {
            FiscalYear::where('company_id', $companyId)->update(['is_current' => false]);
        }

        $fy = FiscalYear::create($validated);
        $this->auditLog->log('fiscal_year.created', $fy, null, $fy->toArray(), $companyId);

        return redirect()->route('erp.fiscal-years.index')->with('success', 'Fiscal Year created successfully.');
    }

    /**
     * Resolve company with automatic provisioning fallback.
     */
    protected function resolveCompany(bool $forceCreate = false): ?Company
    {
        $company = $this->context->currentCompany();
        if ($company) {
            return $company;
        }

        $company = Company::where('status', 'active')->first() ?? Company::first();
        if ($company) {
            $this->context->setCompany($company);
            return $company;
        }

        if ($forceCreate || Company::count() === 0) {
            try {
                $company = Company::firstOrCreate(
                    ['code' => 'AURELIA'],
                    [
                        'legal_name'        => 'Aurelia Estates & Developers Ltd.',
                        'trade_name'        => 'Aurelia Living',
                        'registration_no'   => 'C-198425/2026',
                        'bin'               => '004829103-0101',
                        'tin'               => '491827401928',
                        'base_currency'     => 'BDT',
                        'fiscal_year_start' => '07-01',
                        'timezone'          => 'Asia/Dhaka',
                        'status'            => 'active',
                    ]
                );
                $this->context->setCompany($company);
                return $company;
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    public function update(Request $request, FiscalYear $fiscalYear)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'status'     => 'required|in:active,closed',
            'is_current' => 'nullable|boolean',
        ]);

        $validated['is_current'] = $request->boolean('is_current');

        if ($validated['is_current']) {
            FiscalYear::where('company_id', $fiscalYear->company_id)
                ->where('id', '!=', $fiscalYear->id)
                ->update(['is_current' => false]);
        }

        $before = $fiscalYear->toArray();
        $fiscalYear->update($validated);
        $this->auditLog->log('fiscal_year.updated', $fiscalYear, $before, $fiscalYear->toArray(), $fiscalYear->company_id);

        return redirect()->route('erp.fiscal-years.index')->with('success', 'Fiscal Year updated successfully.');
    }

    public function destroy(FiscalYear $fiscalYear)
    {
        if ($fiscalYear->is_current) {
            return back()->with('error', 'Cannot delete active current fiscal year.');
        }

        $before = $fiscalYear->toArray();
        $fiscalYear->delete();
        $this->auditLog->log('fiscal_year.deleted', null, $before, null, $fiscalYear->company_id);

        return redirect()->route('erp.fiscal-years.index')->with('success', 'Fiscal Year deleted successfully.');
    }
}

