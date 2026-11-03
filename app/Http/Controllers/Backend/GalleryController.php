<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::orderBy('sort_order', 'asc')->orderBy('id', 'desc')->get();
        $settings = Setting::whereIn('key', [
            'gallery_title',
            'gallery_subtitle',
            'gallery_tour_eyebrow',
            'gallery_tour_title',
            'gallery_tour_video_title',
            'gallery_tour_image',
            'gallery_tour_video_url',
            'gallery_section_eyebrow',
            'gallery_section_title',
        ])->pluck('value', 'key')->toArray();

        return view('backend.galleries.index', compact('galleries', 'settings'));
    }

    public function create()
    {
        return view('backend.galleries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('galleries', 'public');
        }

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        Gallery::create($data);

        return redirect()->route('admin.galleries.index')->with('success', 'Gallery photo uploaded successfully.');
    }

    public function edit(Gallery $gallery)
    {
        return view('backend.galleries.edit', compact('gallery'));
    }

    public function show(Gallery $gallery)
    {
        return redirect()->route('admin.galleries.edit', $gallery);
    }

    public function update(Request $request, Gallery $gallery)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($gallery->image && !str_starts_with($gallery->image, 'http')) {
                Storage::disk('public')->delete($gallery->image);
            }
            $data['image'] = $request->file('image')->store('galleries', 'public');
        }

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order', 0);

        $gallery->update($data);

        return redirect()->route('admin.galleries.index')->with('success', 'Gallery photo updated successfully.');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->image && !str_starts_with($gallery->image, 'http')) {
            Storage::disk('public')->delete($gallery->image);
        }
        $gallery->delete();

        return redirect()->route('admin.galleries.index')->with('success', 'Gallery photo deleted successfully.');
    }

    public function updateSettings(Request $request)
    {
        $keys = [
            'gallery_title',
            'gallery_subtitle',
            'gallery_tour_eyebrow',
            'gallery_tour_title',
            'gallery_tour_video_title',
            'gallery_tour_video_url',
            'gallery_section_eyebrow',
            'gallery_section_title',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }

        if ($request->hasFile('gallery_tour_image')) {
            $oldImg = setting('gallery_tour_image');
            if ($oldImg && !str_starts_with($oldImg, 'http')) {
                Storage::disk('public')->delete($oldImg);
            }
            $path = $request->file('gallery_tour_image')->store('galleries', 'public');
            Setting::updateOrCreate(['key' => 'gallery_tour_image'], ['value' => $path]);
        }

        return redirect()->back()->with('success', 'Gallery settings and Video Tour updated successfully.');
    }
}
