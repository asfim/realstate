<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        return view('frontend.pages.home');
    }

    public function projects()
    {
        return view('frontend.pages.projects');
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
