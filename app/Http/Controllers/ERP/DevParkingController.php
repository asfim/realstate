<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\DevParkingSlot;
use App\Models\ERP\DevProject;
use Illuminate\Http\Request;

class DevParkingController extends Controller
{
    public function store(Request $request, DevProject $devProject)
    {
        $validated = $request->validate([
            'slot_number'    => 'required|string|max:50',
            'basement_level' => 'required|string|max:50',
            'price'          => 'nullable|numeric|min:0',
        ]);

        $validated['project_id'] = $devProject->id;
        $validated['price'] = $validated['price'] ?? 0;
        $validated['status'] = 'available';

        DevParkingSlot::create($validated);

        return back()->with('success', 'Parking slot added.');
    }
}

