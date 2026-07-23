<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('backend.homepage_settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);
        
        foreach ($data as $key => $value) {
            // Handle file uploads
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $path = $file->store('settings', 'public');
                
                // Delete old file if exists
                $oldSetting = Setting::where('key', $key)->first();
                if ($oldSetting && $oldSetting->value) {
                    Storage::disk('public')->delete($oldSetting->value);
                }
                
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $path, 'type' => 'image']
                );
            } else {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'type' => 'text']
                );
            }
        }
        
        // Handle toggles (checkboxes) that are not sent in the request when unchecked
        $booleanKeys = ['hero_status', 'property_search_status', 'cta_status', 'why_us_status', 'testimonials_status', 'team_status'];
        foreach ($booleanKeys as $key) {
            if (!$request->has($key)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => '0', 'type' => 'boolean']
                );
            }
        }

        \Illuminate\Support\Facades\Cache::forget('global_settings');

        return back()->with('success', 'Homepage settings updated successfully.');
    }
}
