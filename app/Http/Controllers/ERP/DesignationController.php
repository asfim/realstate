<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Department;
use App\Models\ERP\Designation;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    public function index()
    {
        $currentCompany = $this->context->currentCompany();
        $designations = Designation::where('company_id', $currentCompany?->id)
            ->with('department')
            ->latest()
            ->paginate(20);
        $departments = Department::where('company_id', $currentCompany?->id)->where('status', 'active')->get();

        return view('erp.organization.designations.index', compact('designations', 'departments'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'name'          => 'required|string|max:255',
            'grade'         => 'nullable|string|max:50',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $designation = Designation::create($validated);
        $this->auditLog->log('designation.created', $designation, null, $designation->toArray(), $currentCompany->id);

        return redirect()->route('erp.designations.index')->with('success', 'Designation created successfully.');
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'name'          => 'required|string|max:255',
            'grade'         => 'nullable|string|max:50',
        ]);

        $before = $designation->toArray();
        $designation->update($validated);
        $this->auditLog->log('designation.updated', $designation, $before, $designation->toArray(), $designation->company_id);

        return redirect()->route('erp.designations.index')->with('success', 'Designation updated successfully.');
    }

    public function destroy(Designation $designation)
    {
        $before = $designation->toArray();
        $designation->delete();
        $this->auditLog->log('designation.deleted', null, $before, null, $designation->company_id);

        return redirect()->route('erp.designations.index')->with('success', 'Designation deleted successfully.');
    }
}

