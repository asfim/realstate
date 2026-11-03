<?php

namespace App\Models\ERP;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrEmployee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_employees';

    protected $fillable = [
        'company_id',
        'branch_id',
        'department_id',
        'designation_id',
        'admin_id',
        'employee_code',
        'name',
        'phone',
        'email',
        'nid_no',
        'joining_date',
        'employment_type',
        'basic_salary',
        'house_rent_allowance',
        'medical_allowance',
        'conveyance_allowance',
        'gross_salary',
        'bank_name',
        'bank_account_no',
        'status',
    ];

    protected $casts = [
        'basic_salary'         => 'decimal:2',
        'house_rent_allowance' => 'decimal:2',
        'medical_allowance'    => 'decimal:2',
        'conveyance_allowance' => 'decimal:2',
        'gross_salary'         => 'decimal:2',
        'joining_date'         => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(HrPayroll::class, 'employee_id');
    }
}

