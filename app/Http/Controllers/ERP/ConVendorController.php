<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\ConVendor;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;

class ConVendorController extends Controller
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
        $query = ConVendor::where('company_id', $currentCompany?->id)->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('vendor_code', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $vendors = $query->paginate(15)->withQueryString();

        return view('erp.construction.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('erp.construction.vendors.create');
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'contact_person'   => 'nullable|string|max:255',
            'phone'            => 'required|string|max:50',
            'email'            => 'nullable|email|max:255',
            'trade_license_no' => 'nullable|string|max:100',
            'tin_no'           => 'nullable|string|max:100',
            'bank_details'     => 'nullable|string',
            'status'           => 'required|string',
            'address'          => 'nullable|string',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $validated['vendor_code'] = $this->sequenceService->getNextNumber('vendor', $currentCompany->id);

        $vendor = ConVendor::create($validated);
        $this->auditLog->log('procurement.vendor_created', $vendor, null, $vendor->toArray(), $currentCompany->id);

        return redirect()->route('erp.con-vendors.index')->with('success', "Vendor {$vendor->name} ({$vendor->vendor_code}) created.");
    }

    public function edit(ConVendor $conVendor)
    {
        return view('erp.construction.vendors.edit', compact('conVendor'));
    }

    public function update(Request $request, ConVendor $conVendor)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'contact_person'   => 'nullable|string|max:255',
            'phone'            => 'required|string|max:50',
            'email'            => 'nullable|email|max:255',
            'trade_license_no' => 'nullable|string|max:100',
            'tin_no'           => 'nullable|string|max:100',
            'bank_details'     => 'nullable|string',
            'status'           => 'required|string',
            'address'          => 'nullable|string',
        ]);

        $old = $conVendor->toArray();
        $conVendor->update($validated);
        $this->auditLog->log('procurement.vendor_updated', $conVendor, $old, $conVendor->toArray(), $conVendor->company_id);

        return redirect()->route('erp.con-vendors.index')->with('success', 'Vendor updated successfully.');
    }

    public function show(ConVendor $conVendor)
    {
        return redirect()->route('erp.con-vendors.edit', $conVendor);
    }

    public function destroy(ConVendor $conVendor)
    {
        if ($conVendor->purchaseOrders()->count() > 0) {
            return back()->with('error', 'Cannot delete vendor with associated purchase orders.');
        }

        $old = $conVendor->toArray();
        $conVendor->delete();
        $this->auditLog->log('procurement.vendor_deleted', null, $old, null, $conVendor->company_id);

        return redirect()->route('erp.con-vendors.index')->with('success', 'Vendor deleted successfully.');
    }
}

