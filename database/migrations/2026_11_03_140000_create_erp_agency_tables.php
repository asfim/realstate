<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agency_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('listing_code', 50)->unique();
            $table->string('title');
            $table->string('listing_type', 50); // resale, rental, commercial_lease, land_sale
            $table->string('property_category', 50); // apartment, land, commercial, warehouse, house
            $table->string('location');
            $table->text('address')->nullable();
            $table->decimal('size_sqft', 10, 2)->nullable();
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('floor_number')->nullable();
            $table->string('landlord_seller_name');
            $table->string('landlord_seller_phone', 50);
            $table->string('landlord_seller_email')->nullable();
            $table->string('mandate_type', 50)->default('exclusive'); // exclusive, semi_exclusive, open
            $table->date('mandate_expiry_date')->nullable();
            $table->decimal('asking_price', 18, 2);
            $table->decimal('reserve_price', 18, 2)->nullable();
            $table->decimal('commission_rate_percent', 5, 2)->default(2.00);
            $table->decimal('expected_commission', 18, 2)->default(0);
            $table->boolean('key_held_at_branch')->default(false);
            $table->string('status', 50)->default('active'); // scouting, active, negotiating, closed_deal, expired, withdrawn
            $table->foreignId('assigned_agent_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'listing_type']);
        });

        Schema::create('agency_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('deal_code', 50)->unique();
            $table->foreignId('listing_id')->constrained('agency_listings')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('deal_type', 50); // resale, rental
            $table->decimal('closing_price', 18, 2);
            $table->decimal('company_commission_amount', 18, 2)->default(0);
            $table->decimal('agent_commission_percent', 5, 2)->default(0);
            $table->decimal('agent_commission_amount', 18, 2)->default(0);
            $table->string('external_broker_name')->nullable();
            $table->string('external_broker_phone', 50)->nullable();
            $table->decimal('external_broker_commission_amount', 18, 2)->default(0);
            $table->date('deal_date');
            $table->string('status', 50)->default('pending_settlement'); // pending_settlement, settled, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
        });

        Schema::create('agency_tenant_leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('lease_code', 50)->unique();
            $table->foreignId('listing_id')->constrained('agency_listings')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->decimal('monthly_rent', 18, 2);
            $table->decimal('security_deposit_amount', 18, 2)->default(0);
            $table->decimal('monthly_service_charge', 18, 2)->default(0);
            $table->date('lease_start_date');
            $table->date('lease_end_date');
            $table->unsignedTinyInteger('rent_due_day')->default(5); // day of month 1-28
            $table->string('status', 50)->default('active'); // active, expired, terminated, renewed
            $table->date('key_handover_date')->nullable();
            $table->text('special_terms')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agency_tenant_leases');
        Schema::dropIfExists('agency_deals');
        Schema::dropIfExists('agency_listings');
    }
};

