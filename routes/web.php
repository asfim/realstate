<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/projects', [FrontendController::class, 'projects'])->name('projects');
Route::get('/properties', [FrontendController::class, 'properties'])->name('properties');
Route::get('/services', [FrontendController::class, 'services'])->name('services');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/gallery', [FrontendController::class, 'gallery'])->name('gallery');
Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::get('/property/details', [FrontendController::class, 'propertyDetails'])->name('property.details');
Route::get('/property/construction-status', [FrontendController::class, 'constructionStatus'])->name('property.construction.status');
Route::get('/property/featured', [FrontendController::class, 'propertyFeatured'])->name('property.featured.details');
