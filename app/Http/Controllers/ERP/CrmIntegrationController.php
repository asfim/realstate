<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ERP\CrmWebhookLog;
use App\Models\Setting;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\MetaLeadSyncService;
use Illuminate\Http\Request;

class CrmIntegrationController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected MetaLeadSyncService $syncService
    ) {
    }

    /**
     * Show Meta & WhatsApp Integrations Dashboard.
     */
    public function index()
    {
        $currentCompany = $this->context->currentCompany();

        $verifyToken = Setting::where('key', 'meta_verify_token')->value('value') ?: 'aurelia_erp_meta_2026';
        $pageAccessToken = Setting::where('key', 'meta_page_access_token')->value('value') ?: '';
        $whatsappPhoneId = Setting::where('key', 'whatsapp_phone_number_id')->value('value') ?: '';
        $autoAssign = Setting::where('key', 'meta_auto_assign')->value('value') !== '0';

        // Webhook URLs
        $appUrl = rtrim(config('app.url'), '/');
        $facebookWebhookUrl = url('/api/webhooks/facebook');
        $whatsappWebhookUrl = url('/api/webhooks/whatsapp');
        $universalWebhookUrl = url('/api/webhooks/meta');
        $directIngestUrl = url('/api/leads/ingest');

        // Recent Webhook Logs
        $logs = CrmWebhookLog::with('lead')
            ->when($currentCompany, fn($q) => $q->where('company_id', $currentCompany->id))
            ->latest()
            ->paginate(15);

        // Stats
        $totalLogs = CrmWebhookLog::count();
        $facebookCount = CrmWebhookLog::where('platform', 'facebook')->count();
        $whatsappCount = CrmWebhookLog::where('platform', 'whatsapp')->count();
        $todayCount = CrmWebhookLog::whereDate('created_at', today())->count();

        $branches = $this->context->accessibleBranches();
        $agents = Admin::all();

        return view('erp.crm.integrations.index', compact(
            'verifyToken',
            'pageAccessToken',
            'whatsappPhoneId',
            'autoAssign',
            'facebookWebhookUrl',
            'whatsappWebhookUrl',
            'universalWebhookUrl',
            'directIngestUrl',
            'logs',
            'totalLogs',
            'facebookCount',
            'whatsappCount',
            'todayCount',
            'branches',
            'agents'
        ));
    }

    /**
     * Update Meta / WhatsApp configuration.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'meta_verify_token'         => 'required|string|max:100',
            'meta_page_access_token'    => 'nullable|string',
            'whatsapp_phone_number_id'  => 'nullable|string|max:100',
            'meta_auto_assign'          => 'nullable|boolean',
        ]);

        Setting::updateOrCreate(['key' => 'meta_verify_token'], ['value' => $validated['meta_verify_token']]);
        Setting::updateOrCreate(['key' => 'meta_page_access_token'], ['value' => $validated['meta_page_access_token'] ?? '']);
        Setting::updateOrCreate(['key' => 'whatsapp_phone_number_id'], ['value' => $validated['whatsapp_phone_number_id'] ?? '']);
        Setting::updateOrCreate(['key' => 'meta_auto_assign'], ['value' => $request->boolean('meta_auto_assign') ? '1' : '0']);

        return redirect()->route('erp.crm-integrations.index')
            ->with('success', 'Meta & WhatsApp integration settings updated successfully.');
    }

    /**
     * Simulate a test Facebook or WhatsApp Lead to verify webhook sync.
     */
    public function testWebhook(Request $request)
    {
        $type = $request->input('type', 'facebook');

        if ($type === 'whatsapp') {
            $randomPhone = '017' . rand(10000000, 99999999);
            $lead = $this->syncService->ingestNormalizedLead([
                'platform'         => 'whatsapp',
                'event_type'       => 'click_to_whatsapp',
                'external_id'      => 'test_wamid_' . time(),
                'name'             => 'Test WhatsApp Lead (' . now()->format('H:i') . ')',
                'phone'            => $randomPhone,
                'email'            => 'whatsapp_lead_' . rand(100, 999) . '@example.com',
                'source'           => 'whatsapp_ad',
                'campaign_name'    => 'Facebook Click-to-WhatsApp Campaign',
                'ad_name'          => 'Sponsored WhatsApp Ad',
                'notes'            => 'Hi! I saw your Facebook ad for luxury apartments and want to know pricing and floor plans.',
                'raw_payload'      => ['simulation' => true, 'timestamp' => now()->toIso8601String()],
            ]);

            return redirect()->route('erp.crm-integrations.index')
                ->with('success', "Test WhatsApp Lead synced successfully! Lead Code: {$lead->lead_code}");
        }

        // Facebook Lead Ad simulation
        $randomPhone = '018' . rand(10000000, 99999999);
        $lead = $this->syncService->ingestNormalizedLead([
            'platform'         => 'facebook',
            'event_type'       => 'leadgen',
            'external_id'      => 'test_leadgen_' . time(),
            'name'             => 'Test Facebook Ad Lead (' . now()->format('H:i') . ')',
            'phone'            => $randomPhone,
            'email'            => 'fb_lead_' . rand(100, 999) . '@example.com',
            'source'           => 'facebook_lead_ad',
            'campaign_name'    => 'Aurelia Heights Q4 Facebook Ads',
            'ad_name'          => 'Instant Form: 3BHK Luxury Flats',
            'notes'            => "Form Answers:\n- Preferred Size: 1850 sqft\n- Budget: 1.5 - 2.0 Cr\n- Location: Gulshan / Banani",
            'raw_payload'      => ['simulation' => true, 'timestamp' => now()->toIso8601String()],
        ]);

        return redirect()->route('erp.crm-integrations.index')
            ->with('success', "Test Facebook Ad Lead synced successfully! Lead Code: {$lead->lead_code}");
    }
}
