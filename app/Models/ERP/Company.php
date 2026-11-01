<?php

namespace App\Models\ERP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $table = 'erp_companies';

    protected $fillable = [
        'name',
        'code',
        'currency',
        'email',
        'phone',
        'address',
        'tax_id',
        'logo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'company_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'company_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'company_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'company_id');
    }

    public function staffAssignments(): HasMany
    {
        return $this->hasMany(StaffAssignment::class, 'company_id');
    }
}

