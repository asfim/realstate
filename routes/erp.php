<?php

use App\Http\Controllers\ERP\AccCoaController;
use App\Http\Controllers\ERP\AccJournalController;
use App\Http\Controllers\ERP\AccReportController;
use App\Http\Controllers\ERP\AgencyDealController;
use App\Http\Controllers\ERP\AgencyLeaseController;
use App\Http\Controllers\ERP\AgencyListingController;
use App\Http\Controllers\ERP\AiAssistantController;
use App\Http\Controllers\ERP\AuditLogController;
use App\Http\Controllers\ERP\BranchController;
use App\Http\Controllers\ERP\CompanyController;
use App\Http\Controllers\ERP\ConBoqController;
use App\Http\Controllers\ERP\ConProcurementController;
use App\Http\Controllers\ERP\ConRequisitionController;
use App\Http\Controllers\ERP\ConVendorController;
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
use App\Http\Controllers\ERP\HrEmployeeController;
use App\Http\Controllers\ERP\HrLaborController;
use App\Http\Controllers\ERP\HrPayrollController;
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

// AI Database Assistant & Real-Time Query Engine
Route::prefix('ai-assistant')->group(function () {
    Route::get('/', [AiAssistantController::class, 'index'])->name('ai-assistant.index');
    Route::post('/query', [AiAssistantController::class, 'query'])->name('ai-assistant.query');
    Route::get('/overview', [AiAssistantController::class, 'quickOverview'])->name('ai-assistant.overview');
});

// Foundation & Organization Management
Route::prefix('organization')->group(function () {
    Route::resource('companies', CompanyController::class)->except(['show']);
    Route::resource('branches', BranchController::class)->except(['show']);
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

    Route::resource('customers', CrmCustomerController::class);
    Route::get('crm-customers/create', [CrmCustomerController::class, 'create'])->name('crm-customers.create');
});

// Phase 3: Land Acquisition, Joint Venture & Feasibility
Route::prefix('land')->group(function () {
    Route::post('parcels/{landParcel}/owners', [LandParcelController::class, 'addOwner'])->name('land-parcels.owners.store');
    Route::put('vettings/{vetting}', [LandParcelController::class, 'updateVetting'])->name('land-vettings.update');
    Route::get('parcels/{landParcel}/feasibility/create', [LandFeasibilityController::class, 'create'])->name('land-feasibility.create');
    Route::post('parcels/{landParcel}/feasibility', [LandFeasibilityController::class, 'store'])->name('land-feasibility.store');
    Route::get('feasibility/{feasibility}', [LandFeasibilityController::class, 'show'])->name('land-feasibility.show');
    Route::post('feasibility/{feasibility}/approve', [LandFeasibilityController::class, 'approve'])->name('land-feasibility.approve');
    Route::resource('parcels', LandParcelController::class)->parameters(['parcels' => 'landParcel'])->names('land-parcels');
});

// Phase 4: Developer Sales, Inventory & Bookings
Route::prefix('developer')->group(function () {
    Route::post('projects/{devProject}/units', [DevUnitController::class, 'store'])->name('dev-units.store');
    Route::put('units/{unit}', [DevUnitController::class, 'update'])->name('dev-units.update');
    Route::delete('units/{unit}', [DevUnitController::class, 'destroy'])->name('dev-units.destroy');

    Route::post('projects/{devProject}/parking', [DevParkingController::class, 'store'])->name('dev-parking.store');

    Route::post('bookings/{devBooking}/cancel', [DevSalesBookingController::class, 'cancel'])->name('dev-bookings.cancel');
    Route::post('installments/{installment}/pay', [DevSalesBookingController::class, 'recordPayment'])->name('dev-installments.pay');
    Route::resource('bookings', DevSalesBookingController::class)->parameters(['bookings' => 'devBooking'])->names('dev-bookings');

    Route::resource('projects', DevProjectController::class)->parameters(['projects' => 'devProject'])->names('dev-projects');
});

