<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\DevProject;
use App\Models\ERP\HrDailyLaborer;
use App\Models\ERP\HrLaborMusterRoll;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;

class HrLaborController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected DocumentSequenceService $sequenceService,
        protected AuditLogService $auditLog
    ) {
    }

    public function laborersIndex(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = HrDailyLaborer::where('company_id', $currentCompany?->id)->with('assignedProject')->latest();

        if ($request->filled('trade')) {
            $query->where('trade', $request->trade);
        }

        if ($request->filled('assigned_project_id')) {
            $query->where('assigned_project_id', $request->assigned_project_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('laborer_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $laborers = $query->paginate(20)->withQueryString();
        $projects = DevProject::where('company_id', $currentCompany?->id)->get();

        return view('erp.hr.labor.index', compact('laborers', 'projects'));
    }

    public function laborerStore(Request $request)
    {
        $currentCompany = $this->context->currentCompany();

        $validated = $request->validate([
            'assigned_project_id'  => 'nullable|exists:dev_projects,id',
            'name'                 => 'required|string|max:255',
            'phone'                => 'nullable|string|max:50',
            'nid_no'               => 'nullable|string|max:100',
            'trade'                => 'required|string|in:mason,rod_binder,carpenter,electrician,plumber,helper',
            'daily_wage_rate'      => 'required|numeric|min:1',
            'overtime_hourly_rate' => 'nullable|numeric|min:0',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $validated['overtime_hourly_rate'] = $validated['overtime_hourly_rate'] ?? 0;
        $validated['laborer_code'] = $this->sequenceService->getNextNumber('laborer', $currentCompany->id);

        $laborer = HrDailyLaborer::create($validated);
        $this->auditLog->log('hr.laborer_registered', $laborer, null, $laborer->toArray(), $currentCompany->id);

        return back()->with('success', "Daily Worker {$laborer->name} ({$laborer->laborer_code}) registered.");
    }

    public function musterIndex(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $projects = DevProject::where('company_id', $currentCompany?->id)->get();

        $selectedProjectId = $request->project_id ?? $projects->first()?->id;
        $workDate = $request->work_date ?? now()->format('Y-m-d');

        $musterRolls = collect();
        if ($selectedProjectId) {
            $musterRolls = HrLaborMusterRoll::where('company_id', $currentCompany?->id)
                ->where('project_id', $selectedProjectId)
                ->where('work_date', $workDate)
                ->with('laborer')
                ->get();
        }

        $projectLaborers = collect();
        if ($selectedProjectId) {
            $projectLaborers = HrDailyLaborer::where('company_id', $currentCompany?->id)
                ->where('status', 'active')
                ->where(function ($q) use ($selectedProjectId) {
                    $q->where('assigned_project_id', $selectedProjectId)->orWhereNull('assigned_project_id');
                })
                ->get();
        }

        return view('erp.hr.labor.muster', compact('projects', 'selectedProjectId', 'workDate', 'musterRolls', 'projectLaborers'));
    }

    public function musterStore(Request $request)
    {
        $currentCompany = $this->context->currentCompany();

        $validated = $request->validate([
            'project_id'     => 'required|exists:dev_projects,id',
            'work_date'      => 'required|date',
            'laborer_id'     => 'required|exists:hr_daily_laborers,id',
            'is_present'     => 'required|boolean',
            'regular_hours'  => 'required|numeric|min:0|max:12',
            'overtime_hours' => 'nullable|numeric|min:0|max:12',
        ]);

        $laborer = HrDailyLaborer::findOrFail($validated['laborer_id']);

        $isPresent = (bool) $validated['is_present'];
        $regularHours = $isPresent ? (float) $validated['regular_hours'] : 0;
        $overtimeHours = $isPresent ? (float) ($validated['overtime_hours'] ?? 0) : 0;

        // Daily rate is based on 8 regular hours
        $hourlyRate = $laborer->daily_wage_rate / 8;
        $regWage = $regularHours * $hourlyRate;
        $otRate = $laborer->overtime_hourly_rate > 0 ? (float) $laborer->overtime_hourly_rate : ($hourlyRate * 1.5);
        $otWage = $overtimeHours * $otRate;
        $totalWage = $regWage + $otWage;

        $entry = HrLaborMusterRoll::updateOrCreate(
            [
                'laborer_id' => $laborer->id,
                'work_date'  => $validated['work_date'],
            ],
            [
                'company_id'     => $currentCompany->id,
                'project_id'     => $validated['project_id'],
                'is_present'     => $isPresent,
                'regular_hours'  => $regularHours,
                'overtime_hours' => $overtimeHours,
                'regular_wage'   => $regWage,
                'overtime_wage'  => $otWage,
                'total_wage'     => $totalWage,
                'payment_status' => 'unpaid',
                'recorded_by'    => auth('admin')->id(),
            ]
        );

        return back()->with('success', "Daily wage recorded for {$laborer->name}: ৳ " . number_format($totalWage, 2));
    }
}

