<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\FeaturedProject;
use App\Models\Submission;
use App\Models\Subscriber;
use App\Models\Post;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\Payroll;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_properties'    => Property::count(),
            'active_properties'   => Property::where('is_active', true)->count(),
            'total_projects'      => FeaturedProject::count(),
            'active_projects'     => FeaturedProject::where('is_active', true)->count(),
            'total_submissions'   => Submission::count(),
            'unread_submissions'  => Submission::where('is_read', false)->count(),
            'total_subscribers'   => Subscriber::count(),
            'total_posts'         => Post::count(),
            'total_services'      => Service::count(),
            'total_team'          => TeamMember::count(),
            'total_testimonials'  => Testimonial::count(),
            'total_income'        => Transaction::income()->sum('amount'),
            'total_expense'       => Transaction::expense()->sum('amount'),
            'total_payroll'       => Payroll::where('status', 'paid')->sum('net_salary'),
            'net_balance'         => Transaction::income()->sum('amount') - Transaction::expense()->sum('amount'),
        ];

        $recentSubmissions = Submission::with(['property', 'featuredProject'])
            ->latest()
            ->take(5)
            ->get();

        $recentProperties = Property::latest()
            ->take(4)
            ->get();

        $recentProjects = FeaturedProject::latest()
            ->take(4)
            ->get();

        return view('backend.dashboard.index', compact(
            'stats',
            'recentSubmissions',
            'recentProperties',
            'recentProjects'
        ));
    }
}
