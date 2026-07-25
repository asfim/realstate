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
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\AdminUserController;

Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::middleware(['admin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Homepage Settings Core
    Route::get('/homepage-settings', [HomepageSettingController::class, 'index'])->name('homepage_settings.index')->middleware('can:homepage-settings.view');
    Route::post('/homepage-settings', [HomepageSettingController::class, 'update'])->name('homepage_settings.update')->middleware('can:homepage-settings.edit');

    // General Settings
    Route::get('/settings/site', [SettingController::class, 'site'])->name('settings.site')->middleware('can:settings.view');
    Route::get('/settings/footer', [SettingController::class, 'footer'])->name('settings.footer')->middleware('can:settings.view');
    Route::get('/settings/other', [SettingController::class, 'other'])->name('settings.other')->middleware('can:settings.view');
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update')->middleware('can:settings.edit');

    // Featured Projects
    Route::resource('featured-project', FeaturedProjectController::class)->middleware([
        'index' => 'can:projects.view',
        'show' => 'can:projects.view',
        'create' => 'can:projects.create',
        'store' => 'can:projects.create',
        'edit' => 'can:projects.edit',
        'update' => 'can:projects.edit',
        'destroy' => 'can:projects.delete',
    ]);
    Route::get('featured-project-details', [FeaturedProjectController::class, 'detailsIndex'])->name('featured-project.details.index')->middleware('can:projects.view');
    Route::get('featured-project-details/{featuredProject}/edit', [FeaturedProjectController::class, 'detailsEdit'])->name('featured-project.details.edit')->middleware('can:projects.edit');
    Route::post('featured-project-details/{featuredProject}', [FeaturedProjectController::class, 'detailsUpdate'])->name('featured-project.details.update')->middleware('can:projects.edit');

    // Team Members & Testimonials
    Route::resource('team-members', TeamMemberController::class)->middleware([
        'index' => 'can:team-members.view',
        'show' => 'can:team-members.view',
        'create' => 'can:team-members.create',
        'store' => 'can:team-members.create',
        'edit' => 'can:team-members.edit',
        'update' => 'can:team-members.edit',
        'destroy' => 'can:team-members.delete',
    ]);
    
    Route::resource('testimonials', TestimonialController::class)->middleware([
        'index' => 'can:testimonials.view',
        'show' => 'can:testimonials.view',
        'create' => 'can:testimonials.create',
        'store' => 'can:testimonials.create',
        'edit' => 'can:testimonials.edit',
        'update' => 'can:testimonials.edit',
        'destroy' => 'can:testimonials.delete',
    ]);

    // Properties Management
    Route::resource('properties', PropertyController::class)->middleware([
        'index' => 'can:properties.view',
        'show' => 'can:properties.view',
        'create' => 'can:properties.create',
        'store' => 'can:properties.create',
        'edit' => 'can:properties.edit',
        'update' => 'can:properties.edit',
        'destroy' => 'can:properties.delete',
    ]);
    Route::post('properties/{property}/details', [PropertyController::class, 'updateDetails'])->name('properties.updateDetails')->middleware('can:properties.edit');
    Route::post('properties/{property}/construction-status', [PropertyController::class, 'updateConstruction'])->name('properties.updateConstruction')->middleware('can:properties.edit');
    Route::resource('property-types', PropertyTypeController::class)->middleware([
        'index' => 'can:property-types.view',
        'show' => 'can:property-types.view',
        'create' => 'can:property-types.create',
        'store' => 'can:property-types.create',
        'edit' => 'can:property-types.edit',
        'update' => 'can:property-types.edit',
        'destroy' => 'can:property-types.delete',
    ]);

    // Manage Property Details & Construction Status Routes
    Route::get('properties-manage', [PropertyController::class, 'manageIndex'])->name('properties.manage.index')->middleware('can:properties.view');
    Route::get('properties-manage/{property}/edit', [PropertyController::class, 'manageEdit'])->name('properties.manage.edit')->middleware('can:properties.edit');

    // Submissions Management
    Route::get('submissions', [SubmissionController::class, 'index'])->name('submissions.index')->middleware('can:submissions.view');
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show')->middleware('can:submissions.view');
    Route::delete('submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy')->middleware('can:submissions.delete');

    // Newsletters Management
    Route::resource('newsletters', NewsletterController::class)->only(['index', 'destroy'])->middleware([
        'index' => 'can:newsletters.view',
        'destroy' => 'can:newsletters.delete',
    ]);

    // Services Management
    Route::resource('services', ServiceController::class)->middleware([
        'index' => 'can:services.view',
        'show' => 'can:services.view',
        'create' => 'can:services.create',
        'store' => 'can:services.create',
        'edit' => 'can:services.edit',
        'update' => 'can:services.edit',
        'destroy' => 'can:services.delete',
    ]);

    // Amenities Management
    Route::resource('amenities', AmenityController::class)->middleware([
        'index' => 'can:amenities.view',
        'show' => 'can:amenities.view',
        'create' => 'can:amenities.create',
        'store' => 'can:amenities.create',
        'edit' => 'can:amenities.edit',
        'update' => 'can:amenities.edit',
        'destroy' => 'can:amenities.delete',
    ]);

    // About Us Management
    Route::get('about-settings', [AboutSettingController::class, 'index'])->name('about_settings.index')->middleware('can:about-settings.view');
    Route::post('about-settings', [AboutSettingController::class, 'update'])->name('about_settings.update')->middleware('can:about-settings.edit');
    Route::resource('journeys', JourneyController::class)->middleware([
        'index' => 'can:journeys.view',
        'show' => 'can:journeys.view',
        'create' => 'can:journeys.create',
        'store' => 'can:journeys.create',
        'edit' => 'can:journeys.edit',
        'update' => 'can:journeys.edit',
        'destroy' => 'can:journeys.delete',
    ]);

    // Contact Page Settings Management
    Route::get('contact-settings', [ContactSettingController::class, 'index'])->name('contact_settings.index')->middleware('can:contact-settings.view');
    Route::post('contact-settings', [ContactSettingController::class, 'update'])->name('contact_settings.update')->middleware('can:contact-settings.edit');

    // Gallery Management
    Route::resource('galleries', GalleryController::class)->middleware([
        'index' => 'can:galleries.view',
        'show' => 'can:galleries.view',
        'create' => 'can:galleries.create',
        'store' => 'can:galleries.create',
        'edit' => 'can:galleries.edit',
        'update' => 'can:galleries.edit',
        'destroy' => 'can:galleries.delete',
    ]);
    Route::post('gallery-settings', [GalleryController::class, 'updateSettings'])->name('galleries.updateSettings')->middleware('can:galleries.edit');

    // Blog & Posts Management
    Route::resource('posts', PostController::class)->middleware([
        'index' => 'can:posts.view',
        'show' => 'can:posts.view',
        'create' => 'can:posts.create',
        'store' => 'can:posts.create',
        'edit' => 'can:posts.edit',
        'update' => 'can:posts.edit',
        'destroy' => 'can:posts.delete',
    ]);
    Route::post('posts-settings', [PostController::class, 'updateSettings'])->name('posts.updateSettings')->middleware('can:posts.edit');

    // FAQs Management
    Route::resource('faqs', FaqController::class)->middleware([
        'index' => 'can:faqs.view',
        'show' => 'can:faqs.view',
        'create' => 'can:faqs.create',
        'store' => 'can:faqs.create',
        'edit' => 'can:faqs.edit',
        'update' => 'can:faqs.edit',
        'destroy' => 'can:faqs.delete',
    ]);
    Route::post('faqs-settings', [FaqController::class, 'updateSettings'])->name('faqs.updateSettings')->middleware('can:faqs.edit');

    // Roles & Permissions Management
    Route::resource('roles', RoleController::class)->middleware([
        'index' => 'can:roles.view',
        'show' => 'can:roles.view',
        'create' => 'can:roles.create',
        'store' => 'can:roles.create',
        'edit' => 'can:roles.edit',
        'update' => 'can:roles.edit',
        'destroy' => 'can:roles.delete',
    ]);

    // Admin User Management
    Route::resource('admins', AdminUserController::class)->middleware([
        'index' => 'can:admins.view',
        'show' => 'can:admins.view',
        'create' => 'can:admins.create',
        'store' => 'can:admins.create',
        'edit' => 'can:admins.edit',
        'update' => 'can:admins.edit',
        'destroy' => 'can:admins.delete',
    ]);
});
