<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\CostCenter;
use App\Models\ERP\Department;
use App\Models\ERP\Designation;
use App\Models\ERP\DocumentSequence;
use App\Models\ERP\FiscalYear;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ErpFoundationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or get Company
        $company = Company::firstOrCreate(
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

        $admin = Admin::first();
        $adminId = $admin?->id;

        // 2. Branches
        $hq = Branch::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'DHK-HQ'],
            [
                'name'             => 'Dhaka Corporate HQ',
                'address'          => 'Level 12, Aurelia Tower, Gulshan-2, Dhaka 1212',
                'phone'            => '+880 2 9887766',
                'email'            => 'hq@aureliaestates.com',
                'manager_admin_id' => $adminId,
                'is_head_office'   => true,
                'status'           => 'active',
            ]
        );

        $ctg = Branch::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'CTG-01'],
            [
                'name'             => 'Chattogram Regional Branch',
                'address'          => 'Akrani Center, Agrabad C/A, Chattogram',
                'phone'            => '+880 31 718899',
                'email'            => 'ctg@aureliaestates.com',
                'manager_admin_id' => $adminId,
                'is_head_office'   => false,
                'status'           => 'active',
            ]
        );

        $syl = Branch::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'SYL-01'],
            [
                'name'             => 'Sylhet Branch',
                'address'          => 'East Zindabazar, Sylhet',
                'phone'            => '+880 821 726655',
                'email'            => 'sylhet@aureliaestates.com',
                'manager_admin_id' => $adminId,
                'is_head_office'   => false,
                'status'           => 'active',
            ]
        );

        // 3. Fiscal Year
        $fy = FiscalYear::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'FY 2026-2027'],
            [
                'start_date' => '2026-07-01',
                'end_date'   => '2027-06-30',
                'status'     => 'active',
                'is_current' => true,
            ]
        );

        // 4. Departments & Designations
        $departmentsData = [
            'EXEC'  => [
                'name' => 'Executive & Administration',
                'designations' => ['Managing Director', 'Chief Operating Officer', 'Executive Assistant'],
            ],
            'LAND'  => [
                'name' => 'Land & Project Acquisition',
                'designations' => ['Land Acquisition Specialist', 'Legal & Documentation Officer'],
            ],
            'ENG'   => [
                'name' => 'Architecture & Civil Engineering',
                'designations' => ['Chief Structural Engineer', 'Site Supervisor', 'AutoCAD Designer'],
            ],
            'SALES' => [
                'name' => 'Sales & CRM',
                'designations' => ['Head of Sales', 'Senior Relationship Manager', 'Customer Care Executive'],
            ],
            'FIN'   => [
                'name' => 'Accounts & Finance',
                'designations' => ['Chief Financial Officer', 'Senior Accountant', 'Billing & Payroll Officer'],
            ],
            'HR'    => [
                'name' => 'Human Resources & Workforce',
                'designations' => ['HR Director', 'Talent & Labour Relations Officer'],
            ],
            'PROC'  => [
                'name' => 'Procurement & Inventory',
                'designations' => ['Procurement Manager', 'Store Keeper', 'Quality Inspector'],
            ],
        ];

        foreach ($departmentsData as $code => $data) {
            $dept = Department::firstOrCreate(
                ['company_id' => $company->id, 'code' => $code],
                ['name' => $data['name'], 'status' => 'active']
            );

            foreach ($data['designations'] as $desName) {
                Designation::firstOrCreate(
                    ['company_id' => $company->id, 'department_id' => $dept->id, 'name' => $desName],
                    ['grade' => 'Standard']
                );
            }
        }

        // 5. Cost Centers
        CostCenter::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'CC-CORP-01'],
            ['name' => 'Corporate Overhead & Administration', 'branch_id' => $hq->id, 'status' => 'active']
        );
        CostCenter::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'CC-CTG-01'],
            ['name' => 'Chattogram Operations', 'branch_id' => $ctg->id, 'status' => 'active']
        );
        CostCenter::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'CC-LAND-01'],
            ['name' => 'Land Scouting & Survey Pool', 'branch_id' => $hq->id, 'status' => 'active']
        );

        // 6. Document Sequences
        $docTypes = [
            'quotation' => 'QTE',
            'booking'   => 'BKG',
            'contract'  => 'CTR',
            'invoice'   => 'INV',
            'receipt'   => 'REC',
            'po'        => 'PO',
            'grn'       => 'GRN',
            'bill'      => 'BIL',
            'payment'   => 'PAY',
            'journal'   => 'JRN',
            'payroll'   => 'PRL',
        ];

        foreach ($docTypes as $type => $pfx) {
            DocumentSequence::firstOrCreate(
                [
                    'company_id'     => $company->id,
                    'branch_id'      => null,
                    'fiscal_year_id' => $fy->id,
                    'document_type'  => $type,
                ],
                [
                    'prefix'      => $pfx,
                    'next_number' => 1,
                    'padding'     => 5,
                ]
            );
        }

        // 7. Spatie ERP Permissions
        $permissions = [
            'erp.access',
            'company.view',
            'company.manage',
            'branch.view',
            'branch.manage',
            'department.view',
            'department.manage',
            'cost_center.view',
            'cost_center.manage',
            'fiscal_year.view',
            'fiscal_year.manage',
            'audit_log.view',
        ];

        foreach ($permissions as $permName) {
            Permission::findOrCreate($permName, 'admin');
        }

        $superAdminRole = Role::findOrCreate('Super Admin', 'admin');
        $superAdminRole->givePermissionTo(Permission::where('guard_name', 'admin')->get());

        if ($admin && !$admin->hasRole('Super Admin')) {
            $admin->assignRole($superAdminRole);
        }
    }
}

