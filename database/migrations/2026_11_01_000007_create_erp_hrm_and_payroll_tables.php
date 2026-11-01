<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('erp_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('erp_branches')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('erp_departments')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('employee_code', 50);
            $table->string('name', 255);
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->string('designation', 100);
            $table->date('joining_date');
            
            // Salary Structure
            $table->decimal('basic_salary', 18, 2)->default(0);
            $table->decimal('house_rent', 18, 2)->default(0);
            $table->decimal('medical_allowance', 18, 2)->default(0);
            $table->decimal('other_allowance', 18, 2)->default(0);
            $table->decimal('tax_deduction', 18, 2)->default(0);
            $table->decimal('provident_fund', 18, 2)->default(0);
            $table->decimal('gross_salary', 18, 2)->default(0);

            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_no', 100)->nullable();
            $table->string('status', 32)->default('active'); // active, on_leave, resigned, terminated
            $table->timestamps();

            $table->unique(['company_id', 'employee_code']);
            $table->index(['company_id', 'status']);
        });

        Schema::create('erp_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('erp_employees')->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('status', 32)->default('present'); // present, late, absent, on_leave
            $table->string('remarks', 255)->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
            $table->index(['company_id', 'date']);
        });

        Schema::create('erp_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('erp_employees')->cascadeOnDelete();
            $table->string('leave_type', 32); // casual, sick, annual, unpaid
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('total_days')->default(1);
            $table->text('reason')->nullable();
            $table->string('status', 32)->default('pending'); // pending, approved, rejected
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'status']);
        });

        Schema::create('erp_payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
            $table->string('title', 100);
            $table->decimal('total_gross', 18, 2)->default(0);
            $table->decimal('total_deductions', 18, 2)->default(0);
            $table->decimal('total_net', 18, 2)->default(0);
            $table->string('status', 32)->default('draft'); // draft, approved, paid
            $table->foreignId('journal_id')->nullable()->constrained('erp_journals')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'year', 'month']);
        });

        Schema::create('erp_payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained('erp_payroll_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('erp_employees')->restrictOnDelete();
            $table->decimal('basic_salary', 18, 2);
            $table->decimal('allowances', 18, 2)->default(0);
            $table->decimal('deductions', 18, 2)->default(0);
            $table->decimal('net_salary', 18, 2);
            $table->string('payment_status', 32)->default('pending'); // pending, paid
            $table->string('payment_method', 32)->default('bank_transfer'); // bank_transfer, cash, cheque
            $table->timestamps();
        });

        Schema::create('erp_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('erp_employees')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('erp_bookings')->cascadeOnDelete();
            $table->decimal('booking_amount', 18, 2);
            $table->decimal('rate_percent', 5, 2)->default(1.00);
            $table->decimal('commission_amount', 18, 2);
            $table->string('status', 32)->default('pending'); // pending, approved, paid
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_commissions');
        Schema::dropIfExists('erp_payroll_items');
        Schema::dropIfExists('erp_payroll_runs');
        Schema::dropIfExists('erp_leave_requests');
        Schema::dropIfExists('erp_attendances');
        Schema::dropIfExists('erp_employees');
    }
};

