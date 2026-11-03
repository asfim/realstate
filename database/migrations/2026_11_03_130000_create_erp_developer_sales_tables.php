<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dev_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('land_parcel_id')->nullable()->constrained('land_parcels')->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->string('project_code', 50)->unique();
            $table->string('name');
            $table->string('project_type', 50)->default('residential'); // residential, commercial, mixed_use, land_plotted, warehouse
            $table->text('address')->nullable();
            $table->string('location')->nullable();
            $table->decimal('land_area_katha', 12, 4)->nullable();
            $table->unsignedInteger('total_floors')->default(1);
            $table->unsignedInteger('total_basements')->default(0);
            $table->unsignedInteger('total_units')->default(0);
            $table->unsignedInteger('total_parking_slots')->default(0);
            $table->string('sanction_no', 100)->nullable();
            $table->string('architect_name')->nullable();
            $table->string('structural_engineer')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expected_completion_date')->nullable();
            $table->string('status', 50)->default('planning'); // planning, sanctioned, under_construction, finishing, ready, handed_over
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
        });

        Schema::create('dev_project_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('dev_projects')->cascadeOnDelete();
            $table->string('unit_number', 50); // e.g. Flat 4A, Unit 7B
            $table->string('floor_label', 50)->default('Typical Floor'); // Ground Floor, 4th Floor
            $table->string('unit_type', 50)->default('apartment'); // apartment, commercial_space, shop, duplex, penthouse, plot
            $table->string('allocation_share', 50)->default('developer_share'); // developer_share, landowner_share, reserved
            $table->decimal('gross_area_sqft', 10, 2);
            $table->decimal('net_carpet_area_sqft', 10, 2)->nullable();
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('balconies')->nullable();
            $table->string('facing', 50)->nullable(); // North, South, Lakeview, Corner
            $table->decimal('base_price_sqft', 12, 2)->default(0);
            $table->decimal('total_unit_price', 18, 2)->default(0);
            $table->decimal('utility_charges', 18, 2)->default(0);
            $table->decimal('grand_total_price', 18, 2)->default(0);
            $table->string('status', 50)->default('available'); // available, reserved, booked, sold, handed_over
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['project_id', 'unit_number']);
            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'allocation_share']);
        });

        Schema::create('dev_parking_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('dev_projects')->cascadeOnDelete();
            $table->string('slot_number', 50); // B1-P01
            $table->string('basement_level', 50)->default('Basement 1');
            $table->foreignId('assigned_unit_id')->nullable()->constrained('dev_project_units')->nullOnDelete();
            $table->decimal('price', 18, 2)->default(0);
            $table->string('status', 50)->default('available'); // available, allocated, sold
            $table->timestamps();

            $table->unique(['project_id', 'slot_number']);
        });

        Schema::create('dev_sales_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('booking_code', 50)->unique();
            $table->foreignId('customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->foreignId('project_id')->constrained('dev_projects')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('dev_project_units')->cascadeOnDelete();
            $table->foreignId('parking_slot_id')->nullable()->constrained('dev_parking_slots')->nullOnDelete();
            $table->foreignId('sales_agent_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->decimal('agreed_price_sqft', 12, 2);
            $table->decimal('agreed_unit_price', 18, 2);
            $table->decimal('agreed_parking_price', 18, 2)->default(0);
            $table->decimal('agreed_utility_charge', 18, 2)->default(0);
            $table->decimal('total_contract_amount', 18, 2);
            $table->decimal('downpayment_amount', 18, 2)->default(0);
            $table->date('downpayment_due_date')->nullable();
            $table->string('payment_plan_type', 50)->default('installment_emi'); // installment_emi, milestone_linked, lumpsum, custom
            $table->unsignedInteger('total_installments')->default(1);
            $table->date('booking_date');
            $table->date('agreement_date')->nullable();
            $table->date('expected_handover_date')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('commission_amount', 18, 2)->default(0);
            $table->string('status', 50)->default('active'); // draft, active, registered_deed, cancelled, completed
            $table->text('cancellation_reason')->nullable();
            $table->text('special_conditions')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['project_id', 'status']);
        });

        Schema::create('dev_booking_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('dev_sales_bookings')->cascadeOnDelete();
            $table->string('milestone_name');
            $table->unsignedInteger('installment_number')->default(1);
            $table->date('due_date');
            $table->decimal('due_amount', 18, 2);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->decimal('balance_due', 18, 2);
            $table->string('status', 30)->default('pending'); // pending, partially_paid, paid, overdue
            $table->date('paid_date')->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dev_booking_installments');
        Schema::dropIfExists('dev_sales_bookings');
        Schema::dropIfExists('dev_parking_slots');
        Schema::dropIfExists('dev_project_units');
        Schema::dropIfExists('dev_projects');
    }
};
