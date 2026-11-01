<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $table = 'erp_projects';

    protected $fillable = [
        'company_id',
        'branch_id',
        'code',
        'name',
        'type',
        'status',
        'address',
        'location',
        'land_area',
        'land_area_unit',
        'ownership_type',
        'launch_date',
        'target_end_date',
        'budget',
        'description',
        'featured_image',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'launch_date' => 'date',
            'target_end_date' => 'date',
            'budget' => 'decimal:2',
        ];
    }

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'project_id');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'project_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class, 'project_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'project_id');
    }

    public function publicLink(): HasOne
    {
        return $this->hasOne(PublicProjectLink::class, 'erp_project_id');
    }
}

