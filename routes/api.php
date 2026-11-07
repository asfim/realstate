<?php

use App\Http\Controllers\Api\MetaWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes & Meta / WhatsApp Webhooks
|--------------------------------------------------------------------------
| These endpoints receive Facebook Lead Ads, WhatsApp Business events,
| and Direct Lead Integrations without CSRF restrictions.
*/

Route::prefix('webhooks')->group(function () {
    // 1. Facebook Lead Ads Webhook
    Route::get('/facebook', [MetaWebhookController::class, 'verify'])->name('webhooks.facebook.verify');
    Route::post('/facebook', [MetaWebhookController::class, 'handleFacebook'])->name('webhooks.facebook.handle');

    // 2. WhatsApp Business Webhook (Click-to-WhatsApp Ads & Messages)
    Route::get('/whatsapp', [MetaWebhookController::class, 'verify'])->name('webhooks.whatsapp.verify');
    Route::post('/whatsapp', [MetaWebhookController::class, 'handleWhatsApp'])->name('webhooks.whatsapp.handle');

    // 3. Universal Meta Webhook Endpoint
    Route::get('/meta', [MetaWebhookController::class, 'verify'])->name('webhooks.meta.verify');
    Route::post('/meta', [MetaWebhookController::class, 'handleUniversalMeta'])->name('webhooks.meta.handle');
});

// 4. Direct Lead Ingestion Endpoint (for Zapier, Make, LeadSync, custom webhooks)
Route::post('/leads/ingest', [MetaWebhookController::class, 'directIngest'])->name('api.leads.ingest');
