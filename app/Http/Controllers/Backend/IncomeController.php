<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\AccountCategory;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::income()->with(['category', 'property', 'creator'])->latest('transaction_date')->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('account_category_id', $request->category_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        $totalFilteredAmount = (clone $query)->sum('amount');
        $incomes = $query->paginate(15)->withQueryString();

        $categories = AccountCategory::where('type', 'income')->where('is_active', true)->get();

        return view('backend.accounts.incomes.index', compact('incomes', 'categories', 'totalFilteredAmount'));
    }

    public function create()
    {
        $categories = AccountCategory::where('type', 'income')->where('is_active', true)->get();
        $properties = Property::where('is_active', true)->select('id', 'title', 'location')->get();

        return view('backend.accounts.incomes.create', compact('categories', 'properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'account_category_id' => 'nullable|exists:account_categories,id',
            'property_id' => 'nullable|exists:properties,id',
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,mobile_banking,card',
            'reference_no' => 'nullable|string|max:100',
            'note' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $validated['type'] = 'income';
        $validated['created_by'] = Auth::guard('admin')->id();

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('transactions', 'public');
        }

        Transaction::create($validated);

        return redirect()->route('admin.incomes.index')->with('success', 'Income entry added successfully.');
    }

    public function edit(Transaction $income)
    {
        abort_if($income->type !== 'income', 404);

        $categories = AccountCategory::where('type', 'income')->where('is_active', true)->get();
        $properties = Property::where('is_active', true)->select('id', 'title', 'location')->get();

        return view('backend.accounts.incomes.edit', compact('income', 'categories', 'properties'));
    }

    public function update(Request $request, Transaction $income)
    {
        abort_if($income->type !== 'income', 404);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'account_category_id' => 'nullable|exists:account_categories,id',
            'property_id' => 'nullable|exists:properties,id',
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,mobile_banking,card',
            'reference_no' => 'nullable|string|max:100',
            'note' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            if ($income->attachment && Storage::disk('public')->exists($income->attachment)) {
                Storage::disk('public')->delete($income->attachment);
            }
            $validated['attachment'] = $request->file('attachment')->store('transactions', 'public');
        }

        $income->update($validated);

        return redirect()->route('admin.incomes.index')->with('success', 'Income entry updated successfully.');
    }

    public function destroy(Transaction $income)
    {
        abort_if($income->type !== 'income', 404);

        if ($income->attachment && Storage::disk('public')->exists($income->attachment)) {
            Storage::disk('public')->delete($income->attachment);
        }

        $income->delete();

        return redirect()->route('admin.incomes.index')->with('success', 'Income entry deleted successfully.');
    }
}

