<?php

namespace App\Services\ERP;

use App\Models\ERP\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class AuditLogService
{
    /**
     * Record an audit trail log entry.
     */
    public function log(
        string $action,
        ?Model $subject = null,
        ?array $before = null,
        ?array $after = null,
        ?int $companyId = null,
        ?int $branchId = null
    ): AuditLog {
        $actor = Auth::guard('admin')->user() ?? Auth::user();
        $context = app(CompanyBranchContext::class);

        $companyId = $companyId ?? $context->currentCompany()?->id;
        $branchId = $branchId ?? $context->currentBranch()?->id;

        return AuditLog::create([
            'company_id'   => $companyId,
            'branch_id'    => $branchId,
            'actor_type'   => $actor ? get_class($actor) : null,
            'actor_id'     => $actor?->id,
            'action'       => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->id,
            'before_json'  => $before,
            'after_json'   => $after,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'request_id'   => Request::header('X-Request-ID') ?? (string) Str::uuid(),
            'created_at'   => now(),
        ]);
    }
}

