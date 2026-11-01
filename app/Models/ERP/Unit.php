<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'erp_units';

    protected $fillable = [
        'company_id',
        'project_id',
        'building_id',
        'floor_id',
        'block_id',
        'road_id',
        'unit_code',
        'title',
        'type',
        'status',
        'facing',
        'area',
        'area_unit',
        'asking_price',
        'min_price',
        'bedrooms',
        'bathrooms',
        'balconies',
        'parking_included',
        'road_width_ft',
        'description',
        'lock_version',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'area' => 'decimal:4',
            'asking_price' => 'decimal:2',
            'min_price' => 'decimal:2',
            'road_width_ft' => 'decimal:2',
            'parking_included' => 'boolean',
            'is_active' => 'boolean',
            'lock_version' => 'integer',
        ];
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeOfStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function road(): BelongsTo
    {
        return $this->belongsTo(Road::class, 'road_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(UnitPriceHistory::class, 'unit_id')->latest('created_at');
    }

    public function statusEvents(): HasMany
    {
        return $this->hasMany(UnitStatusEvent::class, 'unit_id')->latest('created_at');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(UnitDocument::class, 'unit_id');
    }

    public function publicLink(): HasOne
    {
        return $this->hasOne(PublicPropertyLink::class, 'unit_id');
    }
}

