<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Permanent Employees Profile
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('employee_code', 50)->unique();
            $table->string('name');
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->string('nid_no', 100)->nullable();
            $table->date('joining_date');
            $table->string('employment_type', 50)->default('permanent'); // permanent, probation, contract
            $table->decimal('basic_salary', 14, 2);
            $table->decimal('house_rent_allowance', 14, 2)->default(0);
            $table->decimal('medical_allowance', 14, 2)->default(0);
            $table->decimal('conveyance_allowance', 14, 2)->default(0);
            $table->decimal('gross_salary', 14, 2);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_no', 100)->nullable();
            $table->string('status', 50)->default('active'); // active, resigned, terminated
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
        });

        // 2. Monthly Staff Payroll Runs
        Schema::create('hr_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('payroll_code', 50)->unique();
            $table->unsignedSmallInteger('salary_month'); // 1 - 12
            $table->unsignedSmallInteger('salary_year'); // 2026
            $table->decimal('gross_salary', 14, 2);
            $table->decimal('bonus', 14, 2)->default(0);
            $table->decimal('deductions', 14, 2)->default(0);
            $table->decimal('net_payable', 14, 2);
            $table->string('payment_status', 50)->default('unpaid'); // unpaid, paid
            $table->date('payment_date')->nullable();
            $table->string('payment_method', 50)->nullable(); // bank_transfer, cheque, cash
            $table->foreignId('processed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['employee_id', 'salary_month', 'salary_year']);
            $table->index(['company_id', 'payment_status']);
        });

        // 3. Site Daily Labor Workers Master
        Schema::create('hr_daily_laborers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('assigned_project_id')->nullable()->constrained('dev_projects')->nullOnDelete();
            $table->string('laborer_code', 50)->unique();
            $table->string('name');
            $table->string('phone', 50)->nullable();
            $table->string('nid_no', 100)->nullable();
            $table->string('trade', 50); // mason, rod_binder, carpenter, electrician, plumber, helper
            $table->decimal('daily_wage_rate', 10, 2);
            $table->decimal('overtime_hourly_rate', 10, 2)->default(0);
            $table->string('status', 50)->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'trade']);
            $table->index(['assigned_project_id', 'status']);
        });

        // 4. Daily Labor Muster Roll & Wage Sheet
        Schema::create('hr_labor_muster_rolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('dev_projects')->cascadeOnDelete();
            $table->foreignId('laborer_id')->constrained('hr_daily_laborers')->cascadeOnDelete();
            $table->date('work_date');
            $table->boolean('is_present')->default(true);
            $table->decimal('regular_hours', 5, 2)->default(8.00);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->decimal('regular_wage', 10, 2);
            $table->decimal('overtime_wage', 10, 2)->default(0);
            $table->decimal('total_wage', 10, 2);
            $table->string('payment_status', 50)->default('unpaid'); // unpaid, paid
            $table->foreignId('recorded_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->unique(['laborer_id', 'work_date']);
            $table->index(['project_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_labor_muster_rolls');
        Schema::dropIfExists('hr_daily_laborers');
        Schema::dropIfExists('hr_payrolls');
        Schema::dropIfExists('hr_employees');
    }
};

