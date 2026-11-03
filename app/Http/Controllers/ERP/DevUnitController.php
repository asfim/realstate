<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\DevProject;
use App\Models\ERP\DevProjectUnit;
use App\Services\ERP\AuditLogService;
use Illuminate\Http\Request;

class DevUnitController extends Controller
{
    public function __construct(protected AuditLogService $auditLog)
    {
    }

    public function store(Request $request, DevProject $devProject)
    {
        $validated = $request->validate([
            'unit_number'          => 'required|string|max:50',
            'floor_label'          => 'nullable|string|max:50',
            'unit_type'            => 'required|string',
            'allocation_share'     => 'required|string',
            'gross_area_sqft'      => 'required|numeric|min:1',
            'net_carpet_area_sqft' => 'nullable|numeric|min:1',
            'bedrooms'             => 'nullable|integer|min:0',
            'bathrooms'            => 'nullable|integer|min:0',
            'balconies'            => 'nullable|integer|min:0',
            'facing'               => 'nullable|string|max:50',
            'base_price_sqft'      => 'required|numeric|min:1',
            'utility_charges'      => 'nullable|numeric|min:0',
            'status'               => 'required|string',
            'notes'                => 'nullable|string',
        ]);

        $validated['floor_label'] = $validated['floor_label'] ?? 'Typical Floor';
        $validated['project_id'] = $devProject->id;
        $validated['total_unit_price'] = $validated['gross_area_sqft'] * $validated['base_price_sqft'];
        $validated['utility_charges'] = $validated['utility_charges'] ?? 0;
        $validated['grand_total_price'] = $validated['total_unit_price'] + $validated['utility_charges'];

        $unit = DevProjectUnit::create($validated);
        $this->auditLog->log('dev.unit_created', $unit, null, $unit->toArray(), $devProject->company_id, $devProject->branch_id);

        return back()->with('success', "Unit {$unit->unit_number} added to inventory.");
    }

    public function update(Request $request, DevProjectUnit $unit)
    {
        $validated = $request->validate([
            'unit_number'          => 'required|string|max:50',
            'floor_label'          => 'required|string|max:50',
            'unit_type'            => 'required|string',
            'allocation_share'     => 'required|string',
            'gross_area_sqft'      => 'required|numeric|min:1',
            'net_carpet_area_sqft' => 'nullable|numeric|min:1',
            'bedrooms'             => 'nullable|integer|min:0',
            'bathrooms'            => 'nullable|integer|min:0',
            'balconies'            => 'nullable|integer|min:0',
            'facing'               => 'nullable|string|max:50',
            'base_price_sqft'      => 'required|numeric|min:1',
            'utility_charges'      => 'nullable|numeric|min:0',
            'status'               => 'required|string',
            'notes'                => 'nullable|string',
        ]);

        $validated['total_unit_price'] = $validated['gross_area_sqft'] * $validated['base_price_sqft'];
        $validated['utility_charges'] = $validated['utility_charges'] ?? 0;
        $validated['grand_total_price'] = $validated['total_unit_price'] + $validated['utility_charges'];

        $before = $unit->toArray();
        $unit->update($validated);
        $this->auditLog->log('dev.unit_updated', $unit, $before, $unit->toArray(), $unit->project->company_id, $unit->project->branch_id);

        return back()->with('success', "Unit {$unit->unit_number} updated.");
    }

    public function destroy(DevProjectUnit $unit)
    {
        if ($unit->status === 'booked' || $unit->status === 'sold') {
            return back()->with('error', 'Cannot delete a unit that is booked or sold.');
        }

        $before = $unit->toArray();
        $unit->delete();
        $this->auditLog->log('dev.unit_deleted', null, $before, null, $unit->project->company_id);

        return back()->with('success', 'Unit removed from inventory.');
    }
}
