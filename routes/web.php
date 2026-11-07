<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\SystemUtilityController;

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/projects', [FrontendController::class, 'projects'])->name('projects');
Route::get('/project/featured/{featuredProject?}', [FrontendController::class, 'projectFeatured'])->name('project.featured');
Route::get('/properties', [FrontendController::class, 'properties'])->name('properties');
Route::get('/services', [FrontendController::class, 'services'])->name('services');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/gallery', [FrontendController::class, 'gallery'])->name('gallery');
Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [FrontendController::class, 'blogDetails'])->name('blog.details');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::get('/property/{property}/details', [FrontendController::class, 'propertyDetails'])->name('property.details');
Route::get('/property/{property}/construction-status', [FrontendController::class, 'constructionStatus'])->name('property.construction.status');
Route::post('/enquiry/submit', [FrontendController::class, 'submitEnquiry'])->name('enquiry.submit');
Route::post('/newsletter/subscribe', [FrontendController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');

/*
|--------------------------------------------------------------------------
| System & Maintenance Utilities (Artisan via Browser)
|--------------------------------------------------------------------------
*/
Route::get('/maintenance', [SystemUtilityController::class, 'index'])->name('system.maintenance');
Route::get('/clear-cache', [SystemUtilityController::class, 'clearCache'])->name('system.clear-cache');
Route::get('/cache-clear', [SystemUtilityController::class, 'clearCache'])->name('system.cache-clear.alias');
Route::get('/storage-link', [SystemUtilityController::class, 'storageLink'])->name('system.storage-link');
Route::get('/link-storage', [SystemUtilityController::class, 'storageLink'])->name('system.link-storage.alias');
Route::get('/migrate', [SystemUtilityController::class, 'migrate'])->name('system.migrate');
Route::get('/optimize', [SystemUtilityController::class, 'optimize'])->name('system.optimize');
Route::get('/db-seed', [SystemUtilityController::class, 'seed'])->name('system.seed');
Route::get('/seed', [SystemUtilityController::class, 'seed'])->name('system.seed.alias');
Route::get('/git-pull', [SystemUtilityController::class, 'gitPull'])->name('system.git-pull');



