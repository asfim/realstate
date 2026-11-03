<?php

use App\Http\Controllers\ERP\AgencyDealController;
use App\Http\Controllers\ERP\AgencyLeaseController;
use App\Http\Controllers\ERP\AgencyListingController;
use App\Http\Controllers\ERP\AuditLogController;
use App\Http\Controllers\ERP\BranchController;
use App\Http\Controllers\ERP\CompanyController;
use App\Http\Controllers\ERP\CostCenterController;
use App\Http\Controllers\ERP\CrmActivityController;
use App\Http\Controllers\ERP\CrmCustomerController;
use App\Http\Controllers\ERP\CrmLeadController;
use App\Http\Controllers\ERP\CrmSiteVisitController;
use App\Http\Controllers\ERP\DepartmentController;
use App\Http\Controllers\ERP\DesignationController;
use App\Http\Controllers\ERP\DevParkingController;
use App\Http\Controllers\ERP\DevProjectController;
use App\Http\Controllers\ERP\DevSalesBookingController;
use App\Http\Controllers\ERP\DevUnitController;
use App\Http\Controllers\ERP\DocumentSequenceController;
use App\Http\Controllers\ERP\ErpDashboardController;
use App\Http\Controllers\ERP\FiscalYearController;
use App\Http\Controllers\ERP\LandFeasibilityController;
use App\Http\Controllers\ERP\LandParcelController;
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

// Phase 2: CRM & Lead Engine
Route::prefix('crm')->group(function () {
    Route::post('leads/import-submissions', [CrmLeadController::class, 'importSubmissions'])->name('leads.import-submissions');
    Route::post('leads/{lead}/stage', [CrmLeadController::class, 'updateStage'])->name('leads.update-stage');
    Route::post('leads/{lead}/convert', [CrmCustomerController::class, 'convertLead'])->name('leads.convert');
    Route::post('leads/{lead}/activities', [CrmActivityController::class, 'store'])->name('leads.activities.store');
    Route::post('leads/{lead}/site-visits', [CrmSiteVisitController::class, 'store'])->name('leads.site-visits.store');
    Route::resource('leads', CrmLeadController::class);

    Route::get('site-visits', [CrmSiteVisitController::class, 'index'])->name('site-visits.index');
    Route::put('site-visits/{siteVisit}', [CrmSiteVisitController::class, 'update'])->name('site-visits.update');

    Route::resource('customers', CrmCustomerController::class)->except(['destroy']);
});

// Phase 3: Land Acquisition, Joint Venture & Feasibility
Route::prefix('land')->group(function () {
    Route::post('parcels/{landParcel}/owners', [LandParcelController::class, 'addOwner'])->name('land-parcels.owners.store');
    Route::put('vettings/{vetting}', [LandParcelController::class, 'updateVetting'])->name('land-vettings.update');
    Route::get('parcels/{landParcel}/feasibility/create', [LandFeasibilityController::class, 'create'])->name('land-feasibility.create');
    Route::post('parcels/{landParcel}/feasibility', [LandFeasibilityController::class, 'store'])->name('land-feasibility.store');
    Route::get('feasibility/{feasibility}', [LandFeasibilityController::class, 'show'])->name('land-feasibility.show');
    Route::post('feasibility/{feasibility}/approve', [LandFeasibilityController::class, 'approve'])->name('land-feasibility.approve');
    Route::resource('parcels', LandParcelController::class)->names('land-parcels');
});

// Phase 4: Developer Sales, Inventory & Bookings
Route::prefix('developer')->group(function () {
    Route::post('projects/{devProject}/units', [DevUnitController::class, 'store'])->name('dev-units.store');
    Route::put('units/{unit}', [DevUnitController::class, 'update'])->name('dev-units.update');
    Route::delete('units/{unit}', [DevUnitController::class, 'destroy'])->name('dev-units.destroy');

    Route::post('projects/{devProject}/parking', [DevParkingController::class, 'store'])->name('dev-parking.store');

    Route::post('bookings/{devBooking}/cancel', [DevSalesBookingController::class, 'cancel'])->name('dev-bookings.cancel');
    Route::post('installments/{installment}/pay', [DevSalesBookingController::class, 'recordPayment'])->name('dev-installments.pay');
    Route::resource('bookings', DevSalesBookingController::class)->names('dev-bookings');

    Route::resource('projects', DevProjectController::class)->names('dev-projects');
});

// Phase 5: Brokerage, Agency & Rental Operations
Route::prefix('agency')->group(function () {
    Route::post('deals/{agencyDeal}/settle', [AgencyDealController::class, 'settle'])->name('agency-deals.settle');
    Route::resource('deals', AgencyDealController::class)->names('agency-deals');

    Route::post('leases/{agencyLease}/terminate', [AgencyLeaseController::class, 'terminate'])->name('agency-leases.terminate');
    Route::resource('leases', AgencyLeaseController::class)->names('agency-leases');

    Route::resource('listings', AgencyListingController::class)->names('agency-listings');
});

