<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\ERP\Block;
use App\Models\ERP\Branch;
use App\Models\ERP\Building;
use App\Models\ERP\Company;
use App\Models\ERP\Department;
use App\Models\ERP\Floor;
use App\Models\ERP\Project;
use App\Models\ERP\PublicProjectLink;
use App\Models\ERP\PublicPropertyLink;
use App\Models\ERP\Road;
use App\Models\ERP\Unit;
use App\Models\ERP\UnitPriceHistory;
use App\Models\ERP\UnitStatusEvent;
use App\Models\FeaturedProject;
use App\Models\Property;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ERPPhase1Seeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'admin';

        // 1. ERP Permissions
        $erpPermissions = [
            'erp.dashboard.view',
            'erp.companies.view',
            'erp.companies.manage',
            'erp.projects.view',
            'erp.projects.create',
            'erp.projects.edit',
            'erp.projects.delete',
            'erp.units.view',
            'erp.units.create',
            'erp.units.edit',
            'erp.units.delete',
        ];

        foreach ($erpPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => $guard]);
        }

        // Assign to Super Admin and Manager
        $superAdmin = Role::where(['name' => 'Super Admin', 'guard_name' => $guard])->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($erpPermissions);
        }

        $manager = Role::where(['name' => 'Manager', 'guard_name' => $guard])->first();
        if ($manager) {
            $manager->givePermissionTo([
                'erp.dashboard.view',
                'erp.companies.view',
                'erp.projects.view',
                'erp.projects.create',
                'erp.projects.edit',
                'erp.units.view',
                'erp.units.create',
                'erp.units.edit',
            ]);
        }

        $adminUser = Admin::first();
        $adminId = $adminUser?->id;

        // 2. Company & Branches
        $company = Company::firstOrCreate(
            ['code' => 'HTEK-AURELIA'],
            [
                'name' => 'H Tech Provision IT — Aurelia Estates',
                'currency' => 'BDT',
                'email' => 'erp@aureliaestates.com',
                'phone' => '+880 1700-000000',
                'address' => 'Plot 42, Road 11, Block D, Banani, Dhaka-1213, Bangladesh',
                'tax_id' => 'TIN-9876543210',
                'is_active' => true,
            ]
        );

        $branchDhaka = Branch::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'DHK-01'],
            [
                'name' => 'Dhaka Corporate Office',
                'phone' => '+880 1700-111111',
                'email' => 'dhaka@aureliaestates.com',
                'address' => 'Banani, Dhaka',
                'is_active' => true,
            ]
        );

        $branchCtg = Branch::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'CTG-01'],
            [
                'name' => 'Chattogram Regional Branch',
                'phone' => '+880 1700-222222',
                'email' => 'ctg@aureliaestates.com',
                'address' => 'GEC Circle, Chattogram',
                'is_active' => true,
            ]
        );

        // Departments
        Department::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Real Estate Sales & CRM'],
            ['code' => 'SALES']
        );
        Department::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Engineering & Construction'],
            ['code' => 'ENG']
        );
        Department::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Finance & Accounts'],
            ['code' => 'FIN']
        );

        // 3. Project A: Residential Flat Tower
        $skyline = Project::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'PRJ-SKYLINE'],
            [
                'branch_id' => $branchDhaka->id,
                'name' => 'Aurelia Skyline Tower',
                'type' => 'flat',
                'status' => 'ongoing',
                'address' => 'Road 79, Gulshan-2, Dhaka',
                'location' => 'Gulshan 2, Dhaka',
                'land_area' => '15',
                'land_area_unit' => 'katha',
                'ownership_type' => 'joint_venture',
                'launch_date' => '2025-01-15',
                'target_end_date' => '2027-12-31',
                'budget' => 250000000.00,
                'description' => 'A premier 14-story residential luxury tower with panoramic lake view, rooftop pool, and club facilities.',
                'is_active' => true,
                'created_by' => $adminId,
            ]
        );

        $towerA = Building::firstOrCreate(
            ['project_id' => $skyline->id, 'code' => 'TOWER-A'],
            [
                'company_id' => $company->id,
                'name' => 'Tower A (Lake Facing)',
                'total_floors' => 14,
                'total_units' => 28,
                'status' => 'under_construction',
            ]
        );

        $floors = [];
        for ($f = 1; $f <= 5; $f++) {
            $floors[$f] = Floor::firstOrCreate(
                ['building_id' => $towerA->id, 'floor_number' => $f],
                [
                    'company_id' => $company->id,
                    'floor_name' => "Floor {$f}",
                ]
            );
        }

        // Flats in Tower A
        $flatUnits = [
            [
                'unit_code' => 'SKY-2A',
                'title' => 'Luxury Lakeview Suite 2A',
                'floor_id' => $floors[2]->id,
                'facing' => 'south_east',
                'area' => 2450.00,
                'asking_price' => 36750000.00, // 15,000 / sqft
                'min_price' => 35000000.00,
                'bedrooms' => 4,
                'bathrooms' => 4,
                'balconies' => 3,
                'parking_included' => true,
                'status' => 'available',
            ],
            [
                'unit_code' => 'SKY-2B',
                'title' => 'Executive Suite 2B',
                'floor_id' => $floors[2]->id,
                'facing' => 'south',
                'area' => 1950.00,
                'asking_price' => 27300000.00, // 14,000 / sqft
                'min_price' => 26000000.00,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'balconies' => 2,
                'parking_included' => true,
                'status' => 'held',
            ],
            [
                'unit_code' => 'SKY-3A',
                'title' => 'Deluxe Residence 3A',
                'floor_id' => $floors[3]->id,
                'facing' => 'south_east',
                'area' => 2450.00,
                'asking_price' => 37000000.00,
                'min_price' => 35500000.00,
                'bedrooms' => 4,
                'bathrooms' => 4,
                'balconies' => 3,
                'parking_included' => true,
                'status' => 'booked',
            ],
            [
                'unit_code' => 'SKY-4A',
                'title' => 'Penthouse Floor Residence 4A',
                'floor_id' => $floors[4]->id,
                'facing' => 'south_east',
                'area' => 2800.00,
                'asking_price' => 44800000.00, // 16,000 / sqft
                'min_price' => 43000000.00,
                'bedrooms' => 4,
                'bathrooms' => 5,
                'balconies' => 4,
                'parking_included' => true,
                'status' => 'sold',
            ],
        ];

        foreach ($flatUnits as $fu) {
            $unit = Unit::firstOrCreate(
                ['project_id' => $skyline->id, 'unit_code' => $fu['unit_code']],
                [
                    'company_id' => $company->id,
                    'building_id' => $towerA->id,
                    'floor_id' => $fu['floor_id'],
                    'title' => $fu['title'],
                    'type' => 'flat',
                    'status' => $fu['status'],
                    'facing' => $fu['facing'],
                    'area' => $fu['area'],
                    'area_unit' => 'sqft',
                    'asking_price' => $fu['asking_price'],
                    'min_price' => $fu['min_price'],
                    'bedrooms' => $fu['bedrooms'],
                    'bathrooms' => $fu['bathrooms'],
                    'balconies' => $fu['balconies'],
                    'parking_included' => $fu['parking_included'],
                    'created_by' => $adminId,
                ]
            );

            UnitStatusEvent::firstOrCreate(
                ['unit_id' => $unit->id, 'from_status' => 'none', 'to_status' => $fu['status']],
                [
                    'company_id' => $company->id,
                    'actor_id' => $adminId,
                    'notes' => 'Inventory seeded',
                    'created_at' => now(),
                ]
            );

            UnitPriceHistory::firstOrCreate(
                ['unit_id' => $unit->id, 'old_price' => $fu['asking_price'], 'new_price' => $fu['asking_price']],
                [
                    'company_id' => $company->id,
                    'changed_by' => $adminId,
                    'reason' => 'Initial launch price',
                    'created_at' => now(),
                ]
            );
        }

        // 4. Project B: Plot / Land Development Project
        $greenValley = Project::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'PRJ-GREEN-VALLEY'],
            [
                'branch_id' => $branchDhaka->id,
                'name' => 'Aurelia Green Valley Township',
                'type' => 'plot',
                'status' => 'ongoing',
                'address' => 'Sector 18, Purbachal New Town, Dhaka',
                'location' => 'Purbachal, Dhaka',
                'land_area' => '100',
                'land_area_unit' => 'bigha',
                'ownership_type' => 'own_land',
                'launch_date' => '2024-06-01',
                'target_end_date' => '2028-12-31',
                'budget' => 500000000.00,
                'description' => 'A planned green satellite mega-township featuring wide avenues, parks, underground utilities, and demarcation plots.',
                'is_active' => true,
                'created_by' => $adminId,
            ]
        );

        $blockA = Block::firstOrCreate(
            ['project_id' => $greenValley->id, 'code' => 'BLOCK-A'],
            [
                'company_id' => $company->id,
                'name' => 'Block A (Lakefront)',
                'description' => 'VIP residential sector fronting the central artificial lake',
            ]
        );

        $road1 = Road::firstOrCreate(
            ['block_id' => $blockA->id, 'road_name_or_no' => 'Lake Avenue (Road 101)'],
            [
                'company_id' => $company->id,
                'width_ft' => 60.00,
            ]
        );

        $plotUnits = [
            [
                'unit_code' => 'PLOT-A101',
                'title' => 'Prime Corner Lakefront Plot 101',
                'road_id' => $road1->id,
                'facing' => 'south',
                'area' => 5.00, // 5 katha
                'area_unit' => 'katha',
                'asking_price' => 12500000.00, // 25 lac / katha
                'min_price' => 12000000.00,
                'road_width_ft' => 60.00,
                'status' => 'available',
            ],
            [
                'unit_code' => 'PLOT-A102',
                'title' => 'Lakefront Plot 102',
                'road_id' => $road1->id,
                'facing' => 'south',
                'area' => 5.00,
                'area_unit' => 'katha',
                'asking_price' => 11500000.00,
                'min_price' => 11000000.00,
                'road_width_ft' => 60.00,
                'status' => 'held',
            ],
            [
                'unit_code' => 'PLOT-A105',
                'title' => 'Large Estate Plot 105',
                'road_id' => $road1->id,
                'facing' => 'north_east',
                'area' => 10.00,
                'area_unit' => 'katha',
                'asking_price' => 24000000.00,
                'min_price' => 23000000.00,
                'road_width_ft' => 60.00,
                'status' => 'available',
            ],
        ];

        foreach ($plotUnits as $pu) {
            $unit = Unit::firstOrCreate(
                ['project_id' => $greenValley->id, 'unit_code' => $pu['unit_code']],
                [
                    'company_id' => $company->id,
                    'block_id' => $blockA->id,
                    'road_id' => $pu['road_id'],
                    'title' => $pu['title'],
                    'type' => 'plot',
                    'status' => $pu['status'],
                    'facing' => $pu['facing'],
                    'area' => $pu['area'],
                    'area_unit' => $pu['area_unit'],
                    'asking_price' => $pu['asking_price'],
                    'min_price' => $pu['min_price'],
                    'road_width_ft' => $pu['road_width_ft'],
                    'created_by' => $adminId,
                ]
            );

            UnitStatusEvent::firstOrCreate(
                ['unit_id' => $unit->id, 'from_status' => 'none', 'to_status' => $pu['status']],
                [
                    'company_id' => $company->id,
                    'actor_id' => $adminId,
                    'notes' => 'Plot inventory seeded',
                    'created_at' => now(),
                ]
            );
        }

        // 5. Project C: Commercial Complex
        $bizHub = Project::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'PRJ-BIZ-HUB'],
            [
                'branch_id' => $branchDhaka->id,
                'name' => 'Aurelia Business Hub',
                'type' => 'commercial',
                'status' => 'planning',
                'address' => 'Kemal Ataturk Avenue, Banani, Dhaka',
                'location' => 'Banani, Dhaka',
                'land_area' => '20',
                'land_area_unit' => 'katha',
                'ownership_type' => 'developer_share',
                'launch_date' => '2026-03-01',
                'target_end_date' => '2029-06-30',
                'budget' => 450000000.00,
                'description' => 'Grade-A modern commercial shopping atrium and corporate office tower in the heart of Banani.',
                'is_active' => true,
                'created_by' => $adminId,
            ]
        );

        $commBldg = Building::firstOrCreate(
            ['project_id' => $bizHub->id, 'code' => 'COMM-BLDG-1'],
            [
                'company_id' => $company->id,
                'name' => 'Commercial Atrium & Tower',
                'total_floors' => 18,
                'total_units' => 60,
                'status' => 'under_construction',
            ]
        );

        $groundFloor = Floor::firstOrCreate(
            ['building_id' => $commBldg->id, 'floor_number' => 0],
            ['company_id' => $company->id, 'floor_name' => 'Ground Floor (Retail Atrium)']
        );
        $fourthFloor = Floor::firstOrCreate(
            ['building_id' => $commBldg->id, 'floor_number' => 4],
            ['company_id' => $company->id, 'floor_name' => 'Level 4 (Corporate Suites)']
        );

        $commUnits = [
            [
                'unit_code' => 'SHOP-G01',
                'title' => 'Anchor Brand Retail Shop G01',
                'floor_id' => $groundFloor->id,
                'type' => 'shop',
                'facing' => 'north',
                'area' => 1200.00,
                'asking_price' => 36000000.00, // 30,000 / sqft
                'min_price' => 34000000.00,
                'status' => 'available',
            ],
            [
                'unit_code' => 'OFF-401',
                'title' => 'Executive Corporate Office 401',
                'floor_id' => $fourthFloor->id,
                'type' => 'office',
                'facing' => 'east',
                'area' => 3500.00,
                'asking_price' => 63000000.00, // 18,000 / sqft
                'min_price' => 60000000.00,
                'status' => 'available',
            ],
        ];

        foreach ($commUnits as $cu) {
            $unit = Unit::firstOrCreate(
                ['project_id' => $bizHub->id, 'unit_code' => $cu['unit_code']],
                [
                    'company_id' => $company->id,
                    'building_id' => $commBldg->id,
                    'floor_id' => $cu['floor_id'],
                    'title' => $cu['title'],
                    'type' => $cu['type'],
                    'status' => $cu['status'],
                    'facing' => $cu['facing'],
                    'area' => $cu['area'],
                    'area_unit' => 'sqft',
                    'asking_price' => $cu['asking_price'],
                    'min_price' => $cu['min_price'],
                    'created_by' => $adminId,
                ]
            );

            UnitStatusEvent::firstOrCreate(
                ['unit_id' => $unit->id, 'from_status' => 'none', 'to_status' => $cu['status']],
                [
                    'company_id' => $company->id,
                    'actor_id' => $adminId,
                    'notes' => 'Commercial inventory seeded',
                    'created_at' => now(),
                ]
            );
        }

        // 6. Map Existing Featured Project & Property if available
        $featured = FeaturedProject::first();
        if ($featured) {
            PublicProjectLink::firstOrCreate(
                ['featured_project_id' => $featured->id],
                ['erp_project_id' => $skyline->id, 'notes' => 'Linked to Aurelia Skyline Tower']
            );
        }

        $property = Property::first();
        $sampleUnit = Unit::first();
        if ($property && $sampleUnit) {
            PublicPropertyLink::firstOrCreate(
                ['property_id' => $property->id],
                ['unit_id' => $sampleUnit->id, 'sync_status' => 'synced', 'notes' => 'Website listing linked to ERP inventory unit']
            );
        }
    }
}
