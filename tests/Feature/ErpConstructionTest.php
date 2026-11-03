<?php

use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\ConBoqItem;
use App\Models\ERP\ConGrn;
use App\Models\ERP\ConMaterialRequisition;
use App\Models\ERP\ConPurchaseOrder;
use App\Models\ERP\ConSiteStock;
use App\Models\ERP\ConVendor;
use App\Models\ERP\DevProject;
use Database\Seeders\ErpFoundationSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(ErpFoundationSeeder::class);

    $this->admin = Admin::firstOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name'     => 'Super Admin',
            'password' => Hash::make('password'),
        ]
    );

    if (!$this->admin->hasRole('Super Admin')) {
        $this->admin->assignRole('Super Admin');
    }

    $this->company = Company::where('code', 'AURELIA')->first();
    $this->branch = Branch::where('code', 'DHK-HQ')->first();

    $this->project = DevProject::create([
        'company_id'              => $this->company->id,
        'branch_id'               => $this->branch->id,
        'project_code'            => 'PRJ-CON-01',
        'name'                    => 'Aurelia Skyview Tower',
        'project_type'            => 'residential',
        'location'                => 'Gulshan 2, Dhaka',
        'total_floors'            => 15,
        'total_units'             => 30,
        'total_construction_sqft' => 65000,
        'status'                  => 'under_construction',
    ]);
});

test('admin can view construction, boq, requisitions, pos, grns, and stocks indexes', function () {
    $r1 = $this->actingAs($this->admin, 'admin')->get('/erp/construction/vendors');
    $r1->assertStatus(200);
    $r1->assertSee('Material Vendors');

    $r2 = $this->actingAs($this->admin, 'admin')->get('/erp/construction/boq');
    $r2->assertStatus(200);
    $r2->assertSee('Bill of Quantities');

    $r3 = $this->actingAs($this->admin, 'admin')->get('/erp/construction/requisitions');
    $r3->assertStatus(200);
    $r3->assertSee('Site Material Requisitions');

    $r4 = $this->actingAs($this->admin, 'admin')->get('/erp/construction/pos');
    $r4->assertStatus(200);
    $r4->assertSee('Purchase Orders');

    $r5 = $this->actingAs($this->admin, 'admin')->get('/erp/construction/grns');
    $r5->assertStatus(200);
    $r5->assertSee('Goods Receipt Notes');

    $r6 = $this->actingAs($this->admin, 'admin')->get('/erp/construction/stocks');
    $r6->assertStatus(200);
    $r6->assertSee('Site Material Inventory');
});

test('admin can register a construction vendor with auto code', function () {
    $response = $this->actingAs($this->admin, 'admin')->post('/erp/construction/vendors', [
        'name'             => 'BSRM Steels Bangladesh',
        'contact_person'   => 'Engr. Enamul Kabir',
        'phone'            => '+880 1711 556677',
        'email'            => 'sales@bsrm.example.com',
        'trade_license_no' => 'TRAD/CTG/889911',
        'tin_no'           => '881239048123',
        'bank_details'     => 'Standard Chartered Bank A/C 01-998811-01',
        'status'           => 'active',
        'address'          => 'Nasirabad Industrial Area, Chattogram',
    ]);

    $response->assertSessionHasNoErrors();

    $vendor = ConVendor::where('name', 'BSRM Steels Bangladesh')->first();
    expect($vendor)->not->toBeNull()
        ->and($vendor->vendor_code)->toStartWith('VEN-')
        ->and($vendor->contact_person)->toBe('Engr. Enamul Kabir');
});

test('admin can add a BOQ item to a project and update actual consumption', function () {
    $response = $this->actingAs($this->admin, 'admin')->post('/erp/construction/boq', [
        'project_id'          => $this->project->id,
        'category'            => 'superstructure',
        'item_name'           => '500W TMT Deformed Rebar (16mm & 20mm)',
        'unit_of_measure'     => 'ton',
        'estimated_quantity'  => 180.00,
        'estimated_unit_rate' => 97000.00,
        'specifications'      => 'BSRM Ultima 500D Grade',
    ]);

    $response->assertSessionHasNoErrors();

    $item = ConBoqItem::where('project_id', $this->project->id)->first();
    expect($item)->not->toBeNull()
        ->and((float) $item->total_estimated_cost)->toEqual(17460000.0) // 180 * 97,000
        ->and($item->category)->toBe('superstructure');

    // Update consumption
    $consumeResponse = $this->actingAs($this->admin, 'admin')->put("/erp/construction/boq/{$item->id}/consumption", [
        'actual_consumed_qty' => 45.00,
        'actual_total_cost'   => 4365000.00,
    ]);
    $consumeResponse->assertSessionHasNoErrors();

    expect((float) $item->fresh()->actual_consumed_qty)->toEqual(45.0)
        ->and((float) $item->fresh()->actual_total_cost)->toEqual(4365000.0);
});

