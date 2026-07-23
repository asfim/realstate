<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FeaturedProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeaturedPropertyController extends Controller
{
    public function index()
    {
        $properties = FeaturedProperty::latest()->get();
        return view('backend.featured_properties.index', compact('properties'));
    }

    public function create()
    {
        return view('backend.featured_properties.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'beds' => 'nullable|integer',
            'baths' => 'nullable|integer',
            'area' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('featured_properties', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        FeaturedProperty::create($data);

        return redirect()->route('admin.featured-properties.index')->with('success', 'Property added successfully.');
    }

    public function edit(FeaturedProperty $featuredProperty)
    {
        return view('backend.featured_properties.edit', compact('featuredProperty'));
    }

    public function update(Request $request, FeaturedProperty $featuredProperty)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'beds' => 'nullable|integer',
            'baths' => 'nullable|integer',
            'area' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            if ($featuredProperty->image) {
                Storage::disk('public')->delete($featuredProperty->image);
            }
            $data['image'] = $request->file('image')->store('featured_properties', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        $featuredProperty->update($data);

        return redirect()->route('admin.featured-properties.index')->with('success', 'Property updated successfully.');
    }

    public function destroy(FeaturedProperty $featuredProperty)
    {
        if ($featuredProperty->image) {
            Storage::disk('public')->delete($featuredProperty->image);
        }
        $featuredProperty->delete();

        return redirect()->route('admin.featured-properties.index')->with('success', 'Property deleted successfully.');
    }
}
