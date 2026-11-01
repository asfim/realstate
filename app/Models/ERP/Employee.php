<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'erp_employees';

    protected $fillable = [
        'company_id',
        'branch_id',
        'department_id',
        'admin_id',
        'employee_code',
        'name',
        'phone',
        'email',
        'designation',
        'joining_date',
        'basic_salary',
        'house_rent',
        'medical_allowance',
        'other_allowance',
        'tax_deduction',
        'provident_fund',
        'gross_salary',
        'bank_name',
        'bank_account_no',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'basic_salary' => 'decimal:2',
            'house_rent' => 'decimal:2',
            'medical_allowance' => 'decimal:2',
            'other_allowance' => 'decimal:2',
            'tax_deduction' => 'decimal:2',
            'provident_fund' => 'decimal:2',
            'gross_salary' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'employee_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'employee_id');
    }

    public function calculateGrossSalary(): float
    {
        return (float)$this->basic_salary + (float)$this->house_rent + (float)$this->medical_allowance + (float)$this->other_allowance;
    }

    public function calculateNetSalary(): float
    {
        $gross = $this->calculateGrossSalary();
        $deductions = (float)$this->tax_deduction + (float)$this->provident_fund;
        return max(0, $gross - $deductions);
    }
}

