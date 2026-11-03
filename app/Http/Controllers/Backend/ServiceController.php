<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('sort_order', 'asc')->orderBy('id', 'desc')->get();
        return view('backend.services.index', compact('services'));
    }

    public function create()
    {
        return view('backend.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'short_description' => 'required|string|max:1000',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);
        $data['icon'] = $request->filled('icon') ? $request->icon : 'fa-solid fa-concierge-bell';

        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return view('backend.services.edit', compact('service'));
    }

    public function show(Service $service)
    {
        return redirect()->route('admin.services.edit', $service);
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'short_description' => 'required|string|max:1000',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->has('delete_image') && $service->image) {
            Storage::disk('public')->delete($service->image);
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);
        $data['icon'] = $request->filled('icon') ? $request->icon : ($service->icon ?? 'fa-solid fa-concierge-bell');

        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}
