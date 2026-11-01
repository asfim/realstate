<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Company;
use App\Models\ERP\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $companyId = $request->input('company_id', 1);

        $query = Customer::where('company_id', $companyId)->withCount('bookings');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%")
                  ->orWhere('nid_or_passport', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('erp.customers.index', compact('customers'));
    }

    public function create(): View
    {
        $companies = Company::where('is_active', true)->get();
        return view('erp.customers.create', compact('companies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:erp_companies,id'],
            'customer_code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'nid_or_passport' => ['nullable', 'string', 'max:50'],
            'tin' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['created_by'] = auth('admin')->id();

        $customer = Customer::create($validated);

        return redirect()->route('erp.customers.show', $customer)
            ->with('success', 'Customer profile registered successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['bookings.unit.project', 'bookings.payments', 'holds.unit']);

        return view('erp.customers.show', compact('customer'));
    }
}
