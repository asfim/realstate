<?php

namespace App\Http\Controllers;

use App\Models\FeaturedProject;
use App\Models\Property;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        $featuredProperties = FeaturedProject::where('is_active', true)->latest()->take(6)->get();
        $teamMembers = TeamMember::where('is_active', true)->latest()->take(8)->get();
        $testimonials = Testimonial::where('is_active', true)->latest()->get();
        $types = \App\Models\PropertyType::take(3)->get();

        return view('frontend.pages.home', compact('featuredProperties', 'teamMembers', 'testimonials', 'types'));
    }

    public function projects()
    {
        $projects = FeaturedProject::where('is_active', true)->latest()->get();
        return view('frontend.pages.projects', compact('projects'));
    }

    public function projectFeatured(?FeaturedProject $featuredProject = null)
    {
        if (!$featuredProject || !$featuredProject->exists) {
            $featuredProject = FeaturedProject::where('is_active', true)->first() ?? FeaturedProject::first();
        }

        return view('frontend.pages.project-featured', [
            'project' => $featuredProject
        ]);
    }

    public function properties(Request $request)
    {
        $query = Property::where('is_active', true);
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $properties = $query->latest()->get();
        $types = \App\Models\PropertyType::all();
        return view('frontend.pages.properties', compact('properties', 'types'));
    }

    public function services()
    {
        return view('frontend.pages.services');
    }

    public function about()
    {
        return view('frontend.pages.about');
    }

    public function gallery()
    {
        return view('frontend.pages.gallery');
    }

    public function blog()
    {
        return view('frontend.pages.blog');
    }

    public function contact()
    {
        return view('frontend.pages.contact');
    }

    public function propertyDetails(Property $property)
    {
        $property->load('detail');
        return view('frontend.pages.property-details', compact('property'));
    }

    public function constructionStatus(Property $property)
    {
        $property->load(['detail', 'constructionStatus']);
        return view('frontend.pages.construction-status', compact('property'));
    }

    public function submitEnquiry(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'message' => 'nullable|string|max:2000',
            'featured_project_id' => 'nullable|exists:featured_projects,id',
            'property_id' => 'nullable|exists:properties,id',
        ]);

        \App\Models\Submission::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your enquiry has been submitted successfully. Our team will contact you shortly.'
            ]);
        }

        return back()->with('success', 'Thank you! Your enquiry has been submitted successfully.');
    }
}
