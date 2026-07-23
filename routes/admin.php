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

    // Featured Projects
    Route::resource('featured-project', \App\Http\Controllers\Backend\FeaturedProjectController::class);
    Route::get('featured-project-details', [\App\Http\Controllers\Backend\FeaturedProjectController::class, 'detailsIndex'])->name('featured-project.details.index');
    Route::get('featured-project-details/{featuredProject}/edit', [\App\Http\Controllers\Backend\FeaturedProjectController::class, 'detailsEdit'])->name('featured-project.details.edit');
    Route::post('featured-project-details/{featuredProject}', [\App\Http\Controllers\Backend\FeaturedProjectController::class, 'detailsUpdate'])->name('featured-project.details.update');

    Route::resource('team-members', \App\Http\Controllers\Backend\TeamMemberController::class);
    Route::resource('testimonials', \App\Http\Controllers\Backend\TestimonialController::class);

    // Properties Management
    Route::resource('properties', \App\Http\Controllers\Backend\PropertyController::class);
    Route::post('properties/{property}/details', [\App\Http\Controllers\Backend\PropertyController::class, 'updateDetails'])->name('properties.updateDetails');
    Route::post('properties/{property}/construction-status', [\App\Http\Controllers\Backend\PropertyController::class, 'updateConstruction'])->name('properties.updateConstruction');
    Route::resource('property-types', \App\Http\Controllers\Backend\PropertyTypeController::class);

    // Manage Property Details & Construction Status Routes
    Route::get('properties-manage', [\App\Http\Controllers\Backend\PropertyController::class, 'manageIndex'])->name('properties.manage.index');
    Route::get('properties-manage/{property}/edit', [\App\Http\Controllers\Backend\PropertyController::class, 'manageEdit'])->name('properties.manage.edit');

    // Submissions Management
    Route::get('submissions', [\App\Http\Controllers\Backend\SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/{submission}', [\App\Http\Controllers\Backend\SubmissionController::class, 'show'])->name('submissions.show');
    Route::delete('submissions/{submission}', [\App\Http\Controllers\Backend\SubmissionController::class, 'destroy'])->name('submissions.destroy');
});
