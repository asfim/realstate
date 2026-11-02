<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AccountCategory;
use Illuminate\Http\Request;

class AccountCategoryController extends Controller
{
    public function index()
    {
        $categories = AccountCategory::withCount('transactions')->latest()->get();
        return view('backend.accounts.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        AccountCategory::create($validated);

        return redirect()->route('admin.account-categories.index')->with('success', 'Account category created successfully.');
    }

    public function update(Request $request, AccountCategory $accountCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $accountCategory->update($validated);

        return redirect()->route('admin.account-categories.index')->with('success', 'Account category updated successfully.');
    }

    public function destroy(AccountCategory $accountCategory)
    {
        if ($accountCategory->transactions()->count() > 0) {
            return back()->with('error', 'Cannot delete category because it has linked transactions.');
        }

        $accountCategory->delete();

        return redirect()->route('admin.account-categories.index')->with('success', 'Account category deleted successfully.');
    }
}

