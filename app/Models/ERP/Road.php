<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Road extends Model
{
    use HasFactory;

    protected $table = 'erp_roads';

    protected $fillable = [
        'company_id',
        'block_id',
        'road_name_or_no',
        'width_ft',
    ];

    protected function casts(): array
    {
        return [
            'width_ft' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'road_id');
    }
}
