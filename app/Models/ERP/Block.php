<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    use HasFactory;

    protected $table = 'erp_blocks';

    protected $fillable = [
        'company_id',
        'project_id',
        'code',
        'name',
        'description',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function roads(): HasMany
    {
        return $this->hasMany(Road::class, 'block_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'block_id');
    }
}

