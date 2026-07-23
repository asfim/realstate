<?php

if (!function_exists('asset_url')) {
    /**
     * Safely resolve image/asset URL for HTTP URLs, local storage, and fallback images.
     */
    function asset_url($path, $default = null)
    {
        if (empty($path)) {
            return $default ?? 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=75';
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['storage/', '/storage/'])) {
            return asset(ltrim($path, '/'));
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    }
}

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        if (class_exists(\App\Models\Setting::class)) {
            try {
                $setting = \App\Models\Setting::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            } catch (\Throwable $e) {
                return $default;
            }
        }
        return $default;
    }
}
