<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\CrmCustomer;
use App\Models\ERP\CrmLead;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use App\Services\ERP\LeadRoutingService;
use Illuminate\Http\Request;

class CrmCustomerController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected DocumentSequenceService $sequenceService,
        protected LeadRoutingService $routingService,
        protected AuditLogService $auditLog
    ) {
    }

    public function index(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = CrmCustomer::where('company_id', $currentCompany?->id)->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%")
                    ->orWhere('nid_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(20)->withQueryString();

        return view('erp.crm.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('erp.crm.customers.create');
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'father_or_husband_name' => 'nullable|string|max:255',
            'mother_name'            => 'nullable|string|max:255',
            'phone'                  => 'required|string|max:50',
            'email'                  => 'nullable|email|max:255',
            'emergency_contact'      => 'nullable|string|max:50',
            'nid_number'             => 'nullable|string|max:50',
            'tin_number'             => 'nullable|string|max:50',
            'passport_number'        => 'nullable|string|max:50',
            'present_address'        => 'nullable|string',
            'permanent_address'      => 'nullable|string',
            'nominee_name'           => 'nullable|string|max:255',
            'nominee_relation'       => 'nullable|string|max:100',
            'nominee_nid'            => 'nullable|string|max:50',
            'nominee_phone'          => 'nullable|string|max:50',
            'status'                 => 'required|in:active,inactive',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $validated['customer_code'] = $this->sequenceService->getNextNumber('customer', $currentCompany->id, $currentBranch?->id);

        $customer = CrmCustomer::create($validated);
        $this->auditLog->log('crm.customer_created', $customer, null, $customer->toArray(), $currentCompany->id);

        return redirect()->route('erp.customers.show', $customer)->with('success', 'Customer record created.');
    }

    public function show(CrmCustomer $customer)
    {
        $customer->load('lead');
        return view('erp.crm.customers.show', compact('customer'));
    }

    public function edit(CrmCustomer $customer)
    {
        return view('erp.crm.customers.edit', compact('customer'));
    }

    public function update(Request $request, CrmCustomer $customer)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'father_or_husband_name' => 'nullable|string|max:255',
            'mother_name'            => 'nullable|string|max:255',
            'phone'                  => 'required|string|max:50',
            'email'                  => 'nullable|email|max:255',
            'emergency_contact'      => 'nullable|string|max:50',
            'nid_number'             => 'nullable|string|max:50',
            'tin_number'             => 'nullable|string|max:50',
            'passport_number'        => 'nullable|string|max:50',
            'present_address'        => 'nullable|string',
            'permanent_address'      => 'nullable|string',
            'nominee_name'           => 'nullable|string|max:255',
            'nominee_relation'       => 'nullable|string|max:100',
            'nominee_nid'            => 'nullable|string|max:50',
            'nominee_phone'          => 'nullable|string|max:50',
            'status'                 => 'required|in:active,inactive',
        ]);

        $before = $customer->toArray();
        $customer->update($validated);
        $this->auditLog->log('crm.customer_updated', $customer, $before, $customer->toArray(), $customer->company_id);

        return redirect()->route('erp.customers.show', $customer)->with('success', 'Customer record updated.');
    }

    public function convertLead(Request $request, CrmLead $lead)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'phone'                  => 'required|string|max:50',
            'email'                  => 'nullable|email|max:255',
            'nid_number'             => 'nullable|string|max:50',
            'tin_number'             => 'nullable|string|max:50',
            'present_address'        => 'nullable|string',
            'permanent_address'      => 'nullable|string',
            'nominee_name'           => 'nullable|string|max:255',
            'nominee_relation'       => 'nullable|string|max:100',
            'nominee_nid'            => 'nullable|string|max:50',
            'nominee_phone'          => 'nullable|string|max:50',
        ]);

        $customer = $this->routingService->convertLeadToCustomer($lead, $validated);

        return redirect()->route('erp.customers.show', $customer)->with('success', "Lead {$lead->lead_code} successfully converted to Customer Account {$customer->customer_code}!");
    }
}
