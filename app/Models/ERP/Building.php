<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory;

    protected $table = 'erp_buildings';

    protected $fillable = [
        'company_id',
        'project_id',
        'code',
        'name',
        'total_floors',
        'total_units',
        'status',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class, 'building_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'building_id');
    }
}
