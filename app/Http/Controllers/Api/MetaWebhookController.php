<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ERP\MetaLeadSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class MetaWebhookController extends Controller
{
    public function __construct(protected MetaLeadSyncService $syncService)
    {
    }

    /**
     * Webhook verification challenge for Meta (Facebook & WhatsApp).
     */
    public function verify(Request $request): Response
    {
        $challenge = $this->syncService->verifyWebhook($request);

        if ($challenge !== null) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden: Invalid verification token', 403);
    }

    /**
     * Handle incoming Facebook Lead Ad Webhooks.
     */
    public function handleFacebook(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            Log::info('Facebook Lead Ad Webhook Received', ['payload' => $payload]);

            $leads = $this->syncService->handleFacebookWebhook($payload);

            return response()->json([
                'success' => true,
                'message' => count($leads) . ' lead(s) processed successfully.',
                'leads'   => collect($leads)->map(fn($l) => [
                    'id'        => $l->id,
                    'lead_code' => $l->lead_code,
                    'name'      => $l->name,
                    'phone'     => $l->phone,
                    'source'    => $l->source,
                ]),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Facebook Lead Ad Webhook Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle incoming WhatsApp Webhooks (Click-to-WhatsApp Ads or Inbound Messages).
     */
    public function handleWhatsApp(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            Log::info('WhatsApp Webhook Received', ['payload' => $payload]);

            $leads = $this->syncService->handleWhatsAppWebhook($payload);

            return response()->json([
                'success' => true,
                'message' => count($leads) . ' WhatsApp lead(s) processed successfully.',
                'leads'   => collect($leads)->map(fn($l) => [
                    'id'        => $l->id,
                    'lead_code' => $l->lead_code,
                    'name'      => $l->name,
                    'phone'     => $l->phone,
                    'source'    => $l->source,
                ]),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('WhatsApp Webhook Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Universal Meta Webhook handler (auto-detects Facebook Lead Ad vs WhatsApp).
     */
    public function handleUniversalMeta(Request $request): JsonResponse
    {
        $payload = $request->all();
        $object = $payload['object'] ?? '';

        if ($object === 'whatsapp_business_account' || isset($payload['messaging_product'])) {
            return $this->handleWhatsApp($request);
        }

        return $this->handleFacebook($request);
    }

    /**
     * Direct Ingest API (for Zapier, Make, Integrately, or custom Facebook webhook relays).
     */
    public function directIngest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'nullable|string|max:255',
            'phone'         => 'required|string|max:50',
            'email'         => 'nullable|email|max:255',
            'source'        => 'nullable|string|max:50',
            'campaign_name' => 'nullable|string|max:255',
            'ad_name'       => 'nullable|string|max:255',
            'notes'         => 'nullable|string',
            'lead_id'       => 'nullable|string|max:100',
        ]);

        try {
            $source = $validated['source'] ?? 'facebook_lead_ad';
            $platform = str_contains(strtolower($source), 'whatsapp') ? 'whatsapp' : 'facebook';

            $lead = $this->syncService->ingestNormalizedLead([
                'platform'         => $platform,
                'event_type'       => 'direct_api',
                'external_id'      => $validated['lead_id'] ?? null,
                'name'             => $validated['name'] ?? 'Ad Lead',
                'phone'            => $validated['phone'],
                'email'            => $validated['email'] ?? null,
                'source'           => $source,
                'campaign_name'    => $validated['campaign_name'] ?? 'Digital Ad Campaign',
                'ad_name'          => $validated['ad_name'] ?? 'Sponsored Ad',
                'notes'            => $validated['notes'] ?? 'Ingested via Direct API',
                'raw_payload'      => $request->all(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead synchronized successfully into ERP CRM.',
                'lead'    => [
                    'id'        => $lead->id,
                    'lead_code' => $lead->lead_code,
                    'name'      => $lead->name,
                    'phone'     => $lead->phone,
                    'email'     => $lead->email,
                    'source'    => $lead->source,
                    'stage'     => $lead->stage,
                    'score'     => $lead->score,
                ],
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Direct Lead Ingest Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
