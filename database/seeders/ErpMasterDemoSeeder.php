<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\ERP\AccChartOfAccount;
use App\Models\ERP\AccJournal;
use App\Models\ERP\AccJournalEntry;
use App\Models\ERP\AgencyDeal;
use App\Models\ERP\AgencyListing;
use App\Models\ERP\AgencyTenantLease;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\ConBoqItem;
use App\Models\ERP\ConGrn;
use App\Models\ERP\ConMaterialRequisition;
use App\Models\ERP\ConPoItem;
use App\Models\ERP\ConPurchaseOrder;
use App\Models\ERP\ConRequisitionItem;
use App\Models\ERP\ConSiteStock;
use App\Models\ERP\ConVendor;
use App\Models\ERP\CostCenter;
use App\Models\ERP\CrmCustomer;
use App\Models\ERP\CrmLead;
use App\Models\ERP\CrmSiteVisit;
use App\Models\ERP\Department;
use App\Models\ERP\Designation;
use App\Models\ERP\DevBookingInstallment;
use App\Models\ERP\DevParkingSlot;
use App\Models\ERP\DevProject;
use App\Models\ERP\DevProjectUnit;
use App\Models\ERP\DevSalesBooking;
use App\Models\ERP\FiscalYear;
use App\Models\ERP\HrDailyLaborer;
use App\Models\ERP\HrEmployee;
use App\Models\ERP\HrLaborMusterRoll;
use App\Models\ERP\HrPayroll;
use App\Models\ERP\LandFeasibility;
use App\Models\ERP\LandParcel;
use App\Models\ERP\LandParcelOwner;
use App\Models\ERP\LandVetting;
use Illuminate\Database\Seeder;

class ErpMasterDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            ErpFoundationSeeder::class,
            ErpChartOfAccountsSeeder::class,
        ]);

        $company = Company::where('code', 'AURELIA')->first();
        if (!$company) {
            return;
        }

        $branch = Branch::where('code', 'DHK-HQ')->first() ?? Branch::where('company_id', $company->id)->first();
        $admin = Admin::where('email', 'admin@example.com')->first();
        $fy = FiscalYear::where('company_id', $company->id)->where('is_current', true)->first();
        $costCenter = CostCenter::where('company_id', $company->id)->first();
        $deptEng = Department::where('company_id', $company->id)->where('code', 'ENG')->first();
        $deptSales = Department::where('company_id', $company->id)->where('code', 'SALES')->first();
        $deptFin = Department::where('company_id', $company->id)->where('code', 'FIN')->first();
        $desManager = Designation::where('company_id', $company->id)->where('name', 'Chief Structural Engineer')->first();
        $desAcc = Designation::where('company_id', $company->id)->where('name', 'Senior Accountant')->first();

        // 1. CRM Leads & Site Visits
        $lead1 = CrmLead::firstOrCreate(
            ['phone' => '01711001122'],
            [
                'company_id'             => $company->id,
                'branch_id'              => $branch->id,
                'lead_code'              => 'LED-2026-00001',
                'name'                   => 'Barrister Rafiqul Islam',
                'email'                  => 'rafiq@supremecourt-law.com',
                'source'                 => 'facebook',
                'stage'                  => 'site_visit_scheduled',
                'score'                  => 85,
                'min_budget'             => 25000000,
                'max_budget'             => 35000000,
                'preferred_location'     => 'Gulshan 2, Dhaka',
                'property_type_interest' => 'residential',
                'notes'                  => 'Interested in 3,000+ sqft 4-bedroom luxury duplex or top-floor penthouse.',
            ]
        );

        CrmSiteVisit::firstOrCreate(
            ['lead_id' => $lead1->id, 'scheduled_at' => '2026-11-05 15:00:00'],
            [
                'company_id'        => $company->id,
                'branch_id'         => $branch->id,
                'assigned_agent_id' => $admin?->id,
                'status'            => 'scheduled',
                'client_feedback'   => 'Client requested structural layout and rooftop infinity pool preview.',
            ]
        );

        $customer1 = CrmCustomer::firstOrCreate(
            ['phone' => '01819223344'],
            [
                'company_id'        => $company->id,
                'customer_code'     => 'CUS-2026-00001',
                'name'              => 'Dr. Sabrina Jahan',
                'email'             => 'sabrina.jahan@squarehospital.com',
                'nid_number'        => '19822694729100021',
                'tin_number'        => '827364819203',
                'emergency_contact' => '01712998877',
                'present_address'   => 'House 14, Road 4, Sector 3, Uttara, Dhaka',
                'status'            => 'active',
            ]
        );

        // 2. Land Bank & Feasibility
        $parcel = LandParcel::firstOrCreate(
            ['parcel_code' => 'LND-2026-00001'],
            [
                'company_id'              => $company->id,
                'branch_id'               => $branch->id,
                'cost_center_id'          => $costCenter?->id,
                'title'                   => 'Gulshan Lakeview Prime Corner',
                'mouza'                   => 'Bhola Mouza',
                'khatian_no'              => 'CS-1042 / RS-883 / BS-4209',
                'dag_no'                  => 'Dag 1402 & 1403',
                'total_area_katha'        => 12.50,
                'total_area_decimal'      => 20.66,
                'acquisition_type'        => 'joint_venture',
                'status'                  => 'jv_signed',
                'developer_ratio'         => 60.00,
                'landowner_ratio'         => 40.00,
                'signing_money_committed' => 15000000.00,
                'signing_money_paid'      => 15000000.00,
                'primary_contact_name'    => 'Haji Nurul Haque',
                'primary_contact_phone'   => '01711889900',
                'created_by'              => $admin?->id,
            ]
        );

        LandParcelOwner::firstOrCreate(
            ['land_parcel_id' => $parcel->id, 'phone' => '01711889900'],
            [
                'name'                => 'Haji Nurul Haque & Heirs',
                'nid_number'          => '1955269384910029',
                'share_percentage'    => 100.00,
                'signing_money_share' => 15000000.00,
                'is_mou_signed'       => true,
                'mou_deed_no'         => 'GPA-9823/2026',
                'notes'               => 'All heirs signed irrevocable General Power of Attorney (GPA).',
            ]
        );

        LandVetting::where('land_parcel_id', $parcel->id)->update([
            'status'     => 'cleared',
            'verified_at'=> now(),
            'findings'   => 'Verified authentic by District Sub-Registry and AC Land records.',
        ]);

        LandFeasibility::firstOrCreate(
            ['land_parcel_id' => $parcel->id],
            [
                'feasibility_code'            => 'FSB-2026-00001',
                'version'                     => 1,
                'proposed_building_type'      => 'residential',
                'num_of_basements'            => 2,
                'num_of_stories'              => 14,
                'permissible_far'             => 4.50,
                'gross_floor_area_sqft'       => 40500.00,
                'sellable_area_sqft'          => 35000.00,
                'developer_share_sqft'        => 21000.00,
                'landowner_share_sqft'        => 14000.00,
                'est_construction_cost_sqft'  => 4200.00,
                'est_total_construction_cost' => 147000000.00,
                'est_land_and_approval_cost'  => 15000000.00,
                'est_total_project_cost'      => 162000000.00,
                'projected_selling_rate_sqft' => 18500.00,
                'projected_gross_revenue'     => 388500000.00,
                'projected_net_profit'        => 226500000.00,
                'roi_percentage'              => 139.81,
                'payback_period_months'       => 36,
                'status'                      => 'approved',
                'approved_by_admin_id'        => $admin?->id,
                'created_by'                  => $admin?->id,
            ]
        );

        // 3. Developer Projects, Units, and Bookings
        $devProject = DevProject::firstOrCreate(
            ['project_code' => 'PRJ-2026-00001'],
            [
                'company_id'          => $company->id,
                'branch_id'           => $branch->id,
                'cost_center_id'      => $costCenter?->id,
                'land_parcel_id'      => $parcel->id,
                'name'                => 'Aurelia Crown Residence',
                'project_type'        => 'residential',
                'status'              => 'under_construction',
                'land_area_katha'     => 12.50,
                'total_floors'        => 14,
                'total_basements'     => 2,
                'total_units'         => 24,
                'total_parking_slots' => 30,
                'location'            => 'Gulshan 2, Dhaka',
                'sanction_no'         => 'RAJUK/BC-542/2025',
                'architect_name'      => 'Studio Vista Architects',
                'structural_engineer' => 'Buro Bangladesh Engineering',
            ]
        );

        $unit1 = DevProjectUnit::firstOrCreate(
            ['project_id' => $devProject->id, 'unit_number' => '5A'],
            [
                'floor_label'          => '5th Floor North-East Facing',
                'unit_type'            => 'apartment',
                'allocation_share'     => 'developer_share',
                'gross_area_sqft'      => 2450.00,
                'net_carpet_area_sqft' => 2100.00,
                'bedrooms'             => 4,
                'bathrooms'            => 4,
                'balconies'            => 3,
                'facing'               => 'North-East Lakeview',
                'base_price_sqft'      => 18500.00,
                'total_unit_price'     => 45325000.00,
                'utility_charges'      => 500000.00,
                'grand_total_price'    => 45825000.00,
                'status'               => 'booked',
            ]
        );

        $unit2 = DevProjectUnit::firstOrCreate(
            ['project_id' => $devProject->id, 'unit_number' => '6B'],
            [
                'floor_label'          => '6th Floor South Facing',
                'unit_type'            => 'apartment',
                'allocation_share'     => 'developer_share',
                'gross_area_sqft'      => 2250.00,
                'net_carpet_area_sqft' => 1920.00,
                'bedrooms'             => 3,
                'bathrooms'            => 3,
                'balconies'            => 2,
                'facing'               => 'South',
                'base_price_sqft'      => 18500.00,
                'total_unit_price'     => 41625000.00,
                'utility_charges'      => 500000.00,
                'grand_total_price'    => 42125000.00,
                'status'               => 'available',
            ]
        );

        $parking1 = DevParkingSlot::firstOrCreate(
            ['project_id' => $devProject->id, 'slot_number' => 'B1-05'],
            [
                'basement_level'   => 'Basement 1',
                'assigned_unit_id' => $unit1->id,
                'price'            => 750000.00,
                'status'           => 'allocated',
            ]
        );

        $booking = DevSalesBooking::firstOrCreate(
            ['booking_code' => 'BKG-2026-00001'],
            [
                'company_id'            => $company->id,
                'branch_id'             => $branch->id,
                'project_id'            => $devProject->id,
                'unit_id'               => $unit1->id,
                'customer_id'           => $customer1->id,
                'booking_date'          => '2026-10-15',
                'agreed_price_sqft'     => 18500.00,
                'agreed_unit_price'     => 45325000.00,
                'parking_slot_id'       => $parking1->id,
                'agreed_parking_price'  => 750000.00,
                'agreed_utility_charge' => 500000.00,
                'total_contract_amount' => 46575000.00,
                'downpayment_amount'    => 10000000.00,
                'payment_plan_type'     => 'milestone_linked',
                'total_installments'    => 5,
                'status'                => 'active',
                'sales_agent_id'        => $admin?->id,
            ]
        );

        DevBookingInstallment::firstOrCreate(
            ['booking_id' => $booking->id, 'milestone_name' => 'Booking Money & Down Payment'],
            [
                'installment_number' => 1,
                'due_date'           => '2026-10-15',
                'due_amount'         => 10000000.00,
                'paid_amount'        => 10000000.00,
                'balance_due'        => 0.00,
                'status'             => 'paid',
                'paid_date'          => '2026-10-15',
                'payment_reference'  => 'Chq # 482910 DBBL Gulshan',
            ]
        );

        DevBookingInstallment::firstOrCreate(
            ['booking_id' => $booking->id, 'milestone_name' => 'Casting of 5th Floor Slab'],
            [
                'installment_number' => 2,
                'due_date'           => '2027-02-28',
                'due_amount'         => 7500000.00,
                'paid_amount'        => 0.00,
                'balance_due'        => 7500000.00,
                'status'             => 'pending',
            ]
        );

        // 4. Brokerage / Agency Listings & Deals
        $listing1 = AgencyListing::firstOrCreate(
            ['listing_code' => 'LST-2026-00001'],
            [
                'company_id'             => $company->id,
                'branch_id'              => $branch->id,
                'mandate_type'           => 'open',
                'listing_type'           => 'resale',
                'title'                  => 'Ready Furnished 2800 Sft Luxury Flat in Banani Road 11',
                'property_category'      => 'apartment',
                'address'                => 'Road 11, Block D, Banani, Dhaka',
                'location'               => 'Banani, Dhaka',
                'size_sqft'              => 2800.00,
                'asking_price'           => 42000000.00,
                'commission_rate_percent'=> 2.00,
                'landlord_seller_name'   => 'Mr. Anisul Haque',
                'landlord_seller_phone'  => '01715002233',
                'status'                 => 'closed_deal',
                'assigned_agent_id'      => $admin?->id,
            ]
        );

        AgencyDeal::firstOrCreate(
            ['deal_code' => 'DEL-2026-00001'],
            [
                'company_id'               => $company->id,
                'branch_id'                => $branch->id,
                'listing_id'               => $listing1->id,
                'customer_id'              => $customer1->id,
                'deal_type'                => 'resale',
                'closing_price'            => 41000000.00,
                'company_commission_amount'=> 615000.00, // 75% company share
                'agent_commission_percent' => 25.00,
                'agent_commission_amount'  => 205000.00,
                'agent_id'                 => $admin?->id,
                'deal_date'                => '2026-10-20',
                'status'                   => 'settled',
            ]
        );

        $listing2 = AgencyListing::firstOrCreate(
            ['listing_code' => 'LST-2026-00002'],
            [
                'company_id'             => $company->id,
                'branch_id'              => $branch->id,
                'mandate_type'           => 'exclusive',
                'listing_type'           => 'rental',
                'title'                  => 'Commercial Office Floor 3500 Sft in Motijheel C/A',
                'property_category'      => 'commercial',
                'address'                => 'Al-Razi Complex, Shaheed Syed Nazrul Islam Sarani, Motijheel, Dhaka',
                'location'               => 'Motijheel, Dhaka',
                'size_sqft'              => 3500.00,
                'asking_price'           => 280000.00, // monthly rent
                'commission_rate_percent'=> 100.00, // 1 month rent
                'landlord_seller_name'   => 'Haji Mominullah Enterprise',
                'landlord_seller_phone'  => '01712003344',
                'status'                 => 'active',
                'assigned_agent_id'      => $admin?->id,
            ]
        );

        AgencyTenantLease::firstOrCreate(
            ['lease_code' => 'LSE-2026-00001'],
            [
                'company_id'             => $company->id,
                'branch_id'              => $branch->id,
                'listing_id'             => $listing2->id,
                'tenant_id'              => $customer1->id,
                'monthly_rent'           => 280000.00,
                'security_deposit_amount'=> 1680000.00,
                'monthly_service_charge' => 25000.00,
                'lease_start_date'       => '2026-11-01',
                'lease_end_date'         => '2029-10-31',
                'rent_due_day'           => 5,
                'status'                 => 'active',
            ]
        );

        // 5. Construction & Procurement
        $vendor1 = ConVendor::firstOrCreate(
            ['vendor_code' => 'VND-2026-00001'],
            [
                'company_id'       => $company->id,
                'name'             => 'BSRM Steels Ltd',
                'trade_license_no' => 'TRAD/DSCC/019283/2024',
                'contact_person'   => 'Engr. Mofizul Alam',
                'phone'            => '01713009988',
                'email'            => 'sales.dhaka@bsrm.com',
                'address'          => 'Ali Mansion, 120 Sadarghat, Chittagong / Dhaka Office',
                'tin_no'           => '192837465019',
                'bank_details'     => 'Standard Chartered Bank Gulshan Br, A/C: 01-1293847-01',
                'status'           => 'active',
            ]
        );

        $vendor2 = ConVendor::firstOrCreate(
            ['vendor_code' => 'VND-2026-00002'],
            [
                'company_id'       => $company->id,
                'name'             => 'Crown Cement PLC',
                'trade_license_no' => 'TRAD/DNCC/082716/2024',
                'contact_person'   => 'Khandaker Zahid',
                'phone'            => '01819334455',
                'email'            => 'orders@crowncement.com',
                'status'           => 'active',
            ]
        );

        ConBoqItem::firstOrCreate(
            ['project_id' => $devProject->id, 'item_name' => '500W Deformed Bar Reinforcement (BSRM 72.5 Grade)'],
            [
                'company_id'          => $company->id,
                'category'            => 'substructure',
                'unit_of_measure'     => 'ton',
                'estimated_quantity'  => 350.00,
                'estimated_unit_rate' => 105000.00,
                'total_estimated_cost'=> 36750000.00,
                'actual_consumed_qty' => 120.00,
                'actual_total_cost'   => 12600000.00,
            ]
        );

        $req = ConMaterialRequisition::firstOrCreate(
            ['requisition_code' => 'REQ-2026-00001'],
            [
                'company_id'          => $company->id,
                'branch_id'           => $branch->id,
                'project_id'          => $devProject->id,
                'requested_by'        => $admin->id,
                'required_date'       => '2026-10-30',
                'urgency'             => 'urgent',
                'status'              => 'approved',
                'approved_by'         => $admin->id,
                'approved_at'         => '2026-10-23 10:00:00',
            ]
        );

        ConRequisitionItem::firstOrCreate(
            ['requisition_id' => $req->id, 'material_name' => '16mm TMT BSRM Rebar'],
            [
                'unit_of_measure' => 'ton',
                'requested_qty'   => 25.00,
                'approved_qty'    => 25.00,
                'remarks'         => 'Needed for basement casting schedule',
            ]
        );

        $po = ConPurchaseOrder::firstOrCreate(
            ['po_code' => 'PO-2026-00001'],
            [
                'company_id'       => $company->id,
                'branch_id'        => $branch->id,
                'project_id'       => $devProject->id,
                'vendor_id'        => $vendor1->id,
                'requisition_id'   => $req->id,
                'po_date'          => '2026-10-23',
                'delivery_due_date'=> '2026-10-31',
                'subtotal'         => 2600000.00,
                'tax_vat_amount'   => 130000.00,
                'grand_total'      => 2730000.00,
                'payment_terms'    => '30% advance on PO confirmation, 70% within 15 days of GRN verification.',
                'status'           => 'issued',
                'created_by'       => $admin->id,
            ]
        );

        ConPoItem::firstOrCreate(
            ['purchase_order_id' => $po->id, 'item_name' => '16mm TMT BSRM Rebar (500W Grade)'],
            [
                'unit_of_measure'  => 'ton',
                'quantity'         => 25.00,
                'unit_rate'        => 104000.00,
                'line_total'       => 2600000.00,
                'received_qty'     => 25.00,
            ]
        );

        ConGrn::firstOrCreate(
            ['grn_code' => 'GRN-2026-00001'],
            [
                'company_id'        => $company->id,
                'branch_id'         => $branch->id,
                'project_id'        => $devProject->id,
                'purchase_order_id' => $po->id,
                'vendor_id'         => $vendor1->id,
                'received_date'     => '2026-10-28',
                'challan_no'        => 'CH-BSRM-98371',
                'vehicle_number'    => 'DHAKA-METRO-TA-11-2233',
                'received_by'       => $admin->id,
                'status'            => 'verified',
                'remarks'           => 'Tested on-site tensile elongation passed; weight verified at site weighbridge.',
            ]
        );

        ConSiteStock::updateOrCreate(
            ['project_id' => $devProject->id, 'material_name' => '16mm TMT BSRM Rebar'],
            [
                'company_id'        => $company->id,
                'unit_of_measure'   => 'ton',
                'current_stock_qty' => 18.50, // 25 received - 6.5 consumed
                'total_received_qty'=> 25.00,
                'total_consumed_qty'=> 6.50,
                'last_received_date'=> '2026-10-28',
            ]
        );

        // 6. Double-Entry Accounting (Journals)
        $accBank = AccChartOfAccount::where('account_code', '1010')->first(); // Bank Accounts
        $accAdvanceCust = AccChartOfAccount::where('account_code', '2010')->first(); // Customer Advances

        if ($accBank && $accAdvanceCust) {
            $jv1 = AccJournal::firstOrCreate(
                ['voucher_code' => 'JV-2026-00001'],
                [
                    'company_id'    => $company->id,
                    'branch_id'     => $branch->id,
                    'fiscal_year_id'=> $fy?->id,
                    'voucher_type'  => 'receipt',
                    'entry_date'    => '2026-10-15',
                    'total_amount'  => 5000000.00,
                    'narration'     => 'Received booking advance from Dr. Sabrina Jahan for Unit 5A (Aurelia Crown Residence)',
                    'status'        => 'posted',
                    'created_by'    => $admin?->id,
                ]
            );

            AccJournalEntry::firstOrCreate(
                ['journal_id' => $jv1->id, 'account_id' => $accBank->id],
                [
                    'cost_center_id' => $costCenter?->id,
                    'debit'          => 5000000.00,
                    'credit'         => 0.00,
                    'entry_narration'=> 'Bank Deposit DBBL Gulshan Br',
                ]
            );

            AccJournalEntry::firstOrCreate(
                ['journal_id' => $jv1->id, 'account_id' => $accAdvanceCust->id],
                [
                    'cost_center_id' => $costCenter?->id,
                    'debit'          => 0.00,
                    'credit'         => 5000000.00,
                    'entry_narration'=> 'Customer Advance Sabina Jahan (Unit 5A)',
                ]
            );
        }

        // 7. HR Employees, Monthly Payroll, and Construction Daily Laborers
        $emp1 = HrEmployee::firstOrCreate(
            ['employee_code' => 'EMP-2026-00001'],
            [
                'company_id'           => $company->id,
                'branch_id'            => $branch->id,
                'department_id'        => $deptEng?->id,
                'designation_id'       => $desManager?->id,
                'name'                 => 'Engr. Tareq Mahmud P.Eng.',
                'phone'                => '01711223399',
                'email'                => 'tareq.mahmud@aurelia.com.bd',
                'nid_no'               => '1981269384910001',
                'joining_date'         => '2024-01-01',
                'employment_type'      => 'permanent',
                'basic_salary'         => 80000.00,
                'house_rent_allowance' => 35000.00,
                'medical_allowance'    => 10000.00,
                'conveyance_allowance' => 10000.00,
                'gross_salary'         => 135000.00,
                'bank_name'            => 'Dutch-Bangla Bank PLC',
                'bank_account_no'      => '115.120.984716',
                'status'               => 'active',
            ]
        );

        $emp2 = HrEmployee::firstOrCreate(
            ['employee_code' => 'EMP-2026-00002'],
            [
                'company_id'           => $company->id,
                'branch_id'            => $branch->id,
                'department_id'        => $deptFin?->id,
                'designation_id'       => $desAcc?->id,
                'name'                 => 'Sumaiya Akter ACMA',
                'phone'                => '01819334466',
                'email'                => 'sumaiya.fin@aurelia.com.bd',
                'nid_no'               => '1992269482910002',
                'joining_date'         => '2024-06-15',
                'employment_type'      => 'permanent',
                'basic_salary'         => 55000.00,
                'house_rent_allowance' => 25000.00,
                'medical_allowance'    => 5000.00,
                'conveyance_allowance' => 5000.00,
                'gross_salary'         => 90000.00,
                'bank_name'            => 'The City Bank PLC',
                'bank_account_no'      => '2101938472001',
                'status'               => 'active',
            ]
        );

        HrPayroll::firstOrCreate(
            ['employee_id' => $emp1->id, 'salary_month' => 10, 'salary_year' => 2026],
            [
                'company_id'     => $company->id,
                'branch_id'      => $branch->id,
                'payroll_code'   => 'PRL-2026-00001',
                'gross_salary'   => 135000.00,
                'bonus'          => 15000.00,
                'deductions'     => 5000.00,
                'net_payable'    => 145000.00,
                'payment_status' => 'paid',
                'payment_date'   => '2026-10-31',
                'payment_method' => 'bank_transfer',
                'processed_by'   => $admin?->id,
            ]
        );

        $lab1 = HrDailyLaborer::firstOrCreate(
            ['laborer_code' => 'LAB-2026-00001'],
            [
                'company_id'          => $company->id,
                'assigned_project_id' => $devProject->id,
                'name'                => 'Md. Alamgir Hossain (হেড রাজমিস্ত্রি)',
                'phone'               => '01622334455',
                'nid_no'              => '198726384910',
                'trade'               => 'mason',
                'daily_wage_rate'     => 950.00,
                'overtime_hourly_rate'=> 180.00,
                'status'              => 'active',
            ]
        );

        $lab2 = HrDailyLaborer::firstOrCreate(
            ['laborer_code' => 'LAB-2026-00002'],
            [
                'company_id'          => $company->id,
                'assigned_project_id' => $devProject->id,
                'name'                => 'Abdul Mannan (রড বাইন্ডার ফোরম্যান)',
                'phone'               => '01733445566',
                'nid_no'              => '199028374619',
                'trade'               => 'rod_binder',
                'daily_wage_rate'     => 900.00,
                'overtime_hourly_rate'=> 170.00,
                'status'              => 'active',
            ]
        );

        $lab3 = HrDailyLaborer::firstOrCreate(
            ['laborer_code' => 'LAB-2026-00003'],
            [
                'company_id'          => $company->id,
                'assigned_project_id' => $devProject->id,
                'name'                => 'Md. Rubel Mia (জেনারেল হেল্পার)',
                'phone'               => '01855667788',
                'nid_no'              => '199527364810',
                'trade'               => 'helper',
                'daily_wage_rate'     => 700.00,
                'overtime_hourly_rate'=> 130.00,
                'status'              => 'active',
            ]
        );

        HrLaborMusterRoll::firstOrCreate(
            ['laborer_id' => $lab1->id, 'work_date' => '2026-11-02'],
            [
                'company_id'     => $company->id,
                'project_id'     => $devProject->id,
                'is_present'     => true,
                'regular_hours'  => 8.00,
                'overtime_hours' => 2.50,
                'regular_wage'   => 950.00,
                'overtime_wage'  => 450.00,
                'total_wage'     => 1400.00,
                'payment_status' => 'unpaid',
                'recorded_by'    => $admin?->id,
            ]
        );

        HrLaborMusterRoll::firstOrCreate(
            ['laborer_id' => $lab2->id, 'work_date' => '2026-11-02'],
            [
                'company_id'     => $company->id,
                'project_id'     => $devProject->id,
                'is_present'     => true,
                'regular_hours'  => 8.00,
                'overtime_hours' => 2.00,
                'regular_wage'   => 900.00,
                'overtime_wage'  => 340.00,
                'total_wage'     => 1240.00,
                'payment_status' => 'unpaid',
                'recorded_by'    => $admin?->id,
            ]
        );
    }
}
