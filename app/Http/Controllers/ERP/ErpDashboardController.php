<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\AgencyDeal;
use App\Models\ERP\AgencyListing;
use App\Models\ERP\AgencyTenantLease;
use App\Models\ERP\ConGrn;
use App\Models\ERP\ConPurchaseOrder;
use App\Models\ERP\ConVendor;
use App\Models\ERP\CostCenter;
use App\Models\ERP\CrmCustomer;
use App\Models\ERP\CrmLead;
use App\Models\ERP\CrmSiteVisit;
use App\Models\ERP\Department;
use App\Models\ERP\DevProject;
use App\Models\ERP\DevProjectUnit;
use App\Models\ERP\DevSalesBooking;
use App\Models\ERP\FiscalYear;
use App\Models\ERP\HrDailyLaborer;
use App\Models\ERP\HrEmployee;
use App\Models\ERP\HrPayroll;
use App\Models\ERP\AccJournal;
use App\Models\ERP\LandParcel;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class ErpDashboardController extends Controller
{
    public function __construct(protected CompanyBranchContext $context)
    {
    }

    public function index()
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $stats = [
            'branches_count'     => $currentCompany ? Branch::where('company_id', $currentCompany->id)->count() : 0,
            'departments_count'  => $currentCompany ? Department::where('company_id', $currentCompany->id)->count() : 0,
            'cost_centers_count' => $currentCompany ? CostCenter::where('company_id', $currentCompany->id)->count() : 0,
            'current_fy'         => $currentCompany ? FiscalYear::where('company_id', $currentCompany->id)->where('is_current', true)->first() : null,
            'leads_count'        => $currentCompany ? CrmLead::where('company_id', $currentCompany->id)->count() : 0,
            'site_visits_count'  => $currentCompany ? CrmSiteVisit::where('company_id', $currentCompany->id)->count() : 0,
            'customers_count'    => $currentCompany ? CrmCustomer::where('company_id', $currentCompany->id)->count() : 0,
            'land_parcels_count' => $currentCompany ? LandParcel::where('company_id', $currentCompany->id)->count() : 0,
            'projects_count'     => $currentCompany ? DevProject::where('company_id', $currentCompany->id)->count() : 0,
            'units_count'        => $currentCompany ? DevProjectUnit::whereHas('project', fn($q) => $q->where('company_id', $currentCompany->id))->count() : 0,
            'bookings_count'     => $currentCompany ? DevSalesBooking::where('company_id', $currentCompany->id)->count() : 0,
            'bookings_value'     => $currentCompany ? DevSalesBooking::where('company_id', $currentCompany->id)->where('status', '!=', 'cancelled')->sum('total_contract_amount') : 0,
            'listings_count'     => $currentCompany ? AgencyListing::where('company_id', $currentCompany->id)->count() : 0,
            'deals_count'        => $currentCompany ? AgencyDeal::where('company_id', $currentCompany->id)->count() : 0,
            'agency_comm_sum'    => $currentCompany ? AgencyDeal::where('company_id', $currentCompany->id)->where('status', '!=', 'cancelled')->sum('company_commission_amount') : 0,
            'leases_count'       => $currentCompany ? AgencyTenantLease::where('company_id', $currentCompany->id)->where('status', 'active')->count() : 0,
            'vendors_count'      => $currentCompany ? ConVendor::where('company_id', $currentCompany->id)->count() : 0,
            'pos_count'          => $currentCompany ? ConPurchaseOrder::where('company_id', $currentCompany->id)->count() : 0,
            'pos_value'          => $currentCompany ? ConPurchaseOrder::where('company_id', $currentCompany->id)->where('status', '!=', 'cancelled')->sum('grand_total') : 0,
            'grns_count'         => $currentCompany ? ConGrn::where('company_id', $currentCompany->id)->count() : 0,
            'employees_count'    => $currentCompany ? HrEmployee::where('company_id', $currentCompany->id)->where('status', 'active')->count() : 0,
            'laborers_count'     => $currentCompany ? HrDailyLaborer::where('company_id', $currentCompany->id)->where('status', 'active')->count() : 0,
            'payrolls_count'     => $currentCompany ? HrPayroll::where('company_id', $currentCompany->id)->count() : 0,
            'journals_count'     => $currentCompany ? AccJournal::where('company_id', $currentCompany->id)->where('is_posted', true)->count() : 0,
        ];

        return view('erp.dashboard', compact('stats', 'currentCompany', 'currentBranch'));
    }
}

