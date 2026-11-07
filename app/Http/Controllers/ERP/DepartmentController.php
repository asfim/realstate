<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Department;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    public function index()
    {
        $currentCompany = $this->context->currentCompany();
        $departments = Department::where('company_id', $currentCompany?->id)
            ->withCount('designations')
            ->latest()
            ->paginate(15);

        return view('erp.organization.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'nullable|string|max:50',
            'status'     => 'required|in:active,inactive',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $companyId = $validated['company_id'] ?? $currentCompany?->id;
        if (!$companyId) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No active company found. Please configure at least one company in Organization -> Companies first.');
        }

        $validated['company_id'] = $companyId;
        $department = Department::create($validated);
        $this->auditLog->log('department.created', $department, null, $department->toArray(), $companyId);

        return redirect()->route('erp.departments.index')->with('success', 'Department created successfully.');
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'code'   => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $before = $department->toArray();
        $department->update($validated);
        $this->auditLog->log('department.updated', $department, $before, $department->toArray(), $department->company_id);

        return redirect()->route('erp.departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $before = $department->toArray();
        $department->delete();
        $this->auditLog->log('department.deleted', null, $before, null, $department->company_id);

        return redirect()->route('erp.departments.index')->with('success', 'Department deleted successfully.');
    }
}

