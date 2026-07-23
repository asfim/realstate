<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AdminAuthController;

Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
