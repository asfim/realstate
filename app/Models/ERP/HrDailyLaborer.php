<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrDailyLaborer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_daily_laborers';

    protected $fillable = [
        'company_id',
        'assigned_project_id',
        'laborer_code',
        'name',
        'phone',
        'nid_no',
        'trade',
        'daily_wage_rate',
        'overtime_hourly_rate',
        'status',
    ];

    protected $casts = [
        'daily_wage_rate'      => 'decimal:2',
        'overtime_hourly_rate' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedProject(): BelongsTo
    {
        return $this->belongsTo(DevProject::class, 'assigned_project_id');
    }

    public function musterRolls(): HasMany
    {
        return $this->hasMany(HrLaborMusterRoll::class, 'laborer_id');
    }
}

