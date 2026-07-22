<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.pages.home');
})->name('home');

Route::get('/projects', function () {
    return view('frontend.pages.projects');
})->name('projects');

Route::get('/properties', function () {
    return view('frontend.pages.properties');
})->name('properties');

Route::get('/services', function () {
    return view('frontend.pages.services');
})->name('services');

Route::get('/about', function () {
    return view('frontend.pages.about');
})->name('about');

Route::get('/gallery', function () {
    return view('frontend.pages.gallery');
})->name('gallery');

Route::get('/blog', function () {
    return view('frontend.pages.blog');
})->name('blog');

Route::get('/contact', function () {
    return view('frontend.pages.contact');
})->name('contact');

Route::get('/property/details', function () { return view('frontend.pages.property-details'); })->name('property.details');

Route::get('/property/construction-status', function () { return view('frontend.pages.construction-status'); })->name('property.construction.status');

Route::get('/property/featured', function () { return view('frontend.pages.property-featured'); })->name('property.featured.details');
