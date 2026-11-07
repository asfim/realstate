<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\ERP\MetaLeadSyncService;
use Illuminate\Database\Seeder;

class MetaLeadSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Meta Integration Settings
        Setting::updateOrCreate(
            ['key' => 'meta_verify_token'],
            ['value' => 'aurelia_erp_meta_2026']
        );

        Setting::updateOrCreate(
            ['key' => 'meta_auto_assign'],
            ['value' => '1']
        );

        // 2. Ingest Sample Facebook Ad Lead
        $service = app(MetaLeadSyncService::class);

        $lead1 = $service->ingestNormalizedLead([
            'platform' => 'facebook',
            'name' => 'Zubair Hossain',
            'phone' => '01811223344',
            'email' => 'zubair.hossain@example.com',
            'source' => 'facebook_lead_ad',
            'campaign_name' => 'Aurelia Heights Luxury Flats Campaign',
            'ad_name' => 'Gulshan 3BHK Premium Ad',
            'notes' => 'Customer submitted Facebook Instant Lead Form inquiring about 3BHK unit.',
        ]);

        // 3. Ingest Sample WhatsApp Ad Lead
        $lead2 = $service->ingestNormalizedLead([
            'platform' => 'whatsapp',
            'name' => 'Tanjila Akter',
            'phone' => '01755667788',
            'email' => 'tanjila.akter@example.com',
            'source' => 'whatsapp_ad',
            'campaign_name' => 'Click-to-WhatsApp Property Promo',
            'ad_name' => 'Direct WhatsApp Chat Ad',
            'notes' => 'Customer initiated WhatsApp message from Facebook Ad referral.',
        ]);

        $this->command?->info("Sample Leads Created: {$lead1->lead_code} & {$lead2->lead_code}");
    }
}

