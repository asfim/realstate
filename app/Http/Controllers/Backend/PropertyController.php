<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Property;
use App\Models\PropertyDetail;
use App\Models\ConstructionStatus;
use App\Models\PropertyType;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::latest()->get();
        return view('backend.properties.index', compact('properties'));
    }

    public function create()
    {
        $types = PropertyType::all();
        return view('backend.properties.create', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('properties', 'public');
        }

        $data['title'] = $data['type'];
        $data['location'] = 'N/A';
        $data['price'] = 'N/A';
        $data['is_active'] = $request->has('is_active');

        $property = Property::create($data);

        return redirect()->route('admin.properties.edit', $property->id)->with('success', 'Property created successfully. Please add details now.');
    }

    public function edit(Property $property)
    {
        $detail = $property->detail ?? new PropertyDetail();
        $construction = $property->constructionStatus ?? new ConstructionStatus();
        $types = PropertyType::all();
        return view('backend.properties.edit', compact('property', 'detail', 'construction', 'types'));
    }

    public function update(Request $request, Property $property)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($property->image) {
                Storage::disk('public')->delete($property->image);
            }
            $data['image'] = $request->file('image')->store('properties', 'public');
        }

        $data['title'] = $data['type'];
        $data['is_active'] = $request->has('is_active');

        $property->update($data);

        return redirect()->route('admin.properties.edit', $property->id)->with('success', 'Property basic info updated.');
    }

    public function updateDetails(Request $request, Property $property)
    {
        $data = $request->validate([
            'description' => 'nullable|string',
            'map_url' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'land_area' => 'nullable|string|max:255',
            'no_of_floors' => 'nullable|string|max:255',
            'apartments_per_floor' => 'nullable|string|max:255',
            'launch_date' => 'nullable|string|max:255',
            'expected_completion' => 'nullable|string|max:255',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'floor_plans.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:255',
            'beds' => 'nullable|integer',
            'baths' => 'nullable|integer',
            'area' => 'nullable|integer',
        ]);

        $detail = $property->detail()->firstOrCreate([], [
            'description' => null,
            'gallery' => [],
            'floor_plans' => [],
        ]);

        // Handle gallery deletions
        $currentGallery = $detail->gallery ?? [];
        if ($request->has('delete_gallery')) {
            foreach ($request->delete_gallery as $delPath) {
                if (in_array($delPath, $currentGallery)) {
                    Storage::disk('public')->delete($delPath);
                    $currentGallery = array_values(array_diff($currentGallery, [$delPath]));
                }
            }
        }

        // Add new gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $currentGallery[] = $file->store('properties/gallery', 'public');
            }
        }

        // Handle floor plans deletions
        $currentFloorPlans = $detail->floor_plans ?? [];
        if ($request->has('delete_floor_plans')) {
            foreach ($request->delete_floor_plans as $delPath) {
                if (in_array($delPath, $currentFloorPlans)) {
                    Storage::disk('public')->delete($delPath);
                    $currentFloorPlans = array_values(array_diff($currentFloorPlans, [$delPath]));
                }
            }
        }

        // Add new floor plans
        if ($request->hasFile('floor_plans')) {
            foreach ($request->file('floor_plans') as $file) {
                $currentFloorPlans[] = $file->store('properties/floor_plans', 'public');
            }
        }

        $property->update([
            'location' => $data['location'] ?? $property->location ?? 'N/A',
            'price' => $data['price'] ?? $property->price ?? 'N/A',
            'beds' => $data['beds'] ?? $property->beds,
            'baths' => $data['baths'] ?? $property->baths,
            'area' => $data['area'] ?? $property->area,
        ]);

        $detail->update([
            'description' => $data['description'],
            'map_url' => $data['map_url'],
            'address' => $data['address'],
            'land_area' => $data['land_area'],
            'no_of_floors' => $data['no_of_floors'],
            'apartments_per_floor' => $data['apartments_per_floor'],
            'launch_date' => $data['launch_date'],
            'expected_completion' => $data['expected_completion'],
            'gallery' => $currentGallery,
            'floor_plans' => $currentFloorPlans,
        ]);

        return redirect()->route('admin.properties.manage.edit', [$property->id, '#details-tab-pane'])->with('success', 'Property details updated.');
    }

    public function updateConstruction(Request $request, Property $property)
    {
        $data = $request->validate([
            'status_updated_at' => 'nullable|string|max:255',
            'progress_names.*' => 'nullable|string',
            'progress_values.*' => 'nullable|string',
        ]);

        // Construct progress_items from names and values
        $progressItems = [];
        if (isset($data['progress_names'])) {
            foreach ($data['progress_names'] as $index => $name) {
                if (!empty($name)) {
                    $progressItems[] = [
                        'name' => $name,
                        'progress' => $data['progress_values'][$index] ?? '0%',
                    ];
                }
            }
        }

        $property->constructionStatus()->updateOrCreate(
            [],
            [
                'status_updated_at' => $data['status_updated_at'],
                'progress_items' => $progressItems,
            ]
        );

        return redirect()->route('admin.properties.manage.edit', [$property->id, '#construction-tab-pane'])->with('success', 'Construction status updated.');
    }

    public function destroy(Property $property)
    {
        // Delete images from disk
        if ($property->image) {
            Storage::disk('public')->delete($property->image);
        }

        $detail = $property->detail;
        if ($detail) {
            foreach ($detail->gallery ?? [] as $img) {
                Storage::disk('public')->delete($img);
            }
            foreach ($detail->floor_plans ?? [] as $plan) {
                Storage::disk('public')->delete($plan);
            }
        }

        $property->delete();

        return redirect()->route('admin.properties.index')->with('success', 'Property and all details deleted.');
    }

    public function manageIndex()
    {
        $properties = Property::latest()->get();
        return view('backend.properties.manage_index', compact('properties'));
    }

    public function manageEdit(Property $property)
    {
        $detail = $property->detail ?? new PropertyDetail();
        $construction = $property->constructionStatus ?? new ConstructionStatus();
        return view('backend.properties.manage_edit', compact('property', 'detail', 'construction'));
    }
}
