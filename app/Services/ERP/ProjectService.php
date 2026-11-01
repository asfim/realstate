<?php

namespace App\Services\ERP;

use App\Models\ERP\Project;
use App\Models\ERP\Unit;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    /**
     * Get computed statistics for a project.
     */
    public function getProjectStats(Project $project): array
    {
        $units = Unit::where('project_id', $project->id)->get();

        $totalUnits = $units->count();
        $availableUnits = $units->where('status', 'available')->count();
        $heldUnits = $units->where('status', 'held')->count();
        $bookedUnits = $units->where('status', 'booked')->count();
        $soldUnits = $units->where('status', 'sold')->count();
        $totalInventoryValue = $units->sum('asking_price');
        $availableInventoryValue = $units->where('status', 'available')->sum('asking_price');

        $totalArea = $units->sum('area');
        $avgPricePerSqft = $totalArea > 0 ? $totalInventoryValue / $totalArea : 0;

        return [
            'total_units' => $totalUnits,
            'available_units' => $availableUnits,
            'held_units' => $heldUnits,
            'booked_units' => $bookedUnits,
            'sold_units' => $soldUnits,
            'total_inventory_value' => $totalInventoryValue,
            'available_inventory_value' => $availableInventoryValue,
            'total_area' => $totalArea,
            'avg_price_per_sqft' => $avgPricePerSqft,
        ];
    }
}

