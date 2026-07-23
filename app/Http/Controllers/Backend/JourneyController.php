<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Journey;
use Illuminate\Http\Request;

class JourneyController extends Controller
{
    public function index()
    {
        $journeys = Journey::orderBy('sort_order', 'asc')->orderBy('id', 'desc')->get();
        return view('backend.journeys.index', compact('journeys'));
    }

    public function create()
    {
        return view('backend.journeys.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        Journey::create($data);

        return redirect()->route('admin.journeys.index')->with('success', 'Journey milestone added successfully.');
    }

    public function edit(Journey $journey)
    {
        return view('backend.journeys.edit', compact('journey'));
    }

    public function update(Request $request, Journey $journey)
    {
        $data = $request->validate([
            'year' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        $journey->update($data);

        return redirect()->route('admin.journeys.index')->with('success', 'Journey milestone updated successfully.');
    }

    public function destroy(Journey $journey)
    {
        $journey->delete();

        return redirect()->route('admin.journeys.index')->with('success', 'Journey milestone deleted successfully.');
    }
}
