<?php

use App\Http\Controllers\ERP\AccountingController;
use App\Http\Controllers\ERP\BookingController;
use App\Http\Controllers\ERP\CompanyController;
use App\Http\Controllers\ERP\CustomerController;
use App\Http\Controllers\ERP\DashboardController;
use App\Http\Controllers\ERP\EmployeeController;
use App\Http\Controllers\ERP\PaymentController;
use App\Http\Controllers\ERP\PayrollController;
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

// Customers & Sales Bookings
Route::resource('customers', CustomerController::class)->middleware([
    'index' => 'can:erp.customers.view',
    'show' => 'can:erp.customers.view',
    'create' => 'can:erp.customers.create',
    'store' => 'can:erp.customers.create',
]);

Route::resource('bookings', BookingController::class)->middleware([
    'index' => 'can:erp.bookings.view',
    'show' => 'can:erp.bookings.view',
    'create' => 'can:erp.bookings.create',
    'store' => 'can:erp.bookings.create',
]);

Route::post('bookings/{booking}/payments', [PaymentController::class, 'store'])
    ->name('bookings.payments.store')
    ->middleware('can:erp.payments.collect');

Route::get('payments', [PaymentController::class, 'index'])
    ->name('payments.index')
    ->middleware('can:erp.payments.view');

Route::get('payments/{payment}', [PaymentController::class, 'show'])
    ->name('payments.show')
    ->middleware('can:erp.payments.view');

// Accounting & General Ledger
Route::prefix('accounting')->name('accounting.')->group(function () {
    Route::get('accounts', [AccountingController::class, 'accounts'])
        ->name('accounts')
        ->middleware('can:erp.accounting.view');
    Route::post('accounts', [AccountingController::class, 'storeAccount'])
        ->name('accounts.store')
        ->middleware('can:erp.accounting.manage');

    Route::get('journals', [AccountingController::class, 'journals'])
        ->name('journals')
        ->middleware('can:erp.accounting.view');
    Route::get('journals/create', [AccountingController::class, 'createJournal'])
        ->name('journals.create')
        ->middleware('can:erp.accounting.manage');
    Route::post('journals', [AccountingController::class, 'storeJournal'])
        ->name('journals.store')
        ->middleware('can:erp.accounting.manage');

    Route::get('profit-and-loss', [AccountingController::class, 'profitAndLoss'])
        ->name('profit-and-loss')
        ->middleware('can:erp.accounting.reports');
    Route::get('trial-balance', [AccountingController::class, 'trialBalance'])
        ->name('trial-balance')
        ->middleware('can:erp.accounting.reports');
    Route::get('balance-sheet', [AccountingController::class, 'balanceSheet'])
        ->name('balance-sheet')
        ->middleware('can:erp.accounting.reports');
    Route::get('customer-ledger', [AccountingController::class, 'customerLedger'])
        ->name('customer-ledger')
        ->middleware('can:erp.accounting.reports');
});

// HRM & Staff Payroll
Route::resource('employees', EmployeeController::class)->middleware([
    'index' => 'can:erp.hrm.view',
    'show' => 'can:erp.hrm.view',
    'create' => 'can:erp.hrm.manage',
    'store' => 'can:erp.hrm.manage',
    'edit' => 'can:erp.hrm.manage',
    'update' => 'can:erp.hrm.manage',
]);

Route::prefix('payroll')->name('payroll.')->group(function () {
    Route::get('/', [PayrollController::class, 'index'])
        ->name('index')
        ->middleware('can:erp.payroll.view');
    Route::post('generate', [PayrollController::class, 'generate'])
        ->name('generate')
        ->middleware('can:erp.payroll.manage');
    Route::get('{payrollRun}', [PayrollController::class, 'show'])
        ->name('show')
        ->middleware('can:erp.payroll.view');
});
