<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AdminAuthController;

Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::middleware(['admin'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Backend\DashboardController::class, 'index'])->name('dashboard');
    
    // Homepage Settings Core
    Route::get('/homepage-settings', [\App\Http\Controllers\Backend\HomepageSettingController::class, 'index'])->name('homepage_settings.index');
    Route::post('/homepage-settings', [\App\Http\Controllers\Backend\HomepageSettingController::class, 'update'])->name('homepage_settings.update');

    // General Settings
    Route::get('/settings/footer', [\App\Http\Controllers\Backend\SettingController::class, 'footer'])->name('settings.footer');
    Route::get('/settings/other', [\App\Http\Controllers\Backend\SettingController::class, 'other'])->name('settings.other');
    Route::post('/settings/update', [\App\Http\Controllers\Backend\SettingController::class, 'update'])->name('settings.update');

    // Featured Properties
    Route::resource('featured-properties', \App\Http\Controllers\Backend\FeaturedPropertyController::class);
    Route::resource('team-members', \App\Http\Controllers\Backend\TeamMemberController::class);
    Route::resource('testimonials', \App\Http\Controllers\Backend\TestimonialController::class);
});
