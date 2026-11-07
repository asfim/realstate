<?php

use App\Models\Admin;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\CrmLead;
use App\Models\ERP\CrmWebhookLog;
use App\Models\Setting;
use Database\Seeders\ErpFoundationSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(ErpFoundationSeeder::class);

    $this->admin = Admin::firstOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name'     => 'Super Admin',
            'password' => Hash::make('password'),
        ]
    );

    if (!$this->admin->hasRole('Super Admin')) {
        $this->admin->assignRole('Super Admin');
    }

    $this->company = Company::where('code', 'AURELIA')->first();
    $this->branch = Branch::where('code', 'DHK-HQ')->first();

    Setting::updateOrCreate(
        ['key' => 'meta_verify_token'],
        ['value' => 'test_meta_verify_token_123']
    );
});

test('meta webhook verification responds with challenge when token matches', function () {
    $response = $this->get('/api/webhooks/facebook?hub.mode=subscribe&hub.verify_token=test_meta_verify_token_123&hub.challenge=CHALLENGE_STRING_ABC');

    $response->assertStatus(200);
    expect($response->getContent())->toBe('CHALLENGE_STRING_ABC');
});

test('meta webhook verification rejects unauthorized requests', function () {
    $response = $this->get('/api/webhooks/facebook?hub.mode=subscribe&hub.verify_token=WRONG_TOKEN&hub.challenge=CHALLENGE_STRING_ABC');

    $response->assertStatus(403);
});

test('facebook lead ad webhook automatically creates lead in erp crm', function () {
    $payload = [
        'name'          => 'Hasibul Hasan',
        'phone'         => '01866700047',
        'email'         => 'hasib@example.com',
        'source'        => 'facebook_lead_ad',
        'campaign_name' => 'Aurelia Heights Facebook Campaign',
        'ad_name'       => '3BHK Luxury Flats Gulshan',
        'notes'         => 'Interested in flat 5A. Please call after 3 PM.',
        'lead_id'       => 'fb_lead_987654321',
    ];

    $response = $this->postJson('/api/webhooks/facebook', $payload);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $lead = CrmLead::where('phone', '8801866700047')->first();
    expect($lead)->not->toBeNull()
        ->and($lead->name)->toBe('Hasibul Hasan')
        ->and($lead->source)->toBe('facebook_lead_ad')
        ->and($lead->campaign_name)->toBe('Aurelia Heights Facebook Campaign')
        ->and($lead->ad_name)->toBe('3BHK Luxury Flats Gulshan')
        ->and($lead->lead_code)->toStartWith('LED-');

    // Verify webhook log recorded
    $log = CrmWebhookLog::where('lead_id', $lead->id)->first();
    expect($log)->not->toBeNull()
        ->and($log->platform)->toBe('facebook')
        ->and($log->status)->toBe('synced');
});

