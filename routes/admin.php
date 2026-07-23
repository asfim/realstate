<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AdminAuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\HomepageSettingController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\FeaturedProjectController;
use App\Http\Controllers\Backend\TeamMemberController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\PropertyController;
use App\Http\Controllers\Backend\PropertyTypeController;
use App\Http\Controllers\Backend\SubmissionController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\AmenityController;
use App\Http\Controllers\Backend\AboutSettingController;
use App\Http\Controllers\Backend\JourneyController;
use App\Http\Controllers\Backend\GalleryController;
use App\Http\Controllers\Backend\PostController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\ContactSettingController;
use App\Http\Controllers\Backend\NewsletterController;

Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::middleware(['admin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Homepage Settings Core
    Route::get('/homepage-settings', [HomepageSettingController::class, 'index'])->name('homepage_settings.index');
    Route::post('/homepage-settings', [HomepageSettingController::class, 'update'])->name('homepage_settings.update');

    // General Settings
    Route::get('/settings/site', [SettingController::class, 'site'])->name('settings.site');
    Route::get('/settings/footer', [SettingController::class, 'footer'])->name('settings.footer');
    Route::get('/settings/other', [SettingController::class, 'other'])->name('settings.other');
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');

    // Featured Projects
    Route::resource('featured-project', FeaturedProjectController::class);
    Route::get('featured-project-details', [FeaturedProjectController::class, 'detailsIndex'])->name('featured-project.details.index');
    Route::get('featured-project-details/{featuredProject}/edit', [FeaturedProjectController::class, 'detailsEdit'])->name('featured-project.details.edit');
    Route::post('featured-project-details/{featuredProject}', [FeaturedProjectController::class, 'detailsUpdate'])->name('featured-project.details.update');

    Route::resource('team-members', TeamMemberController::class);
    Route::resource('testimonials', TestimonialController::class);

    // Properties Management
    Route::resource('properties', PropertyController::class);
    Route::post('properties/{property}/details', [PropertyController::class, 'updateDetails'])->name('properties.updateDetails');
    Route::post('properties/{property}/construction-status', [PropertyController::class, 'updateConstruction'])->name('properties.updateConstruction');
    Route::resource('property-types', PropertyTypeController::class);

    // Manage Property Details & Construction Status Routes
    Route::get('properties-manage', [PropertyController::class, 'manageIndex'])->name('properties.manage.index');
    Route::get('properties-manage/{property}/edit', [PropertyController::class, 'manageEdit'])->name('properties.manage.edit');

    // Submissions Management
    Route::get('submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::delete('submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');

    // Newsletters Management
    Route::resource('newsletters', NewsletterController::class)->only(['index', 'destroy']);

    // Services Management
    Route::resource('services', ServiceController::class);

    // Amenities Management
    Route::resource('amenities', AmenityController::class);

    // About Us Management
    Route::get('about-settings', [AboutSettingController::class, 'index'])->name('about_settings.index');
    Route::post('about-settings', [AboutSettingController::class, 'update'])->name('about_settings.update');
    Route::resource('journeys', JourneyController::class);

    // Contact Page Settings Management
    Route::get('contact-settings', [ContactSettingController::class, 'index'])->name('contact_settings.index');
    Route::post('contact-settings', [ContactSettingController::class, 'update'])->name('contact_settings.update');

    // Gallery Management
    Route::resource('galleries', GalleryController::class);
    Route::post('gallery-settings', [GalleryController::class, 'updateSettings'])->name('galleries.updateSettings');

    // Blog & Posts Management
    Route::resource('posts', PostController::class);
    Route::post('posts-settings', [PostController::class, 'updateSettings'])->name('posts.updateSettings');

    // FAQs Management
    Route::resource('faqs', FaqController::class);
    Route::post('faqs-settings', [FaqController::class, 'updateSettings'])->name('faqs.updateSettings');
});

