<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmWebhookLog extends Model
{
    use HasFactory;

    protected $table = 'crm_webhook_logs';

    protected $fillable = [
        'company_id',
        'platform',
        'event_type',
        'external_id',
        'sender_name',
        'sender_phone',
        'sender_email',
        'campaign_name',
        'ad_name',
        'message_body',
        'lead_id',
        'status',
        'raw_payload',
        'error_message',
    ];

    protected $casts = [
        'raw_payload' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }
}
