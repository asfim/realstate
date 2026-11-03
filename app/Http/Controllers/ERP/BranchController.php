<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    public function index()
    {
        $currentCompany = $this->context->currentCompany();
        $branches = Branch::where('company_id', $currentCompany?->id)
            ->with(['manager', 'company'])
            ->latest()
            ->paginate(15);

        return view('erp.organization.branches.index', compact('branches'));
    }

    public function create()
    {
        $currentCompany = $this->context->currentCompany();
        $admins = Admin::all();
        return view('erp.organization.branches.create', compact('currentCompany', 'admins'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $validated = $request->validate([
            'code'             => 'required|string|max:50',
            'name'             => 'required|string|max:255',
            'address'          => 'nullable|string',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'manager_admin_id' => 'nullable|exists:admins,id',
            'is_head_office'   => 'nullable|boolean',
            'status'           => 'required|in:active,inactive',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $validated['is_head_office'] = $request->boolean('is_head_office');

        if ($validated['is_head_office']) {
            Branch::where('company_id', $currentCompany->id)->update(['is_head_office' => false]);
        }

        $branch = Branch::create($validated);
        $this->auditLog->log('branch.created', $branch, null, $branch->toArray(), $currentCompany->id, $branch->id);

        return redirect()->route('erp.branches.index')->with('success', 'Branch created successfully.');
    }

    public function edit(Branch $branch)
    {
        $admins = Admin::all();
        return view('erp.organization.branches.edit', compact('branch', 'admins'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'code'             => 'required|string|max:50',
            'name'             => 'required|string|max:255',
            'address'          => 'nullable|string',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'manager_admin_id' => 'nullable|exists:admins,id',
            'is_head_office'   => 'nullable|boolean',
            'status'           => 'required|in:active,inactive',
        ]);

        $validated['is_head_office'] = $request->boolean('is_head_office');

        if ($validated['is_head_office']) {
            Branch::where('company_id', $branch->company_id)
                ->where('id', '!=', $branch->id)
                ->update(['is_head_office' => false]);
        }

        $before = $branch->toArray();
        $branch->update($validated);
        $this->auditLog->log('branch.updated', $branch, $before, $branch->toArray(), $branch->company_id, $branch->id);

        return redirect()->route('erp.branches.index')->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->is_head_office) {
            return back()->with('error', 'Cannot delete the designated Head Office branch.');
        }

        $before = $branch->toArray();
        $this->auditLog->log('branch.deleted', null, $before, null, $branch->company_id, null);
        $branch->delete();

        return redirect()->route('erp.branches.index')->with('success', 'Branch deleted successfully.');
    }

    /**
     * Switch company/branch context in session.
     */
    public function switchContext(Request $request)
    {
        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'branch_id'  => 'nullable|exists:branches,id',
        ]);

        if ($request->filled('company_id')) {
            $company = Company::findOrFail($request->company_id);
            $this->context->setCompany($company);
        }

        if ($request->filled('branch_id')) {
            $branch = Branch::findOrFail($request->branch_id);
            $this->context->setBranch($branch);
        }

        return back()->with('success', 'Active workspace context switched successfully.');
    }
}

