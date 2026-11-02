<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\CostCenter;
use App\Models\ERP\Department;
use App\Models\ERP\FiscalYear;
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
            'branches_count'    => $currentCompany ? Branch::where('company_id', $currentCompany->id)->count() : 0,
            'departments_count' => $currentCompany ? Department::where('company_id', $currentCompany->id)->count() : 0,
            'cost_centers_count'=> $currentCompany ? CostCenter::where('company_id', $currentCompany->id)->count() : 0,
            'current_fy'        => $currentCompany ? FiscalYear::where('company_id', $currentCompany->id)->where('is_current', true)->first() : null,
        ];

        return view('erp.dashboard', compact('stats', 'currentCompany', 'currentBranch'));
    }
}
