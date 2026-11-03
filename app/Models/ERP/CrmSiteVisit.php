<?php

namespace App\Models\ERP;

use App\Models\Admin;
use App\Models\FeaturedProject;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmSiteVisit extends Model
{
    use HasFactory;

    protected $table = 'crm_site_visits';

    protected $fillable = [
        'lead_id',
        'company_id',
        'branch_id',
        'featured_project_id',
        'property_id',
        'assigned_agent_id',
        'scheduled_at',
        'completed_at',
        'status',
        'client_feedback',
        'client_rating',
        'agent_remarks',
    ];

    protected $casts = [
        'scheduled_at'  => 'datetime',
        'completed_at'  => 'datetime',
        'client_rating' => 'integer',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_agent_id');
    }

    public function featuredProject(): BelongsTo
    {
        return $this->belongsTo(FeaturedProject::class, 'featured_project_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
