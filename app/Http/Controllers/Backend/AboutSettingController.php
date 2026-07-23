<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('key', [
            'about_title',
            'about_subtitle',
            'about_eyebrow',
            'about_heading',
            'about_description',
            'about_badge_number',
            'about_badge_text',
            'about_mission_title',
            'about_mission_text',
            'about_vision_title',
            'about_vision_text',
            'about_image',
            'about_stat1_number',
            'about_stat1_suffix',
            'about_stat1_label',
            'about_stat2_number',
            'about_stat2_suffix',
            'about_stat2_label',
            'about_stat3_number',
            'about_stat3_suffix',
            'about_stat3_label',
            'about_stat4_number',
            'about_stat4_suffix',
            'about_stat4_label',
        ])->pluck('value', 'key')->toArray();

        return view('backend.about_settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $keys = [
            'about_title',
            'about_subtitle',
            'about_eyebrow',
            'about_heading',
            'about_description',
            'about_badge_number',
            'about_badge_text',
            'about_mission_title',
            'about_mission_text',
            'about_vision_title',
            'about_vision_text',
            'about_stat1_number',
            'about_stat1_suffix',
            'about_stat1_label',
            'about_stat2_number',
            'about_stat2_suffix',
            'about_stat2_label',
            'about_stat3_number',
            'about_stat3_suffix',
            'about_stat3_label',
            'about_stat4_number',
            'about_stat4_suffix',
            'about_stat4_label',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }

        if ($request->hasFile('about_image')) {
            $oldImage = setting('about_image');
            if ($oldImage && !str_starts_with($oldImage, 'http')) {
                Storage::disk('public')->delete($oldImage);
            }
            $path = $request->file('about_image')->store('about', 'public');
            Setting::updateOrCreate(['key' => 'about_image'], ['value' => $path]);
        }

        return redirect()->back()->with('success', 'About Page settings updated successfully.');
    }
}