test('whatsapp cloud api webhook automatically creates whatsapp ad lead in erp crm', function () {
    $metaWhatsAppPayload = [
        'object' => 'whatsapp_business_account',
        'entry'  => [
            [
                'id'      => 'WABA_1029384756',
                'changes' => [
                    [
                        'field' => 'messages',
                        'value' => [
                            'messaging_product' => 'whatsapp',
                            'contacts'          => [
                                [
                                    'profile' => ['name' => 'Tariqul Islam'],
                                    'wa_id'   => '8801711223344',
                                ],
                            ],
                            'messages'          => [
                                [
                                    'from'      => '8801711223344',
                                    'id'        => 'wamid.HBgLODgwMTcxMTIyMzM0NBUCAB...',
                                    'timestamp' => '1730950000',
                                    'type'      => 'text',
                                    'text'      => [
                                        'body' => 'I saw your Facebook ad for Aurelia Living. Can you send me the price brochure?',
                                    ],
                                    'referral'  => [
                                        'source_type' => 'ad',
                                        'source_id'   => '1202020202020',
                                        'headline'    => 'Gulshan Lakeview Luxury Apartments',
                                        'body'        => 'Exclusive 3BHK flats with rooftop infinity pool',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $response = $this->postJson('/api/webhooks/whatsapp', $metaWhatsAppPayload);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $lead = CrmLead::where('phone', '8801711223344')->first();
    expect($lead)->not->toBeNull()
        ->and($lead->name)->toBe('Tariqul Islam')
        ->and($lead->source)->toBe('whatsapp_ad')
        ->and($lead->priority)->toBe('urgent')
        ->and($lead->campaign_name)->toContain('Gulshan Lakeview')
        ->and($lead->notes)->toContain('price brochure');

    // Verify webhook log recorded
    $log = CrmWebhookLog::where('lead_id', $lead->id)->first();
    expect($log)->not->toBeNull()
        ->and($log->platform)->toBe('whatsapp')
        ->and($log->event_type)->toBe('click_to_whatsapp');
});

test('subsequent ad interaction from same customer updates existing lead without duplication', function () {
    // 1. First lead interaction via Facebook Ad
    $this->postJson('/api/webhooks/facebook', [
        'name'          => 'Mahmudur Rahman',
        'phone'         => '01912345678',
        'email'         => 'mahmud@example.com',
        'source'        => 'facebook_lead_ad',
        'campaign_name' => 'Initial FB Ad',
    ]);

    $initialCount = CrmLead::where('phone', '8801912345678')->count();
    expect($initialCount)->toBe(1);

    // 2. Second interaction via WhatsApp Ad
    $this->postJson('/api/webhooks/whatsapp', [
        'name'          => 'Mahmudur Rahman',
        'phone'         => '01912345678',
        'message'       => 'Following up on my inquiry on WhatsApp',
        'campaign_name' => 'WhatsApp Re-engagement Ad',
    ]);

    // Lead count should STILL be 1 (no duplicate!)
    $afterCount = CrmLead::where('phone', '8801912345678')->count();
    expect($afterCount)->toBe(1);

    $lead = CrmLead::where('phone', '8801912345678')->first();
    expect($lead->activities()->count())->toBeGreaterThan(1);

    // Verify webhook log recorded re-engagement status
    $secondLog = CrmWebhookLog::where('lead_id', $lead->id)->latest('id')->first();
    expect($secondLog->status)->toBe('updated_existing');
});

test('direct lead ingest api endpoint accepts json and returns 201', function () {
    $response = $this->postJson('/api/leads/ingest', [
        'name'          => 'Dr. Farhana Yasmin',
        'phone'         => '01799887766',
        'email'         => 'farhana@hospital.com',
        'source'        => 'facebook_lead_ad',
        'campaign_name' => 'Doctors Real Estate Offer',
        'ad_name'       => 'Special Executive Discount',
        'notes'         => 'Looking for duplex apartment',
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'success',
        'message',
        'lead' => [
            'id',
            'lead_code',
            'name',
            'phone',
            'source',
            'stage',
        ],
    ]);

    expect(CrmLead::where('phone', '8801799887766')->exists())->toBeTrue();
});

test('crm integrations dashboard loads for authenticated admin', function () {
    $response = $this->actingAs($this->admin, 'admin')
        ->withSession([
            'erp_company_id' => $this->company->id,
            'erp_branch_id'  => $this->branch->id,
        ])
        ->get(route('erp.crm-integrations.index'));

    $response->assertStatus(200);
    $response->assertSee('Meta (Facebook & WhatsApp) Ad Integrations', false);
    $response->assertSee('Meta Webhook URLs for Ad Configuration', false);
    $response->assertSee('/api/webhooks/facebook');
    $response->assertSee('/api/webhooks/whatsapp');
});

test('admin can simulate facebook and whatsapp leads from integration page', function () {
    // 1. Simulate Facebook Lead
    $res1 = $this->actingAs($this->admin, 'admin')
        ->withSession([
            'erp_company_id' => $this->company->id,
            'erp_branch_id'  => $this->branch->id,
        ])
        ->post(route('erp.crm-integrations.test'), ['type' => 'facebook']);

    $res1->assertSessionHas('success');
    expect(CrmLead::where('source', 'facebook_lead_ad')->count())->toBeGreaterThan(0);

    // 2. Simulate WhatsApp Lead
    $res2 = $this->actingAs($this->admin, 'admin')
        ->withSession([
            'erp_company_id' => $this->company->id,
            'erp_branch_id'  => $this->branch->id,
        ])
        ->post(route('erp.crm-integrations.test'), ['type' => 'whatsapp']);

    $res2->assertSessionHas('success');
    expect(CrmLead::where('source', 'whatsapp_ad')->count())->toBeGreaterThan(0);
});