test('admin can submit and approve site material requisition', function () {
    $response = $this->actingAs($this->admin, 'admin')->post('/erp/construction/requisitions', [
        'project_id'          => $this->project->id,
        'required_date'       => '2026-11-10',
        'urgency'             => 'urgent',
        'site_location_notes' => 'Basement 1 Casting Deck',
        'items'               => [
            [
                'material_name'   => 'Shah Special Cement 42.5N',
                'unit_of_measure' => 'bag',
                'requested_qty'   => 600,
                'remarks'         => 'Needed for slab casting',
            ],
            [
                'material_name'   => 'Sylhet Coarse Sand (FM 2.5)',
                'unit_of_measure' => 'cft',
                'requested_qty'   => 1200,
                'remarks'         => 'Washed coarse sand',
            ],
        ],
    ]);

    $response->assertSessionHasNoErrors();

    $req = ConMaterialRequisition::where('project_id', $this->project->id)->first();
    expect($req)->not->toBeNull()
        ->and($req->requisition_code)->toStartWith('REQ-')
        ->and($req->items()->count())->toBe(2)
        ->and($req->status)->toBe('submitted');

    // Approve requisition
    $approveResponse = $this->actingAs($this->admin, 'admin')->post("/erp/construction/requisitions/{$req->id}/approve");
    $approveResponse->assertSessionHasNoErrors();

    expect($req->fresh()->status)->toBe('approved')
        ->and($req->fresh()->approved_by)->toBe($this->admin->id);
});

test('admin can issue purchase order for vendor and project with line items', function () {
    $vendor = ConVendor::create([
        'company_id'  => $this->company->id,
        'vendor_code' => 'VEN-TEST-01',
        'name'        => 'Shah Cement Industries',
        'phone'       => '+880 1711 112233',
        'status'      => 'active',
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post('/erp/construction/pos', [
        'project_id'        => $this->project->id,
        'vendor_id'         => $vendor->id,
        'po_date'           => '2026-11-03',
        'delivery_due_date' => '2026-11-12',
        'tax_vat_amount'    => 15000,
        'payment_terms'     => 'Net 30 Days',
        'notes'             => 'Delivery in 2 batches of 300 bags',
        'items'             => [
            [
                'item_name'       => 'Shah Special Cement (50kg)',
                'unit_of_measure' => 'bag',
                'quantity'        => 600,
                'unit_rate'       => 560,
            ],
        ],
    ]);

    $response->assertSessionHasNoErrors();

    // Line total: 600 * 560 = 336,000 + 15,000 VAT = 351,000
    $po = ConPurchaseOrder::where('vendor_id', $vendor->id)->first();
    expect($po)->not->toBeNull()
        ->and($po->po_code)->toStartWith('PO-')
        ->and((float) $po->subtotal)->toEqual(336000.0)
        ->and((float) $po->grand_total)->toEqual(351000.0)
        ->and($po->items()->count())->toBe(1)
        ->and($po->status)->toBe('issued');
});

test('admin can process GRN against PO which updates received qty and increments site stock', function () {
    $vendor = ConVendor::create([
        'company_id'  => $this->company->id,
        'vendor_code' => 'VEN-TEST-02',
        'name'        => 'Meghna Sand & Aggregates',
        'phone'       => '+880 1819 889900',
        'status'      => 'active',
    ]);

    $po = ConPurchaseOrder::create([
        'company_id'     => $this->company->id,
        'branch_id'      => $this->branch->id,
        'project_id'     => $this->project->id,
        'vendor_id'      => $vendor->id,
        'po_code'        => 'PO-TEST-02',
        'po_date'        => '2026-11-03',
        'subtotal'       => 150000,
        'tax_vat_amount' => 0,
        'grand_total'    => 150000,
        'status'         => 'issued',
    ]);

    $poItem = $po->items()->create([
        'item_name'       => 'Sylhet Coarse Sand (FM 2.5)',
        'unit_of_measure' => 'cft',
        'quantity'        => 2000,
        'unit_rate'       => 75,
        'line_total'      => 150000,
        'received_qty'    => 0,
    ]);

    $response = $this->actingAs($this->admin, 'admin')->post('/erp/construction/grns', [
        'purchase_order_id' => $po->id,
        'received_date'     => '2026-11-03',
        'challan_no'        => 'CH-MEGHNA-99881',
        'vehicle_number'    => 'Dhaka Metro-TA 14-8899',
        'remarks'           => 'First truck of 1000 cft verified and dumped',
        'items'             => [
            [
                'po_item_id'   => $poItem->id,
                'received_qty' => 1000,
            ],
        ],
    ]);

    $response->assertSessionHasNoErrors();

    // Check GRN
    $grn = ConGrn::where('purchase_order_id', $po->id)->first();
    expect($grn)->not->toBeNull()
        ->and($grn->grn_code)->toStartWith('GRN-')
        ->and($grn->challan_no)->toBe('CH-MEGHNA-99881');

    // PO item received_qty should be 1000, status should be partial_received
    expect((float) $poItem->fresh()->received_qty)->toEqual(1000.0);
    expect($po->fresh()->status)->toBe('partial_received');

    // Site stock inventory must be incremented
    $stock = ConSiteStock::where('project_id', $this->project->id)
        ->where('material_name', 'Sylhet Coarse Sand (FM 2.5)')
        ->first();

    expect($stock)->not->toBeNull()
        ->and((float) $stock->current_stock_qty)->toEqual(1000.0)
        ->and((float) $stock->total_received_qty)->toEqual(1000.0);
});

