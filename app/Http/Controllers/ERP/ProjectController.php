<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Http\Requests\ERP\ProjectRequest;
use App\Models\ERP\Block;
use App\Models\ERP\Branch;
use App\Models\ERP\Building;
use App\Models\ERP\Company;
use App\Models\ERP\Floor;
use App\Models\ERP\Project;
use App\Models\ERP\Road;
use App\Services\ERP\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $companyId = $request->input('company_id', 1);

        $query = Project::query()
            ->with(['company', 'branch'])
            ->withCount([
                'units',
                'units as available_units_count' => function ($q) {
                    $q->where('status', 'available');
                },
                'units as sold_units_count' => function ($q) {
                    $q->whereIn('status', ['sold', 'booked', 'registered', 'handed_over']);
                },
            ]);

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $projects = $query->latest()->paginate(12)->withQueryString();

        return view('erp.projects.index', compact('projects'));
    }

    public function create(): View
    {
        $companies = Company::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();

        return view('erp.projects.create', compact('companies', 'branches'));
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth('admin')->id();

        $project = Project::create($data);

        return redirect()->route('erp.projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project, ProjectService $service): View
    {
        $project->load([
            'company',
            'branch',
            'buildings.floors.units',
            'blocks.roads.units',
            'milestones',
            'publicLink.featuredProject',
        ]);

        $stats = $service->getProjectStats($project);

        $units = $project->units()->with(['building', 'floor', 'block'])->paginate(15);

        return view('erp.projects.show', compact('project', 'stats', 'units'));
    }

    public function edit(Project $project): View
    {
        $companies = Company::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();

        return view('erp.projects.edit', compact('project', 'companies', 'branches'));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('erp.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->units()->count() > 0) {
            return back()->with('error', 'Cannot delete project with existing inventory units.');
        }

        $project->delete();

        return redirect()->route('erp.projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function storeBuilding(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'total_floors' => ['nullable', 'integer', 'min:0'],
        ]);

        $building = Building::create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
            'code' => $validated['code'],
            'name' => $validated['name'],
            'total_floors' => $validated['total_floors'] ?? 0,
        ]);

        // Automatically create floors if specified
        if ($building->total_floors > 0) {
            for ($i = 1; $i <= $building->total_floors; $i++) {
                Floor::create([
                    'company_id' => $project->company_id,
                    'building_id' => $building->id,
                    'floor_number' => $i,
                    'floor_name' => "Floor {$i}",
                ]);
            }
        }

        return back()->with('success', 'Building added successfully.');
    }

    public function storeBlock(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Block::create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Block added successfully.');
    }
}
