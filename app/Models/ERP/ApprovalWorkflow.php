<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;

class ApprovalWorkflow extends Model
{
    use HasFactory;

    protected $table = 'approval_workflows';

    protected $fillable = [
        'company_id',
        'module',
        'action',
        'threshold_amount',
        'currency',
        'sequence',
        'role_id',
        'branch_id',
    ];

    protected $casts = [
        'threshold_amount' => 'decimal:2',
        'sequence' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
