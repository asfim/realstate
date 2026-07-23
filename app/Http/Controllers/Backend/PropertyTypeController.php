<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PropertyType;
use Illuminate\Support\Facades\Storage;

class PropertyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = PropertyType::latest()->get();
        return view('backend.property_types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.property_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255|unique:property_types,title',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'icon_class' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('property_types', 'public');
        }

        PropertyType::create($data);

        return redirect()->route('admin.property-types.index')->with('success', 'Property Type created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PropertyType $propertyType)
    {
        return view('backend.property_types.edit', compact('propertyType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PropertyType $propertyType)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255|unique:property_types,title,' . $propertyType->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'icon_class' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            if ($propertyType->image) {
                Storage::disk('public')->delete($propertyType->image);
            }
            $data['image'] = $request->file('image')->store('property_types', 'public');
        }

        $propertyType->update($data);

        return redirect()->route('admin.property-types.index')->with('success', 'Property Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PropertyType $propertyType)
    {
        if ($propertyType->image) {
            Storage::disk('public')->delete($propertyType->image);
        }
        $propertyType->delete();

        return redirect()->route('admin.property-types.index')->with('success', 'Property Type deleted.');
    }
}
