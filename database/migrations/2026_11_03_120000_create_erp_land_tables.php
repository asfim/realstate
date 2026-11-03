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
        Schema::create('land_parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->string('parcel_code', 50)->unique();
            $table->string('title');
            $table->string('mouza')->nullable();
            $table->string('khatian_no', 100)->nullable();
            $table->string('dag_no', 100)->nullable();
            $table->decimal('total_area_katha', 12, 4);
            $table->decimal('total_area_decimal', 12, 4)->nullable();
            $table->string('acquisition_type', 50)->default('joint_venture'); // joint_venture, outright_purchase, leasehold, power_of_attorney
            $table->string('status', 50)->default('scouting'); // scouting, legal_vetting, feasibility_study, mou_negotiation, registered, handed_over, dropped
            $table->decimal('landowner_ratio', 5, 2)->nullable()->default(50.00);
            $table->decimal('developer_ratio', 5, 2)->nullable()->default(50.00);
            $table->decimal('estimated_land_value', 18, 2)->nullable();
            $table->decimal('signing_money_committed', 18, 2)->nullable();
            $table->decimal('signing_money_paid', 18, 2)->default(0);
            $table->string('primary_contact_name')->nullable();
            $table->string('primary_contact_phone', 50)->nullable();
            $table->date('target_handover_date')->nullable();
            $table->string('boundary_north')->nullable();
            $table->string('boundary_south')->nullable();
            $table->string('boundary_east')->nullable();
            $table->string('boundary_west')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'acquisition_type']);
        });

        Schema::create('land_parcel_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_parcel_id')->constrained('land_parcels')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('share_percentage', 5, 2)->default(100.00);
            $table->string('phone', 50)->nullable();
            $table->string('nid_number', 50)->nullable();
            $table->decimal('signing_money_share', 18, 2)->nullable();
            $table->boolean('is_mou_signed')->default(false);
            $table->string('mou_deed_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('land_vettings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_parcel_id')->constrained('land_parcels')->cascadeOnDelete();
            $table->string('stage_title'); // Title Deed, Mutation & Khajna, Porcha Chain, Bia Deed 25-yr, NEC, Zoning, Soil Test, Environmental
            $table->string('status', 40)->default('pending'); // pending, in_progress, verified_clear, disputed_red_flag, waived
            $table->foreignId('verified_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('lawyer_or_expert_name')->nullable();
            $table->text('findings')->nullable();
            $table->string('attachment_path')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();

            $table->index(['land_parcel_id', 'status']);
        });

        Schema::create('land_feasibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_parcel_id')->constrained('land_parcels')->cascadeOnDelete();
            $table->string('feasibility_code', 50)->unique();
            $table->unsignedInteger('version')->default(1);
            $table->string('proposed_building_type', 50)->default('Residential'); // Residential, Commercial, Mixed_Use, Gated_Community
            $table->unsignedInteger('num_of_basements')->default(1);
            $table->unsignedInteger('num_of_stories')->default(10);
            $table->decimal('permissible_far', 6, 2)->default(3.50);
            $table->decimal('gross_floor_area_sqft', 14, 2);
            $table->decimal('sellable_area_sqft', 14, 2);
            $table->decimal('landowner_share_sqft', 14, 2);
            $table->decimal('developer_share_sqft', 14, 2);
            $table->decimal('est_construction_cost_sqft', 12, 2)->default(3500);
            $table->decimal('est_total_construction_cost', 18, 2);
            $table->decimal('est_land_and_approval_cost', 18, 2)->default(0);
            $table->decimal('est_total_project_cost', 18, 2);
            $table->decimal('projected_selling_rate_sqft', 12, 2)->default(7500);
            $table->decimal('projected_gross_revenue', 18, 2);
            $table->decimal('projected_net_profit', 18, 2);
            $table->decimal('roi_percentage', 8, 2);
            $table->unsignedInteger('payback_period_months')->nullable();
            $table->string('status', 30)->default('draft'); // draft, submitted, approved, rejected
            $table->foreignId('approved_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->text('approval_remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index(['land_parcel_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('land_feasibilities');
        Schema::dropIfExists('land_vettings');
        Schema::dropIfExists('land_parcel_owners');
        Schema::dropIfExists('land_parcels');
    }
};
