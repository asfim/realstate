<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMilestone extends Model
{
    use HasFactory;

    protected $table = 'erp_project_milestones';

    protected $fillable = [
        'company_id',
        'project_id',
        'title',
        'description',
        'target_date',
        'completed_at',
        'status',
        'weightage_percent',
    ];

    protected function casts(): array
    {
        return [
            'target_date' => 'date',
            'completed_at' => 'date',
            'weightage_percent' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}

