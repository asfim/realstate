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
     * Auto-provisions a default company if the database has no company record yet.
     */
    public function currentCompany(): ?Company
    {
        $companyId = Session::get(self::SESSION_COMPANY_KEY);

        if ($companyId) {
            $company = Company::find($companyId);
            if ($company) {
                if ($company->status !== 'active') {
                    $company->update(['status' => 'active']);
                }
                return $company;
            }
        }

        $defaultCompany = Company::where('status', 'active')->first() ?? Company::first();
        if ($defaultCompany) {
            if ($defaultCompany->status !== 'active') {
                $defaultCompany->update(['status' => 'active']);
            }
            $this->setCompany($defaultCompany);
            return $defaultCompany;
        }

        // Auto-provision default company and head office branch if no company exists yet
        try {
            $defaultCompany = Company::firstOrCreate(
                ['code' => 'AURELIA'],
                [
                    'legal_name'        => 'Aurelia Estates & Developers Ltd.',
                    'trade_name'        => 'Aurelia Living',
                    'registration_no'   => 'C-198425/2026',
                    'bin'               => '004829103-0101',
                    'tin'               => '491827401928',
                    'base_currency'     => 'BDT',
                    'fiscal_year_start' => '07-01',
                    'timezone'          => 'Asia/Dhaka',
                    'status'            => 'active',
                ]
            );

            Branch::firstOrCreate(
                ['company_id' => $defaultCompany->id, 'code' => 'DHK-HQ'],
                [
                    'name'           => 'Dhaka Corporate HQ',
                    'address'        => 'Level 12, Aurelia Tower, Gulshan-2, Dhaka 1212',
                    'phone'          => '+880 2 9887766',
                    'email'          => 'hq@aureliaestates.com',
                    'is_head_office' => true,
                    'status'         => 'active',
                ]
            );

            $this->setCompany($defaultCompany);
            return $defaultCompany;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get the active branch, resolving from session or the company's head office / first branch.
     * Auto-provisions head office if company has no branch yet.
     */
    public function currentBranch(): ?Branch
    {
        $company = $this->currentCompany();
        if (!$company) {
            return null;
        }

        $branchId = Session::get(self::SESSION_BRANCH_KEY);

        if ($branchId) {
            $branch = Branch::where('company_id', $company->id)->find($branchId);
            if ($branch) {
                if ($branch->status !== 'active') {
                    $branch->update(['status' => 'active']);
                }
                return $branch;
            }
        }

        // Default to head office if exists, else first branch
        $defaultBranch = Branch::where('company_id', $company->id)
            ->where('status', 'active')
            ->orderByDesc('is_head_office')
            ->first() ?? Branch::where('company_id', $company->id)->first();

        if ($defaultBranch) {
            if ($defaultBranch->status !== 'active') {
                $defaultBranch->update(['status' => 'active']);
            }
            $this->setBranch($defaultBranch);
            return $defaultBranch;
        }

        // Auto-provision branch for company
        try {
            $defaultBranch = Branch::firstOrCreate(
                ['company_id' => $company->id, 'code' => 'DHK-HQ'],
                [
                    'name'           => 'Dhaka Corporate HQ',
                    'address'        => 'Level 12, Aurelia Tower, Gulshan-2, Dhaka 1212',
                    'phone'          => '+880 2 9887766',
                    'email'          => 'hq@aureliaestates.com',
                    'is_head_office' => true,
                    'status'         => 'active',
                ]
            );

            $this->setBranch($defaultBranch);
            return $defaultBranch;
        } catch (\Throwable $e) {
            return null;
        }
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

