<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\AuditLog;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct(protected CompanyBranchContext $context)
    {
    }

    public function index(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = AuditLog::where('company_id', $currentCompany?->id)->with(['actor', 'branch'])->latest();

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', '%' . $request->subject_type . '%');
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('erp.organization.audit_logs.index', compact('logs'));
    }
}

