<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrPayroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_payrolls';

    protected $fillable = [
        'company_id',
        'branch_id',
        'employee_id',
        'payroll_code',
        'salary_month',
        'salary_year',
        'gross_salary',
        'bonus',
        'deductions',
        'net_payable',
        'payment_status',
        'payment_date',
        'payment_method',
        'processed_by',
    ];

    protected $casts = [
        'gross_salary'  => 'decimal:2',
        'bonus'         => 'decimal:2',
        'deductions'    => 'decimal:2',
        'net_payable'   => 'decimal:2',
        'salary_month'  => 'integer',
        'salary_year'   => 'integer',
        'payment_date'  => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }
}

