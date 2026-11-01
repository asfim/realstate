<?php

namespace App\Models\ERP;

use App\Models\FeaturedProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicProjectLink extends Model
{
    use HasFactory;

    protected $table = 'erp_public_project_links';

    protected $fillable = [
        'featured_project_id',
        'erp_project_id',
        'notes',
    ];

    public function featuredProject(): BelongsTo
    {
        return $this->belongsTo(FeaturedProject::class, 'featured_project_id');
    }

    public function erpProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'erp_project_id');
    }
}

