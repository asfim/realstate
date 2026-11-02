<?php

namespace App\Services\ERP;

use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CompanyBranchContext
{
    public const SESSION_COMPANY_KEY = 'erp_active_company_id';
    public const SESSION_BRANCH_KEY = 'erp_active_branch_id';

    /**
     * Get the active company, resolving from session or the first active company.
     */
    public function currentCompany(): ?Company
    {
        $companyId = Session::get(self::SESSION_COMPANY_KEY);

        if ($companyId) {
            $company = Company::where('status', 'active')->find($companyId);
            if ($company) {
                return $company;
            }
        }

        $defaultCompany = Company::where('status', 'active')->first();
        if ($defaultCompany) {
            $this->setCompany($defaultCompany);
            return $defaultCompany;
        }

        return null;
    }

    /**
     * Get the active branch, resolving from session or the company's head office / first branch.
     */
    public function currentBranch(): ?Branch
    {
        $company = $this->currentCompany();
        if (!$company) {
            return null;
        }

        $branchId = Session::get(self::SESSION_BRANCH_KEY);

        if ($branchId) {
            $branch = Branch::where('company_id', $company->id)
                ->where('status', 'active')
                ->find($branchId);
            if ($branch) {
                return $branch;
            }
        }

        // Default to head office if exists, else first active branch
        $defaultBranch = Branch::where('company_id', $company->id)
            ->where('status', 'active')
            ->orderByDesc('is_head_office')
            ->first();

        if ($defaultBranch) {
            $this->setBranch($defaultBranch);
            return $defaultBranch;
        }

        return null;
    }

    /**
     * Set the active company. Also resets or updates the branch context.
     */
    public function setCompany(Company|int $company): void
    {
        $companyId = $company instanceof Company ? $company->id : $company;
        Session::put(self::SESSION_COMPANY_KEY, $companyId);

        // Reset branch if it doesn't belong to the newly selected company
        $currentBranchId = Session::get(self::SESSION_BRANCH_KEY);
        if ($currentBranchId) {
            $branchExists = Branch::where('id', $currentBranchId)
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->exists();
            if (!$branchExists) {
                Session::forget(self::SESSION_BRANCH_KEY);
            }
        }
    }

    /**
     * Set the active branch.
     */
    public function setBranch(Branch|int $branch): void
    {
        $branchId = $branch instanceof Branch ? $branch->id : $branch;
        $branchModel = $branch instanceof Branch ? $branch : Branch::find($branchId);

        if ($branchModel) {
            Session::put(self::SESSION_COMPANY_KEY, $branchModel->company_id);
            Session::put(self::SESSION_BRANCH_KEY, $branchModel->id);
        }
    }

    /**
     * Return list of active companies accessible.
     */
    public function accessibleCompanies(?Admin $admin = null): Collection
    {
        return Company::where('status', 'active')->orderBy('legal_name')->get();
    }

    /**
     * Return list of active branches for a company.
     */
    public function accessibleBranches(?Admin $admin = null, ?int $companyId = null): Collection
    {
        $companyId = $companyId ?? $this->currentCompany()?->id;
        if (!$companyId) {
            return collect();
        }

        return Branch::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderByDesc('is_head_office')
            ->orderBy('name')
            ->get();
    }

    /**
     * Ensure both company and branch are set in session.
     */
    public function ensureContext(?Admin $admin = null): void
    {
        $this->currentCompany();
        $this->currentBranch();
    }
}
