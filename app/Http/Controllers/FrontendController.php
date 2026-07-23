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
        $services = \App\Models\Service::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $amenities = \App\Models\Amenity::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        return view('frontend.pages.services', compact('services', 'amenities'));
    }

    public function about()
    {
        $journeys = \App\Models\Journey::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $teamMembers = \App\Models\TeamMember::where('is_active', true)->latest()->take(8)->get();

        return view('frontend.pages.about', compact('journeys', 'teamMembers'));
    }

    public function gallery()
    {
        $galleries = \App\Models\Gallery::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        return view('frontend.pages.gallery', compact('galleries'));
    }

    public function blog()
    {
        $posts = \App\Models\Post::where('is_active', true)->latest('published_at')->paginate(6);
        $faqs = \App\Models\Faq::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        return view('frontend.pages.blog', compact('posts', 'faqs'));
    }

    public function blogDetails($slug)
    {
        $post = \App\Models\Post::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $recentPosts = \App\Models\Post::where('is_active', true)->where('id', '!=', $post->id)->latest()->take(4)->get();
        return view('frontend.pages.blog-details', compact('post', 'recentPosts'));
    }

    public function contact()
    {
        $propertyTypes = \App\Models\PropertyType::all();
        return view('frontend.pages.contact', compact('propertyTypes'));
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

    public function subscribeNewsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $subscriber = \App\Models\Subscriber::firstOrCreate(
            ['email' => $validated['email']]
        );

        $msg = $subscriber->wasRecentlyCreated 
            ? 'Thank you for subscribing to our newsletter!' 
            : 'You are already subscribed to our newsletter.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg
            ]);
        }

        return back()->with('success', $msg);
    }
}
