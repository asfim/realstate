<?php

namespace App\Services\ERP;

use App\Models\Admin;
use App\Models\ERP\Company;
use App\Models\ERP\CrmLead;
use App\Models\ERP\CrmLeadActivity;
use App\Models\ERP\CrmWebhookLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaLeadSyncService
{
    public function __construct(
        protected DocumentSequenceService $sequenceService,
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog,
        protected LeadRoutingService $routingService
    ) {
    }

    /**
     * Get the configured verify token for Meta webhooks.
     */
    public function getVerifyToken(): string
    {
        return Setting::where('key', 'meta_verify_token')->value('value')
            ?: config('services.meta.verify_token', env('META_VERIFY_TOKEN', 'aurelia_erp_meta_2026'));
    }

    /**
     * Verify Meta incoming subscription challenge.
     */
    public function verifyWebhook(Request $request): ?string
    {
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        if ($mode === 'subscribe' && $token === $this->getVerifyToken()) {
            return $challenge;
        }

        return null;
    }

    /**
     * Process Facebook Lead Ads Webhook.
     */
    public function handleFacebookWebhook(array $payload): array
    {
        $results = [];

        // 1. Direct Webhook Ingest (e.g. from Zapier / Make / LeadSync / Custom Relay)
        if (isset($payload['phone']) || isset($payload['name'])) {
            $lead = $this->ingestNormalizedLead([
                'platform'         => 'facebook',
                'event_type'       => 'direct_ingest',
                'external_id'      => $payload['lead_id'] ?? $payload['id'] ?? null,
                'name'             => $payload['name'] ?? $payload['full_name'] ?? 'Facebook Lead',
                'phone'            => $payload['phone'] ?? $payload['phone_number'] ?? '',
                'email'            => $payload['email'] ?? null,
                'source'           => $payload['source'] ?? 'facebook_lead_ad',
                'campaign_name'    => $payload['campaign_name'] ?? $payload['campaign'] ?? 'Facebook Ad Campaign',
                'ad_name'          => $payload['ad_name'] ?? $payload['ad'] ?? 'Facebook Instant Form',
                'notes'            => $payload['notes'] ?? $payload['message'] ?? 'Lead received from Facebook Ad Form',
                'raw_payload'      => $payload,
            ]);

            return [$lead];
        }

        // 2. Official Meta Graph Webhook (field: leadgen)
        $entries = $payload['entry'] ?? [];
        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                if (($change['field'] ?? '') === 'leadgen') {
                    $val = $change['value'] ?? [];
                    $leadgenId = $val['leadgen_id'] ?? null;

                    if ($leadgenId) {
                        $leadDetails = $this->fetchMetaLeadgenDetails($leadgenId, $val);
                        $lead = $this->ingestNormalizedLead([
                            'platform'         => 'facebook',
                            'event_type'       => 'leadgen',
                            'external_id'      => (string) $leadgenId,
                            'name'             => $leadDetails['name'] ?? 'Meta Ad Lead',
                            'phone'            => $leadDetails['phone'] ?? '',
                            'email'            => $leadDetails['email'] ?? null,
                            'source'           => 'facebook_lead_ad',
                            'campaign_name'    => $leadDetails['campaign_name'] ?? ('Form #' . ($val['form_id'] ?? '')),
                            'ad_name'          => $leadDetails['ad_name'] ?? ('Ad #' . ($val['ad_id'] ?? '')),
                            'notes'            => $leadDetails['notes'] ?? ('Facebook Leadgen ID: ' . $leadgenId),
                            'raw_payload'      => array_merge($val, ['fetched_details' => $leadDetails]),
                        ]);
                        $results[] = $lead;
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Process WhatsApp Business Webhook (Click-to-WhatsApp Ads or Incoming WhatsApp Messages).
     */
    public function handleWhatsAppWebhook(array $payload): array
    {
        $results = [];

        // 1. Direct Webhook Ingest
        if (isset($payload['phone']) || (isset($payload['from']) && !isset($payload['entry']))) {
            $lead = $this->ingestNormalizedLead([
                'platform'         => 'whatsapp',
                'event_type'       => 'direct_ingest',
                'external_id'      => $payload['message_id'] ?? $payload['id'] ?? null,
                'name'             => $payload['name'] ?? $payload['sender_name'] ?? 'WhatsApp Lead',
                'phone'            => $payload['phone'] ?? $payload['from'] ?? '',
                'email'            => $payload['email'] ?? null,
                'source'           => $payload['source'] ?? 'whatsapp_ad',
                'campaign_name'    => $payload['campaign_name'] ?? 'WhatsApp Ad Campaign',
                'ad_name'          => $payload['ad_name'] ?? 'Click-to-WhatsApp Ad',
                'notes'            => $payload['message'] ?? $payload['body'] ?? 'Customer initiated WhatsApp conversation from ad.',
                'raw_payload'      => $payload,
            ]);

            return [$lead];
        }

        // 2. Official Meta WhatsApp Cloud API format
        $entries = $payload['entry'] ?? [];
        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];
            foreach ($changes as $change) {
                $value = $change['value'] ?? [];
                $contacts = $value['contacts'] ?? [];
                $messages = $value['messages'] ?? [];

                // Map contacts by wa_id
                $contactsMap = [];
                foreach ($contacts as $c) {
                    $waId = $c['wa_id'] ?? null;
                    if ($waId) {
                        $contactsMap[$waId] = $c['profile']['name'] ?? 'WhatsApp Contact';
                    }
                }

                foreach ($messages as $msg) {
                    $from = $msg['from'] ?? null;
                    $msgId = $msg['id'] ?? null;
                    $msgBody = $msg['text']['body'] ?? ($msg['type'] ?? 'message');
                    $referral = $msg['referral'] ?? null;

                    $isFromAd = !empty($referral);
                    $adHeadline = $referral['headline'] ?? null;
                    $adBody = $referral['body'] ?? null;
                    $adSourceUrl = $referral['source_url'] ?? null;

                    $campaign = $adHeadline ? "Ad: {$adHeadline}" : 'WhatsApp Inbound';
                    $adName = $adBody ?: ($isFromAd ? 'Click-to-WhatsApp Facebook Ad' : 'Direct WhatsApp Chat');
                    $source = $isFromAd ? 'whatsapp_ad' : 'whatsapp';

                    $notesText = "WhatsApp Message: \"{$msgBody}\"";
                    if ($isFromAd) {
                        $notesText .= "\nReferred from Facebook Ad: {$adHeadline} ({$adSourceUrl})";
                    }

                    $contactName = $contactsMap[$from] ?? 'WhatsApp User';

                    $lead = $this->ingestNormalizedLead([
                        'platform'         => 'whatsapp',
                        'event_type'       => $isFromAd ? 'click_to_whatsapp' : 'incoming_message',
                        'external_id'      => $msgId,
                        'name'             => $contactName,
                        'phone'            => (string) $from,
                        'email'            => null,
                        'source'           => $source,
                        'campaign_name'    => $campaign,
                        'ad_name'          => $adName,
                        'notes'            => $notesText,
                        'raw_payload'      => $value,
                    ]);

                    $results[] = $lead;
                }
            }
        }

        return $results;
    }

    /**
     * Fetch full lead field values from Meta Graph API using leadgen_id.
     */
    protected function fetchMetaLeadgenDetails(string $leadgenId, array $fallbackValue = []): array
    {
        $accessToken = Setting::where('key', 'meta_page_access_token')->value('value')
            ?: config('services.meta.page_access_token', env('META_PAGE_ACCESS_TOKEN'));

        if (!$accessToken) {
            return [
                'name'          => 'Meta Ad Lead',
                'phone'         => '',
                'email'         => null,
                'campaign_name' => 'Form #' . ($fallbackValue['form_id'] ?? ''),
                'ad_name'       => 'Ad #' . ($fallbackValue['ad_id'] ?? ''),
                'notes'         => 'Meta Leadgen ID: ' . $leadgenId . ' (Configure Meta Page Access Token in ERP CRM to fetch full form fields automatically).',
            ];
        }

        try {
            $response = Http::timeout(10)->get("https://graph.facebook.com/v19.0/{$leadgenId}", [
                'access_token' => $accessToken,
                'fields'       => 'created_time,id,ad_id,ad_name,campaign_id,campaign_name,form_id,field_data',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $fieldData = $data['field_data'] ?? [];

                $extracted = [
                    'name'          => null,
                    'phone'         => null,
                    'email'         => null,
                    'campaign_name' => $data['campaign_name'] ?? ('Form #' . ($data['form_id'] ?? '')),
                    'ad_name'       => $data['ad_name'] ?? ('Ad #' . ($data['ad_id'] ?? '')),
                    'extra_fields'  => [],
                ];

                foreach ($fieldData as $field) {
                    $key = strtolower($field['name'] ?? '');
                    $val = $field['values'][0] ?? '';

                    if (str_contains($key, 'full_name') || str_contains($key, 'name')) {
                        $extracted['name'] = $val;
                    } elseif (str_contains($key, 'phone') || str_contains($key, 'mobile')) {
                        $extracted['phone'] = $val;
                    } elseif (str_contains($key, 'email')) {
                        $extracted['email'] = $val;
                    } else {
                        $extracted['extra_fields'][$field['name']] = $val;
                    }
                }

                $notes = "Facebook Instant Form Lead (ID: {$leadgenId})";
                if (!empty($extracted['extra_fields'])) {
                    $notes .= "\nForm Answers:\n";
                    foreach ($extracted['extra_fields'] as $k => $v) {
                        $notes .= "- {$k}: {$v}\n";
                    }
                }
                $extracted['notes'] = trim($notes);

                return $extracted;
            } else {
                Log::warning('Meta Graph API Leadgen Fetch Failed: ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Meta Graph API Exception: ' . $e->getMessage());
        }

        return [
            'name'          => 'Meta Ad Lead',
            'phone'         => '',
            'email'         => null,
            'campaign_name' => 'Form #' . ($fallbackValue['form_id'] ?? ''),
            'ad_name'       => 'Ad #' . ($fallbackValue['ad_id'] ?? ''),
            'notes'         => 'Meta Leadgen ID: ' . $leadgenId,
        ];
    }

    /**
     * Ingest a normalized lead into ERP CRM with deduplication and agent auto-routing.
     */
    public function ingestNormalizedLead(array $data): CrmLead
    {
        $platform = $data['platform'] ?? 'meta';
        $eventType = $data['event_type'] ?? 'leadgen';
        $externalId = $data['external_id'] ?? null;
        $rawPhone = trim($data['phone'] ?? '');
        $cleanPhone = $this->cleanPhoneNumber($rawPhone);
        $name = trim($data['name'] ?? '') ?: ($cleanPhone ? "Lead {$cleanPhone}" : 'New Ad Lead');
        $email = !empty($data['email']) ? trim($data['email']) : null;
        $source = $data['source'] ?? ($platform === 'whatsapp' ? 'whatsapp_ad' : 'facebook_lead_ad');
        $campaignName = $data['campaign_name'] ?? null;
        $adName = $data['ad_name'] ?? null;
        $notes = $data['notes'] ?? null;
        $rawPayload = $data['raw_payload'] ?? [];

        // 1. Resolve Company and Branch
        $company = $this->context->currentCompany();
        if (!$company) {
            $company = Company::where('status', 'active')->first() ?? Company::first();
        }
        $companyId = $company?->id ?? 1;

        $branch = $this->context->currentBranch();
        $branchId = $branch?->id;

        // 2. Check for existing Lead with same phone number
        $existingLead = null;
        if ($cleanPhone !== '') {
            $existingLead = CrmLead::where('company_id', $companyId)
                ->where(function ($q) use ($cleanPhone, $rawPhone) {
                    $q->where('phone', $cleanPhone)
                        ->orWhere('phone', $rawPhone)
                        ->orWhere('phone', 'like', "%" . substr($cleanPhone, -10));
                })
                ->latest()
                ->first();
        }

        // 3. If Lead already exists: Append activity, boost score, and record log
        if ($existingLead) {
            $activitySubject = $platform === 'whatsapp'
                ? 'WhatsApp Re-engagement from Ad'
                : 'Facebook Ad Re-submission';

            CrmLeadActivity::create([
                'lead_id'       => $existingLead->id,
                'admin_id'      => $existingLead->assigned_agent_id,
                'type'          => $platform === 'whatsapp' ? 'whatsapp' : 'status_change',
                'subject'       => $activitySubject,
                'description'   => "Customer engaged again via {$platform}.\nCampaign: {$campaignName}\nAd: {$adName}\nDetails: {$notes}",
                'activity_date' => now(),
                'outcome'       => 'connected',
            ]);

            // If existing lead had no email and we now received one, update it
            if (!$existingLead->email && $email) {
                $existingLead->email = $email;
            }
            if ($campaignName && !$existingLead->campaign_name) {
                $existingLead->campaign_name = $campaignName;
            }
            if ($adName && !$existingLead->ad_name) {
                $existingLead->ad_name = $adName;
            }

            // Score boost for multi-touchpoint engagement
            $existingLead->score = min(100, ($existingLead->score ?? 10) + 15);
            $existingLead->next_follow_up_at = now();
            $existingLead->save();

            // Record Webhook Log
            CrmWebhookLog::create([
                'company_id'    => $companyId,
                'platform'      => $platform,
                'event_type'    => $eventType,
                'external_id'   => $externalId,
                'sender_name'   => $name,
                'sender_phone'  => $cleanPhone ?: $rawPhone,
                'sender_email'  => $email,
                'campaign_name' => $campaignName,
                'ad_name'       => $adName,
                'message_body'  => $notes,
                'lead_id'       => $existingLead->id,
                'status'        => 'updated_existing',
                'raw_payload'   => $rawPayload,
            ]);

            $this->auditLog->log('crm.lead_reengaged_' . $platform, $existingLead, null, [
                'campaign' => $campaignName,
                'ad'       => $adName,
            ], $companyId, $branchId);

            return $existingLead;
        }

        // 4. Create New ERP Lead
        $leadCode = $this->sequenceService->getNextNumber('lead', $companyId, $branchId);
        $assignedAgent = $this->routingService->getNextAvailableAgent($companyId, $branchId);

        $lead = CrmLead::create([
            'company_id'        => $companyId,
            'branch_id'         => $branchId,
            'assigned_agent_id' => $assignedAgent?->id,
            'lead_code'         => $leadCode,
            'name'              => $name,
            'phone'             => $cleanPhone ?: ($rawPhone ?: 'N/A'),
            'email'             => $email,
            'source'            => $source,
            'campaign_name'     => $campaignName,
            'ad_name'           => $adName,
            'external_lead_id'  => $externalId,
            'stage'             => 'new',
            'priority'          => $platform === 'whatsapp' ? 'urgent' : 'high',
            'notes'             => $notes,
        ]);

        $lead->score = $lead->calculateScore();
        $lead->save();

        // 5. Create Initial Lead Activity
        $activitySubject = $platform === 'whatsapp'
            ? 'Inbound WhatsApp Ad Lead'
            : 'Facebook Instant Form Captured';

        CrmLeadActivity::create([
            'lead_id'       => $lead->id,
            'admin_id'      => $assignedAgent?->id,
            'type'          => $platform === 'whatsapp' ? 'whatsapp' : 'status_change',
            'subject'       => $activitySubject,
            'description'   => "Automated synchronization from {$platform} ad.\nCampaign: {$campaignName}\nAd: {$adName}\nAssigned To: " . ($assignedAgent?->name ?? 'Unassigned'),
            'activity_date' => now(),
            'outcome'       => 'connected',
        ]);

        // 6. Record Webhook Log
        CrmWebhookLog::create([
            'company_id'    => $companyId,
            'platform'      => $platform,
            'event_type'    => $eventType,
            'external_id'   => $externalId,
            'sender_name'   => $name,
            'sender_phone'  => $cleanPhone ?: $rawPhone,
            'sender_email'  => $email,
            'campaign_name' => $campaignName,
            'ad_name'       => $adName,
            'message_body'  => $notes,
            'lead_id'       => $lead->id,
            'status'        => 'synced',
            'raw_payload'   => $rawPayload,
        ]);

        $this->auditLog->log('crm.lead_synced_' . $platform, $lead, null, $lead->toArray(), $companyId, $branchId);

        return $lead;
    }

    /**
     * Clean and format phone number with country code.
     */
    public function cleanPhoneNumber(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if ($clean === '') {
            return '';
        }

        // Bangladesh standard normalization (017... -> 88017...)
        if (str_starts_with($clean, '01') && strlen($clean) === 11) {
            return '88' . $clean;
        }

        return $clean;
    }
}

