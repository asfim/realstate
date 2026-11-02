<?php

use App\Http\Controllers\ERP\AuditLogController;
use App\Http\Controllers\ERP\BranchController;
use App\Http\Controllers\ERP\CompanyController;
use App\Http\Controllers\ERP\CostCenterController;
use App\Http\Controllers\ERP\DepartmentController;
use App\Http\Controllers\ERP\DesignationController;
use App\Http\Controllers\ERP\DocumentSequenceController;
use App\Http\Controllers\ERP\ErpDashboardController;
use App\Http\Controllers\ERP\FiscalYearController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Enterprise Real Estate ERP Routes
|--------------------------------------------------------------------------
| Prefix: /erp | Name: erp. | Middleware: ['web', 'admin', 'erp.context']
*/

Route::get('/', [ErpDashboardController::class, 'index'])->name('dashboard');
Route::post('/switch-context', [BranchController::class, 'switchContext'])->name('switch-context');

// Foundation & Organization Management
Route::prefix('organization')->group(function () {
    Route::resource('companies', CompanyController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('departments', DepartmentController::class)->except(['create', 'show', 'edit']);
    Route::resource('designations', DesignationController::class)->except(['create', 'show', 'edit']);
    Route::resource('cost-centers', CostCenterController::class)->except(['create', 'show', 'edit']);
    Route::resource('fiscal-years', FiscalYearController::class)->except(['create', 'show', 'edit']);
    Route::resource('document-sequences', DocumentSequenceController::class)->only(['index', 'store', 'update']);
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});
