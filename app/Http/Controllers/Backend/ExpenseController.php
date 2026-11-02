<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\AccountCategory;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::expense()->with(['category', 'property', 'creator'])->latest('transaction_date')->latest('id');

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
        $expenses = $query->paginate(15)->withQueryString();

        $categories = AccountCategory::where('type', 'expense')->where('is_active', true)->get();

        return view('backend.accounts.expenses.index', compact('expenses', 'categories', 'totalFilteredAmount'));
    }

    public function create()
    {
        $categories = AccountCategory::where('type', 'expense')->where('is_active', true)->get();
        $properties = Property::where('is_active', true)->select('id', 'title', 'location')->get();

        return view('backend.accounts.expenses.create', compact('categories', 'properties'));
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

        $validated['type'] = 'expense';
        $validated['created_by'] = Auth::guard('admin')->id();

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('transactions', 'public');
        }

        Transaction::create($validated);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense entry recorded successfully.');
    }

    public function edit(Transaction $expense)
    {
        abort_if($expense->type !== 'expense', 404);

        $categories = AccountCategory::where('type', 'expense')->where('is_active', true)->get();
        $properties = Property::where('is_active', true)->select('id', 'title', 'location')->get();

        return view('backend.accounts.expenses.edit', compact('expense', 'categories', 'properties'));
    }

    public function update(Request $request, Transaction $expense)
    {
        abort_if($expense->type !== 'expense', 404);

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
            if ($expense->attachment && Storage::disk('public')->exists($expense->attachment)) {
                Storage::disk('public')->delete($expense->attachment);
            }
            $validated['attachment'] = $request->file('attachment')->store('transactions', 'public');
        }

        $expense->update($validated);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense entry updated successfully.');
    }

    public function destroy(Transaction $expense)
    {
        abort_if($expense->type !== 'expense', 404);

        if ($expense->attachment && Storage::disk('public')->exists($expense->attachment)) {
            Storage::disk('public')->delete($expense->attachment);
        }

        $expense->delete();

        return redirect()->route('admin.expenses.index')->with('success', 'Expense entry deleted successfully.');
    }
}