// Phase 5: Brokerage, Agency & Rental Operations
Route::prefix('agency')->group(function () {
    Route::post('deals/{agencyDeal}/settle', [AgencyDealController::class, 'settle'])->name('agency-deals.settle');
    Route::resource('deals', AgencyDealController::class)->parameters(['deals' => 'agencyDeal'])->names('agency-deals');

    Route::post('leases/{agencyLease}/terminate', [AgencyLeaseController::class, 'terminate'])->name('agency-leases.terminate');
    Route::resource('leases', AgencyLeaseController::class)->parameters(['leases' => 'agencyLease'])->names('agency-leases');

    Route::resource('listings', AgencyListingController::class)->parameters(['listings' => 'agencyListing'])->names('agency-listings');
});

// Phase 6: Construction, BOQ, Procurement & Site Materials
Route::prefix('construction')->group(function () {
    Route::resource('vendors', ConVendorController::class)->parameters(['vendors' => 'conVendor'])->names('con-vendors');

    Route::get('boq', [ConBoqController::class, 'index'])->name('con-boq.index');
    Route::post('boq', [ConBoqController::class, 'store'])->name('con-boq.store');
    Route::put('boq/{boqItem}/consumption', [ConBoqController::class, 'updateConsumption'])->name('con-boq.consumption');

    Route::post('requisitions/{conRequisition}/approve', [ConRequisitionController::class, 'approve'])->name('con-requisitions.approve');
    Route::resource('requisitions', ConRequisitionController::class)->parameters(['requisitions' => 'conRequisition'])->names('con-requisitions');

    Route::get('pos', [ConProcurementController::class, 'posIndex'])->name('con-pos.index');
    Route::get('pos/create', [ConProcurementController::class, 'poCreate'])->name('con-pos.create');
    Route::post('pos', [ConProcurementController::class, 'poStore'])->name('con-pos.store');
    Route::get('pos/{conPo}', [ConProcurementController::class, 'poShow'])->name('con-pos.show');

    Route::get('grns', [ConProcurementController::class, 'grnsIndex'])->name('con-grns.index');
    Route::get('grns/create', [ConProcurementController::class, 'grnCreate'])->name('con-grns.create');
    Route::post('grns', [ConProcurementController::class, 'grnStore'])->name('con-grns.store');
    Route::get('grns/{conGrn}', [ConProcurementController::class, 'grnShow'])->name('con-grns.show');

    Route::get('stocks', [ConProcurementController::class, 'stocksIndex'])->name('con-stocks.index');
});

// Phase 7: Double-Entry Financial Accounting, General Ledger & Reports
Route::prefix('accounting')->group(function () {
    Route::get('coa', [AccCoaController::class, 'index'])->name('acc-coa.index');
    Route::post('coa', [AccCoaController::class, 'store'])->name('acc-coa.store');
    Route::put('coa/{accCoa}', [AccCoaController::class, 'update'])->name('acc-coa.update');
    Route::delete('coa/{accCoa}', [AccCoaController::class, 'destroy'])->name('acc-coa.destroy');

    Route::resource('journals', AccJournalController::class)->only(['index', 'create', 'store', 'show', 'destroy'])->names('acc-journals');

    Route::get('reports/trial-balance', [AccReportController::class, 'trialBalance'])->name('acc-reports.trial-balance');
    Route::get('reports/profit-loss', [AccReportController::class, 'profitAndLoss'])->name('acc-reports.profit-loss');
    Route::get('reports/balance-sheet', [AccReportController::class, 'balanceSheet'])->name('acc-reports.balance-sheet');
});

// Phase 8: HR, Permanent Employee Payroll & Site Daily Labor Wages
Route::prefix('hr')->group(function () {
    Route::post('payrolls/{payroll}/pay', [HrPayrollController::class, 'pay'])->name('hr-payrolls.pay');
    Route::resource('payrolls', HrPayrollController::class)->only(['index', 'create', 'store', 'show', 'destroy'])->names('hr-payrolls');

    Route::get('labor', [HrLaborController::class, 'laborersIndex'])->name('hr-labor.index');
    Route::post('labor', [HrLaborController::class, 'laborerStore'])->name('hr-labor.laborers.store');
    Route::post('labor/store-alias', [HrLaborController::class, 'laborerStore'])->name('hr-labor.store');
    Route::get('labor/muster', [HrLaborController::class, 'musterIndex'])->name('hr-labor.muster');
    Route::post('labor/muster', [HrLaborController::class, 'musterStore'])->name('hr-labor.muster.store');

    Route::resource('employees', HrEmployeeController::class)->names('hr-employees');
});

