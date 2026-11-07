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
        // 1. Add campaign & external tracking columns to crm_leads if missing
        Schema::table('crm_leads', function (Blueprint $table) {
            if (!Schema::hasColumn('crm_leads', 'campaign_name')) {
                $table->string('campaign_name')->nullable()->after('source');
            }
            if (!Schema::hasColumn('crm_leads', 'ad_name')) {
                $table->string('ad_name')->nullable()->after('campaign_name');
            }
            if (!Schema::hasColumn('crm_leads', 'external_lead_id')) {
                $table->string('external_lead_id')->nullable()->after('ad_name')->index();
            }
        });

        // 2. Create crm_webhook_logs table
        if (!Schema::hasTable('crm_webhook_logs')) {
            Schema::create('crm_webhook_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('platform', 50)->default('facebook'); // facebook, whatsapp, meta_lead_ad, zapier, webhook
                $table->string('event_type', 50)->default('leadgen'); // leadgen, message, click_to_whatsapp, direct_ingest
                $table->string('external_id')->nullable()->index();
                $table->string('sender_name')->nullable();
                $table->string('sender_phone', 50)->nullable()->index();
                $table->string('sender_email')->nullable();
                $table->string('campaign_name')->nullable();
                $table->string('ad_name')->nullable();
                $table->text('message_body')->nullable();
                $table->foreignId('lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
                $table->string('status', 30)->default('synced'); // synced, updated_existing, duplicate, failed
                $table->json('raw_payload')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();

                $table->index(['platform', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_webhook_logs');

        Schema::table('crm_leads', function (Blueprint $table) {
            if (Schema::hasColumn('crm_leads', 'external_lead_id')) {
                $table->dropColumn('external_lead_id');
            }
            if (Schema::hasColumn('crm_leads', 'ad_name')) {
                $table->dropColumn('ad_name');
            }
            if (Schema::hasColumn('crm_leads', 'campaign_name')) {
                $table->dropColumn('campaign_name');
            }
        });
    }
};

