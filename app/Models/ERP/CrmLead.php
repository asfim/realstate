<?php

namespace App\Models\ERP;

use App\Models\Admin;
use App\Models\FeaturedProject;
use App\Models\Property;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_leads';

    protected $fillable = [
        'company_id',
        'branch_id',
        'assigned_agent_id',
        'submission_id',
        'lead_code',
        'name',
        'phone',
        'email',
        'secondary_phone',
        'source',
        'stage',
        'priority',
        'score',
        'property_type_interest',
        'featured_project_id',
        'property_id',
        'min_budget',
        'max_budget',
        'preferred_location',
        'preferred_size_sqft',
        'lost_reason',
        'next_follow_up_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'min_budget'        => 'decimal:2',
        'max_budget'        => 'decimal:2',
        'score'             => 'integer',
        'next_follow_up_at' => 'datetime',
    ];

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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function featuredProject(): BelongsTo
    {
        return $this->belongsTo(FeaturedProject::class, 'featured_project_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmLeadActivity::class, 'lead_id')->latest('activity_date');
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(CrmSiteVisit::class, 'lead_id')->latest('scheduled_at');
    }

    public function customer(): HasOne
    {
        return $this->hasOne(CrmCustomer::class, 'converted_from_lead_id');
    }

    /**
     * Helper to compute dynamic lead score.
     */
    public function calculateScore(): int
    {
        $score = 10; // Base score

        if ($this->phone && $this->email) {
            $score += 15;
        }

        if ($this->min_budget > 0 || $this->max_budget > 0) {
            $score += 20;
        }

        if ($this->featured_project_id || $this->property_id) {
            $score += 20;
        }

        if ($this->siteVisits()->where('status', 'completed')->exists()) {
            $score += 25;
        } elseif ($this->siteVisits()->where('status', 'scheduled')->exists()) {
            $score += 15;
        }

        if ($this->stage === 'negotiation') {
            $score += 10;
        } elseif ($this->stage === 'booked') {
            $score = 100;
        } elseif (in_array($this->stage, ['lost', 'junk'])) {
            $score = 0;
        }

        return min(100, $score);
    }
}

