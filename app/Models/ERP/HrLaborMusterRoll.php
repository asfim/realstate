<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrLaborMusterRoll extends Model
{
    use HasFactory;

    protected $table = 'hr_labor_muster_rolls';

    protected $fillable = [
        'company_id',
        'project_id',
        'laborer_id',
        'work_date',
        'is_present',
        'regular_hours',
        'overtime_hours',
        'regular_wage',
        'overtime_wage',
        'total_wage',
        'payment_status',
        'recorded_by',
    ];

    protected $casts = [
        'is_present'     => 'boolean',
        'regular_hours'  => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'regular_wage'   => 'decimal:2',
        'overtime_wage'  => 'decimal:2',
        'total_wage'     => 'decimal:2',
        'work_date'      => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(DevProject::class, 'project_id');
    }

    public function laborer(): BelongsTo
    {
        return $this->belongsTo(HrDailyLaborer::class, 'laborer_id');
    }

    public function recordedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'recorded_by');
    }
}

