<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class ContactSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('backend.contact_settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'text']
            );
        }

        // Handle toggles (checkboxes) that are not sent in request when unchecked
        $booleanKeys = ['contact_cta_status'];
        foreach ($booleanKeys as $key) {
            if (!$request->has($key)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => '0', 'type' => 'boolean']
                );
            }
        }

        \Illuminate\Support\Facades\Cache::forget('global_settings');

        return back()->with('success', 'Contact page settings updated successfully.');
    }
}
