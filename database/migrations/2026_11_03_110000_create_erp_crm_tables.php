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
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('assigned_agent_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('submission_id')->nullable()->constrained('submissions')->nullOnDelete();
            $table->string('lead_code', 50)->unique();
            $table->string('name');
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->string('secondary_phone', 50)->nullable();
            $table->string('source', 50)->default('web_form'); // web_form, walk_in, social_media, phone_call, agent_referral, newspaper_ad, other
            $table->string('stage', 50)->default('new'); // new, contacted, qualified, site_visit_scheduled, site_visit_completed, negotiation, booked, lost, junk
            $table->string('priority', 20)->default('medium'); // low, medium, high, urgent
            $table->integer('score')->default(0);
            $table->string('property_type_interest', 100)->nullable(); // Apartment, Commercial, Land, Duplex, Plot
            $table->foreignId('featured_project_id')->nullable()->constrained('featured_projects')->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->decimal('min_budget', 18, 2)->nullable();
            $table->decimal('max_budget', 18, 2)->nullable();
            $table->string('preferred_location')->nullable();
            $table->string('preferred_size_sqft', 100)->nullable();
            $table->text('lost_reason')->nullable();
            $table->dateTime('next_follow_up_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'stage']);
            $table->index(['company_id', 'assigned_agent_id']);
            $table->index('phone');
        });

        Schema::create('crm_lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('type', 50)->default('call'); // call, meeting, site_visit, email, whatsapp, note, status_change
            $table->string('subject');
            $table->text('description')->nullable();
            $table->dateTime('activity_date');
            $table->dateTime('next_follow_up_date')->nullable();
            $table->string('outcome', 50)->nullable(); // connected, busy, no_answer, interested, not_interested, rescheduled, completed
            $table->timestamps();

            $table->index(['lead_id', 'created_at']);
        });

        Schema::create('crm_site_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('featured_project_id')->nullable()->constrained('featured_projects')->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('assigned_agent_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->dateTime('completed_at')->nullable();
            $table->string('status', 30)->default('scheduled'); // scheduled, completed, cancelled, no_show
            $table->text('client_feedback')->nullable();
            $table->tinyInteger('client_rating')->nullable(); // 1 to 5
            $table->text('agent_remarks')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'scheduled_at']);
        });

        Schema::create('crm_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('customer_code', 50)->unique();
            $table->foreignId('converted_from_lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->string('name');
            $table->string('father_or_husband_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->string('emergency_contact', 50)->nullable();
            $table->string('nid_number', 50)->nullable();
            $table->string('tin_number', 50)->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('nominee_name')->nullable();
            $table->string('nominee_relation', 100)->nullable();
            $table->string('nominee_nid', 50)->nullable();
            $table->string('nominee_phone', 50)->nullable();
            $table->string('nominee_photo_path')->nullable();
            $table->string('customer_photo_path')->nullable();
            $table->string('status', 20)->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'phone']);
            $table->index(['company_id', 'nid_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_customers');
        Schema::dropIfExists('crm_site_visits');
        Schema::dropIfExists('crm_lead_activities');
        Schema::dropIfExists('crm_leads');
    }
};

