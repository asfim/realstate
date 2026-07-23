<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Setting;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort_order', 'asc')->orderBy('id', 'desc')->get();
        $settings = Setting::whereIn('key', ['faq_eyebrow', 'faq_heading'])
            ->pluck('value', 'key')->toArray();

        return view('backend.faqs.index', compact('faqs', 'settings'));
    }

    public function create()
    {
        return view('backend.faqs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        Faq::create($data);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ added successfully.');
    }

    public function edit(Faq $faq)
    {
        return view('backend.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        $faq->update($data);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted successfully.');
    }

    public function updateSettings(Request $request)
    {
        $keys = ['faq_eyebrow', 'faq_heading'];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }

        return redirect()->back()->with('success', 'FAQ section headers updated successfully.');
    }
}
