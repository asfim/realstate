<?php

use App\Http\Controllers\ERP\CompanyController;
use App\Http\Controllers\ERP\DashboardController;
use App\Http\Controllers\ERP\ProjectController;
use App\Http\Controllers\ERP\UnitController;
use Illuminate\Support\Facades\Route;

// ERP Dashboard
Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('can:erp.dashboard.view');

// Companies & Branches
Route::get('companies', [CompanyController::class, 'index'])
    ->name('companies.index')
    ->middleware('can:erp.companies.view');
Route::post('companies', [CompanyController::class, 'store'])
    ->name('companies.store')
    ->middleware('can:erp.companies.manage');
Route::post('companies/{company}/branches', [CompanyController::class, 'storeBranch'])
    ->name('companies.branches.store')
    ->middleware('can:erp.companies.manage');

// Projects
Route::resource('projects', ProjectController::class)->middleware([
    'index' => 'can:erp.projects.view',
    'show' => 'can:erp.projects.view',
    'create' => 'can:erp.projects.create',
    'store' => 'can:erp.projects.create',
    'edit' => 'can:erp.projects.edit',
    'update' => 'can:erp.projects.edit',
    'destroy' => 'can:erp.projects.delete',
]);
Route::post('projects/{project}/buildings', [ProjectController::class, 'storeBuilding'])
    ->name('projects.buildings.store')
    ->middleware('can:erp.projects.edit');
Route::post('projects/{project}/blocks', [ProjectController::class, 'storeBlock'])
    ->name('projects.blocks.store')
    ->middleware('can:erp.projects.edit');

// Units (Inventory)
Route::get('units/hierarchy', [UnitController::class, 'getHierarchy'])
    ->name('units.hierarchy')
    ->middleware('can:erp.units.view');

Route::resource('units', UnitController::class)->middleware([
    'index' => 'can:erp.units.view',
    'show' => 'can:erp.units.view',
    'create' => 'can:erp.units.create',
    'store' => 'can:erp.units.create',
    'edit' => 'can:erp.units.edit',
    'update' => 'can:erp.units.edit',
    'destroy' => 'can:erp.units.delete',
]);
Route::post('units/{unit}/revise-price', [UnitController::class, 'revisePrice'])
    ->name('units.revise-price')
    ->middleware('can:erp.units.edit');
Route::post('units/{unit}/transition-status', [UnitController::class, 'transitionStatus'])
    ->name('units.transition-status')
    ->middleware('can:erp.units.edit');
Route::post('units/{unit}/link-property', [UnitController::class, 'linkWebsiteProperty'])
    ->name('units.link-property')
    ->middleware('can:erp.units.edit');
