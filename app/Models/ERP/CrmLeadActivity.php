<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmLeadActivity extends Model
{
    use HasFactory;

    protected $table = 'crm_lead_activities';

    protected $fillable = [
        'lead_id',
        'admin_id',
        'type',
        'subject',
        'description',
        'activity_date',
        'next_follow_up_date',
        'outcome',
    ];

    protected $casts = [
        'activity_date'       => 'datetime',
        'next_follow_up_date' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}

