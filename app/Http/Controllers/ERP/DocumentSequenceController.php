<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Branch;
use App\Models\ERP\DocumentSequence;
use App\Models\ERP\FiscalYear;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class DocumentSequenceController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    public function index()
    {
        $currentCompany = $this->context->currentCompany();
        $sequences = DocumentSequence::where('company_id', $currentCompany?->id)
            ->with(['branch', 'fiscalYear'])
            ->latest()
            ->paginate(20);

        $branches = Branch::where('company_id', $currentCompany?->id)->where('status', 'active')->get();
        $fiscalYears = FiscalYear::where('company_id', $currentCompany?->id)->get();

        return view('erp.organization.document_sequences.index', compact('sequences', 'branches', 'fiscalYears'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $validated = $request->validate([
            'document_type'  => 'required|string|max:50',
            'prefix'         => 'required|string|max:20',
            'branch_id'      => 'nullable|exists:branches,id',
            'fiscal_year_id' => 'nullable|exists:fiscal_years,id',
            'next_number'    => 'required|integer|min:1',
            'padding'        => 'required|integer|min:1|max:10',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $sequence = DocumentSequence::create($validated);
        $this->auditLog->log('document_sequence.created', $sequence, null, $sequence->toArray(), $currentCompany->id);

        return redirect()->route('erp.document-sequences.index')->with('success', 'Document Sequence configured successfully.');
    }

    public function update(Request $request, DocumentSequence $documentSequence)
    {
        $validated = $request->validate([
            'prefix'      => 'required|string|max:20',
            'next_number' => 'required|integer|min:1',
            'padding'     => 'required|integer|min:1|max:10',
        ]);

        $before = $documentSequence->toArray();
        $documentSequence->update($validated);
        $this->auditLog->log('document_sequence.updated', $documentSequence, $before, $documentSequence->toArray(), $documentSequence->company_id);

        return redirect()->route('erp.document-sequences.index')->with('success', 'Document Sequence updated successfully.');
    }
}

