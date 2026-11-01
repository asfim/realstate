<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Company;
use App\Models\ERP\Project;
use App\Models\ERP\Unit;
use App\Models\ERP\UnitPriceHistory;
use App\Models\ERP\UnitStatusEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $companyId = $request->input('company_id', 1);
        $company = Company::find($companyId) ?? Company::first();

        $projectsCount = Project::where('company_id', $company?->id)->count();
        $units = Unit::where('company_id', $company?->id)->get();

        $totalUnits = $units->count();
        $availableUnits = $units->where('status', 'available')->count();
        $heldUnits = $units->where('status', 'held')->count();
        $bookedUnits = $units->where('status', 'booked')->count();
        $soldUnits = $units->where('status', 'sold')->count();

        $totalInventoryValue = $units->sum('asking_price');
        $availableInventoryValue = $units->where('status', 'available')->sum('asking_price');
        $soldInventoryValue = $units->whereIn('status', ['booked', 'sold', 'registered', 'handed_over'])->sum('asking_price');

        $recentProjects = Project::where('company_id', $company?->id)
            ->withCount('units')
            ->latest()
            ->take(5)
            ->get();

        $recentStatusEvents = UnitStatusEvent::where('company_id', $company?->id)
            ->with(['unit.project', 'actor'])
            ->latest('created_at')
            ->take(6)
            ->get();

        $recentPriceHistories = UnitPriceHistory::where('company_id', $company?->id)
            ->with(['unit.project', 'changedBy'])
            ->latest('created_at')
            ->take(6)
            ->get();

        return view('erp.dashboard.index', compact(
            'company',
            'projectsCount',
            'totalUnits',
            'availableUnits',
            'heldUnits',
            'bookedUnits',
            'soldUnits',
            'totalInventoryValue',
            'availableInventoryValue',
            'soldInventoryValue',
            'recentProjects',
            'recentStatusEvents',
            'recentPriceHistories'
        ));
    }
}
