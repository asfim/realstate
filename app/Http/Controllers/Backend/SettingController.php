<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function site()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('backend.settings.site', compact('settings'));
    }

    public function footer()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('backend.settings.footer', compact('settings'));
    }

    public function other()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('backend.settings.other', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method', 'redirect_to']);
        
        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $path = $file->store('settings', 'public');
                
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
        // Only process toggles if they were present in the view (we use a hidden field to pass context)
        if ($request->has('context') && $request->context == 'other') {
            $booleanKeys = ['why_us_status', 'testimonials_status', 'team_status'];
            foreach ($booleanKeys as $key) {
                if (!$request->has($key)) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        ['value' => '0', 'type' => 'boolean']
                    );
                }
            }
        }

        \Illuminate\Support\Facades\Cache::forget('global_settings');

        if ($request->has('redirect_to')) {
            return redirect($request->redirect_to)->with('success', 'Settings updated successfully.');
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
