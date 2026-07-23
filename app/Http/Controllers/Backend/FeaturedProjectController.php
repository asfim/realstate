<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FeaturedProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeaturedProjectController extends Controller
{
    public function index()
    {
        $projects = FeaturedProject::latest()->get();
        return view('backend.featured_projects.index', compact('projects'));
    }

    public function create()
    {
        return view('backend.featured_projects.create');
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
            $data['image'] = $request->file('image')->store('featured_projects', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        FeaturedProject::create($data);

        return redirect()->route('admin.featured-project.index')->with('success', 'Project added successfully.');
    }

    public function edit(FeaturedProject $featuredProject)
    {
        return view('backend.featured_projects.edit', compact('featuredProject'));
    }

    public function update(Request $request, FeaturedProject $featuredProject)
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
            if ($featuredProject->image) {
                Storage::disk('public')->delete($featuredProject->image);
            }
            $data['image'] = $request->file('image')->store('featured_projects', 'public');
        }

        $data['is_active'] = $request->has('is_active');

        $featuredProject->update($data);

        return redirect()->route('admin.featured-project.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(FeaturedProject $featuredProject)
    {
        if ($featuredProject->image) {
            Storage::disk('public')->delete($featuredProject->image);
        }
        $featuredProject->delete();

        return redirect()->route('admin.featured-project.index')->with('success', 'Project deleted successfully.');
    }

    public function detailsIndex()
    {
        $projects = FeaturedProject::latest()->get();
        return view('backend.featured_projects.details_index', compact('projects'));
    }

    public function detailsEdit(FeaturedProject $featuredProject)
    {
        return view('backend.featured_projects.details_edit', compact('featuredProject'));
    }

    public function detailsUpdate(Request $request, FeaturedProject $featuredProject)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:255',
            'beds' => 'nullable|integer',
            'baths' => 'nullable|integer',
            'area' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'land_area' => 'nullable|string|max:255',
            'no_of_floors' => 'nullable|string|max:255',
            'apartments_per_floor' => 'nullable|string|max:255',
            'launch_date' => 'nullable|string|max:255',
            'expected_completion' => 'nullable|string|max:255',
            'nearest_landmark' => 'nullable|string|max:255',
            'map_url' => 'nullable|string',
            'video_url' => 'nullable|string|max:255',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg,mov|max:102400',
            'video_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'brochure' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'floor_plans.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('image')) {
            if ($featuredProject->image && Storage::disk('public')->exists($featuredProject->image)) {
                Storage::disk('public')->delete($featuredProject->image);
            }
            $data['image'] = $request->file('image')->store('featured_projects', 'public');
        }

        // Handle video_file deletion & upload
        if ($request->has('delete_video_file')) {
            if ($featuredProject->video_file && Storage::disk('public')->exists($featuredProject->video_file)) {
                Storage::disk('public')->delete($featuredProject->video_file);
            }
            $data['video_file'] = null;
        }
        if ($request->hasFile('video_file')) {
            if ($featuredProject->video_file && Storage::disk('public')->exists($featuredProject->video_file)) {
                Storage::disk('public')->delete($featuredProject->video_file);
            }
            $data['video_file'] = $request->file('video_file')->store('featured_projects/videos', 'public');
        }

        // Handle video_thumbnail deletion & upload
        if ($request->has('delete_video_thumbnail')) {
            if ($featuredProject->video_thumbnail && Storage::disk('public')->exists($featuredProject->video_thumbnail)) {
                Storage::disk('public')->delete($featuredProject->video_thumbnail);
            }
            $data['video_thumbnail'] = null;
        }
        if ($request->hasFile('video_thumbnail')) {
            if ($featuredProject->video_thumbnail && Storage::disk('public')->exists($featuredProject->video_thumbnail)) {
                Storage::disk('public')->delete($featuredProject->video_thumbnail);
            }
            $data['video_thumbnail'] = $request->file('video_thumbnail')->store('featured_projects/video_thumbnails', 'public');
        }

        if ($request->hasFile('brochure')) {
            if ($featuredProject->brochure && Storage::disk('public')->exists($featuredProject->brochure)) {
                Storage::disk('public')->delete($featuredProject->brochure);
            }
            $data['brochure'] = $request->file('brochure')->store('featured_projects/brochures', 'public');
        }

        // Handle gallery deletions
        $currentGallery = $featuredProject->gallery ?? [];
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
                $currentGallery[] = $file->store('featured_projects/gallery', 'public');
            }
        }
        $data['gallery'] = $currentGallery;

        // Handle category-wise floor plan uploads, deletions & info rows
        $categories = ['ground', 'typical', 'roof', 'unit'];
        $currentFloorPlans = $featuredProject->floor_plans ?? [];

        foreach ($categories as $cat) {
            // Delete category image if requested
            if ($request->has("delete_floor_plan_{$cat}")) {
                if (isset($currentFloorPlans[$cat]['image']) && Storage::disk('public')->exists($currentFloorPlans[$cat]['image'])) {
                    Storage::disk('public')->delete($currentFloorPlans[$cat]['image']);
                }
                unset($currentFloorPlans[$cat]['image']);
            }

            // Upload new category image
            if ($request->hasFile("floor_plan_{$cat}")) {
                if (isset($currentFloorPlans[$cat]['image']) && Storage::disk('public')->exists($currentFloorPlans[$cat]['image'])) {
                    Storage::disk('public')->delete($currentFloorPlans[$cat]['image']);
                }
                $currentFloorPlans[$cat]['image'] = $request->file("floor_plan_{$cat}")->store("featured_projects/floor_plans/{$cat}", 'public');
            }

            // Save key-value info rows
            if ($request->has("floor_plan_info.{$cat}")) {
                $infoRows = [];
                foreach ($request->input("floor_plan_info.{$cat}") as $row) {
                    if (!empty($row['key']) || !empty($row['value'])) {
                        $infoRows[] = [
                            'key' => trim($row['key'] ?? ''),
                            'value' => trim($row['value'] ?? ''),
                        ];
                    }
                }
                $currentFloorPlans[$cat]['info'] = $infoRows;
            }
        }
        $data['floor_plans'] = $currentFloorPlans;

        $featuredProject->update($data);

        return redirect()->route('admin.featured-project.details.index')->with('success', 'Project details updated successfully.');
    }
}
