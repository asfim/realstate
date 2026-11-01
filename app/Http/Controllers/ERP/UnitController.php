<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Http\Requests\ERP\PriceRevisionRequest;
use App\Http\Requests\ERP\UnitRequest;
use App\Http\Requests\ERP\UnitStatusTransitionRequest;
use App\Models\ERP\Block;
use App\Models\ERP\Building;
use App\Models\ERP\Company;
use App\Models\ERP\Floor;
use App\Models\ERP\Project;
use App\Models\ERP\PublicPropertyLink;
use App\Models\ERP\Road;
use App\Models\ERP\Unit;
use App\Models\ERP\UnitStatusEvent;
use App\Models\Property;
use App\Services\ERP\UnitAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::where('is_active', true)->get();

        $query = Unit::query()->with(['project', 'building', 'floor', 'block', 'road']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('facing')) {
            $query->where('facing', $request->string('facing'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('unit_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $viewMode = $request->input('view', 'table'); // 'table' or 'grid'

        $units = $query->latest()->paginate(24)->withQueryString();

        $summary = [
            'total' => Unit::count(),
            'available' => Unit::where('status', 'available')->count(),
            'held' => Unit::where('status', 'held')->count(),
            'booked' => Unit::where('status', 'booked')->count(),
            'sold' => Unit::where('status', 'sold')->count(),
        ];

        return view('erp.units.index', compact('units', 'projects', 'viewMode', 'summary'));
    }

    public function create(Request $request): View
    {
        $selectedProjectId = $request->input('project_id');
        $companies = Company::where('is_active', true)->get();
        $projects = Project::where('is_active', true)->get();

        $buildings = $selectedProjectId ? Building::where('project_id', $selectedProjectId)->get() : collect();
        $blocks = $selectedProjectId ? Block::where('project_id', $selectedProjectId)->get() : collect();

        return view('erp.units.create', compact('companies', 'projects', 'buildings', 'blocks', 'selectedProjectId'));
    }

    public function store(UnitRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth('admin')->id();

        $unit = Unit::create($data);

        // Record initial status event
        UnitStatusEvent::create([
            'company_id' => $unit->company_id,
            'unit_id' => $unit->id,
            'from_status' => 'none',
            'to_status' => $unit->status,
            'actor_id' => auth('admin')->id(),
            'notes' => 'Initial inventory creation',
            'created_at' => now(),
        ]);

        return redirect()->route('erp.units.show', $unit)
            ->with('success', 'Unit created successfully.');
    }

    public function show(Unit $unit): View
    {
        $unit->load([
            'company',
            'project',
            'building',
            'floor',
            'block',
            'road',
            'priceHistories.changedBy',
            'statusEvents.actor',
            'publicLink.property',
        ]);

        $availableProperties = Property::whereDoesntHave('detail')->orWhere('is_active', true)->take(50)->get();

        return view('erp.units.show', compact('unit', 'availableProperties'));
    }

    public function edit(Unit $unit): View
    {
        $companies = Company::where('is_active', true)->get();
        $projects = Project::where('is_active', true)->get();
        $buildings = Building::where('project_id', $unit->project_id)->get();
        $floors = $unit->building_id ? Floor::where('building_id', $unit->building_id)->get() : collect();
        $blocks = Block::where('project_id', $unit->project_id)->get();
        $roads = $unit->block_id ? Road::where('block_id', $unit->block_id)->get() : collect();

        return view('erp.units.edit', compact('unit', 'companies', 'projects', 'buildings', 'floors', 'blocks', 'roads'));
    }

    public function update(UnitRequest $request, Unit $unit): RedirectResponse
    {
        $unit->update($request->validated());

        return redirect()->route('erp.units.show', $unit)
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if (in_array($unit->status, ['booked', 'sold', 'registered', 'handed_over'])) {
            return back()->with('error', 'Cannot delete a unit that is booked or sold.');
        }

        $unit->delete();

        return redirect()->route('erp.units.index')
            ->with('success', 'Unit deleted successfully.');
    }

    public function revisePrice(PriceRevisionRequest $request, Unit $unit, UnitAvailabilityService $service): RedirectResponse
    {
        $service->revisePrice(
            $unit,
            $request->validated('new_price'),
            $request->validated('new_min_price'),
            auth('admin')->user(),
            $request->validated('reason')
        );

        return back()->with('success', 'Unit price revised successfully.');
    }

    public function transitionStatus(UnitStatusTransitionRequest $request, Unit $unit, UnitAvailabilityService $service): RedirectResponse
    {
        $service->transitionStatus(
            $unit,
            $request->validated('status'),
            auth('admin')->user(),
            $request->validated('notes')
        );

        return back()->with('success', 'Unit status transitioned successfully.');
    }

    public function linkWebsiteProperty(Request $request, Unit $unit): RedirectResponse
    {
        $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
        ]);

        PublicPropertyLink::updateOrCreate(
            ['unit_id' => $unit->id],
            [
                'property_id' => $request->integer('property_id'),
                'sync_status' => 'synced',
                'notes' => 'Linked manually via Unit detail desk',
            ]
        );

        return back()->with('success', 'Website property linked successfully.');
    }

    public function getHierarchy(Request $request): JsonResponse
    {
        $projectId = $request->integer('project_id');

        $buildings = Building::where('project_id', $projectId)->get(['id', 'name', 'code']);
        $blocks = Block::where('project_id', $projectId)->get(['id', 'name', 'code']);

        return response()->json([
            'buildings' => $buildings,
            'blocks' => $blocks,
        ]);
    }
}

