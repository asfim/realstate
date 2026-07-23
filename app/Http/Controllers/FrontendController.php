<?php

namespace App\Http\Controllers;

use App\Models\FeaturedProperty;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        $featuredProperties = FeaturedProperty::where('is_active', true)->latest()->take(6)->get();
        $teamMembers = TeamMember::where('is_active', true)->latest()->take(8)->get();
        $testimonials = Testimonial::where('is_active', true)->latest()->get();
        
        return view('frontend.pages.home', compact('featuredProperties', 'teamMembers', 'testimonials'));
    }

    public function projects()
    {
        $projects = FeaturedProperty::where('is_active', true)->latest()->get();
        return view('frontend.pages.projects', compact('projects'));
    }

    public function properties()
    {
        return view('frontend.pages.properties');
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

    public function propertyDetails()
    {
        return view('frontend.pages.property-details');
    }

    public function constructionStatus()
    {
        return view('frontend.pages.construction-status');
    }

    public function propertyFeatured()
    {
        return view('frontend.pages.property-featured');
    }
}
