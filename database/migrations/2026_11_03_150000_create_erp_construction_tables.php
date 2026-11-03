<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Vendors & Suppliers
        Schema::create('con_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('vendor_code', 50)->unique();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->string('trade_license_no', 100)->nullable();
            $table->string('tin_no', 100)->nullable();
            $table->text('bank_details')->nullable();
            $table->string('status', 50)->default('active'); // active, inactive, blacklisted
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
        });

        // 2. Bill of Quantities (BOQ) Items per Project
        Schema::create('con_boq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('dev_projects')->cascadeOnDelete();
            $table->string('category', 50); // substructure, superstructure, finishing, mep, external
            $table->string('item_name');
            $table->string('unit_of_measure', 30); // ton, cft, sft, bag, nos, rft
            $table->decimal('estimated_quantity', 12, 2);
            $table->decimal('estimated_unit_rate', 14, 2);
            $table->decimal('total_estimated_cost', 18, 2);
            $table->decimal('actual_consumed_qty', 12, 2)->default(0);
            $table->decimal('actual_total_cost', 18, 2)->default(0);
            $table->text('specifications')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'category']);
        });

        // 3. Site Material Requisitions
        Schema::create('con_material_requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('project_id')->constrained('dev_projects')->cascadeOnDelete();
            $table->string('requisition_code', 50)->unique();
            $table->foreignId('requested_by')->constrained('admins')->cascadeOnDelete();
            $table->date('required_date');
            $table->string('urgency', 30)->default('normal'); // normal, urgent, emergency
            $table->string('status', 50)->default('submitted'); // submitted, approved, po_issued, rejected
            $table->text('site_location_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
        });

        Schema::create('con_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('con_material_requisitions')->cascadeOnDelete();
            $table->string('material_name');
            $table->string('unit_of_measure', 30);
            $table->decimal('requested_qty', 12, 2);
            $table->decimal('approved_qty', 12, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 4. Purchase Orders (PO)
        Schema::create('con_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('project_id')->constrained('dev_projects')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('con_vendors')->cascadeOnDelete();
            $table->foreignId('requisition_id')->nullable()->constrained('con_material_requisitions')->nullOnDelete();
            $table->string('po_code', 50)->unique();
            $table->date('po_date');
            $table->date('delivery_due_date')->nullable();
            $table->decimal('subtotal', 18, 2);
            $table->decimal('tax_vat_amount', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2);
            $table->string('payment_terms')->nullable();
            $table->string('status', 50)->default('issued'); // issued, partial_received, completed, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['project_id', 'status']);
        });

        Schema::create('con_po_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('con_purchase_orders')->cascadeOnDelete();
            $table->string('item_name');
            $table->string('unit_of_measure', 30);
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_rate', 14, 2);
            $table->decimal('line_total', 18, 2);
            $table->decimal('received_qty', 12, 2)->default(0);
            $table->timestamps();
        });

        // 5. Goods Receipt Notes (GRN) & Site Stock
        Schema::create('con_grns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('project_id')->constrained('dev_projects')->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->constrained('con_purchase_orders')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('con_vendors')->cascadeOnDelete();
            $table->string('grn_code', 50)->unique();
            $table->date('received_date');
            $table->string('challan_no', 100);
            $table->string('vehicle_number', 50)->nullable();
            $table->foreignId('received_by')->constrained('admins')->cascadeOnDelete();
            $table->string('status', 50)->default('verified'); // received, verified, rejected
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
        });

        Schema::create('con_site_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('dev_projects')->cascadeOnDelete();
            $table->string('material_name');
            $table->string('unit_of_measure', 30);
            $table->decimal('current_stock_qty', 14, 2)->default(0);
            $table->decimal('total_received_qty', 14, 2)->default(0);
            $table->decimal('total_consumed_qty', 14, 2)->default(0);
            $table->date('last_received_date')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'material_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('con_site_stocks');
        Schema::dropIfExists('con_grns');
        Schema::dropIfExists('con_po_items');
        Schema::dropIfExists('con_purchase_orders');
        Schema::dropIfExists('con_requisition_items');
        Schema::dropIfExists('con_material_requisitions');
        Schema::dropIfExists('con_boq_items');
        Schema::dropIfExists('con_vendors');
    }
};
