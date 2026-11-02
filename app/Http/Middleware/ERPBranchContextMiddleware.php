<?php

namespace App\Http\Middleware;

use App\Services\ERP\CompanyBranchContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ERPBranchContextMiddleware
{
    public function __construct(protected CompanyBranchContext $context)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = $request->user('admin');
        $this->context->ensureContext($admin);

        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();
        $accessibleCompanies = $this->context->accessibleCompanies($admin);
        $accessibleBranches = $this->context->accessibleBranches($admin, $currentCompany?->id);

        View::share('currentCompany', $currentCompany);
        View::share('currentBranch', $currentBranch);
        View::share('accessibleCompanies', $accessibleCompanies);
        View::share('accessibleBranches', $accessibleBranches);

        return $next($request);
    }
}
