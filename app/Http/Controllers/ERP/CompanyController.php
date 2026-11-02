<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Company;
use App\Services\ERP\AuditLogService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(protected AuditLogService $auditLog)
    {
    }

    public function index()
    {
        $companies = Company::withCount(['branches', 'departments', 'costCenters'])->latest()->paginate(15);
        return view('erp.organization.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('erp.organization.companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'              => 'required|string|max:50|unique:companies,code',
            'legal_name'        => 'required|string|max:255',
            'trade_name'        => 'nullable|string|max:255',
            'registration_no'   => 'nullable|string|max:100',
            'bin'               => 'nullable|string|max:100',
            'tin'               => 'nullable|string|max:100',
            'base_currency'     => 'required|string|max:10',
            'fiscal_year_start' => 'required|string|max:10',
            'timezone'          => 'required|string|max:50',
            'status'            => 'required|in:active,inactive',
        ]);

        $company = Company::create($validated);
        $this->auditLog->log('company.created', $company, null, $company->toArray(), $company->id);

        return redirect()->route('erp.companies.index')->with('success', 'Company created successfully.');
    }

    public function edit(Company $company)
    {
        return view('erp.organization.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'code'              => 'required|string|max:50|unique:companies,code,' . $company->id,
            'legal_name'        => 'required|string|max:255',
            'trade_name'        => 'nullable|string|max:255',
            'registration_no'   => 'nullable|string|max:100',
            'bin'               => 'nullable|string|max:100',
            'tin'               => 'nullable|string|max:100',
            'base_currency'     => 'required|string|max:10',
            'fiscal_year_start' => 'required|string|max:10',
            'timezone'          => 'required|string|max:50',
            'status'            => 'required|in:active,inactive',
        ]);

        $before = $company->toArray();
        $company->update($validated);
        $this->auditLog->log('company.updated', $company, $before, $company->toArray(), $company->id);

        return redirect()->route('erp.companies.index')->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        if ($company->branches()->count() > 0) {
            return back()->with('error', 'Cannot delete company with associated branches.');
        }

        $before = $company->toArray();
        $company->delete();
        $this->auditLog->log('company.deleted', null, $before, null, $company->id);

        return redirect()->route('erp.companies.index')->with('success', 'Company deleted successfully.');
    }
}
