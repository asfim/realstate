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
}
