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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('legal_name');
            $table->string('trade_name')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('bin')->nullable();
            $table->string('tin')->nullable();
            $table->string('base_currency', 10)->default('BDT');
            $table->string('fiscal_year_start', 10)->default('07-01');
            $table->string('timezone', 50)->default('Asia/Dhaka');
            $table->string('logo_path')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->foreignId('manager_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->boolean('is_head_office')->default(false);
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('name');
            $table->string('grade', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('code', 50);
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 20)->default('active'); // active, closed
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        Schema::create('document_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('fiscal_year_id')->nullable()->constrained('fiscal_years')->nullOnDelete();
            $table->string('document_type', 50); // quotation, booking, contract, invoice, receipt, po, grn, bill, payment, journal, payroll
            $table->string('prefix', 20);
            $table->unsignedBigInteger('next_number')->default(1);
            $table->unsignedInteger('padding')->default(5);
            $table->timestamps();

            $table->unique(['company_id', 'branch_id', 'fiscal_year_id', 'document_type'], 'doc_seq_unique');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('actor_type')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('action', 100);
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('before_json')->nullable();
            $table->json('after_json')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_id', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['actor_type', 'actor_id']);
        });

        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('module', 50); // sales, procurement, accounting, payroll
            $table->string('action', 50); // discount, po_approval, journal_posting, salary_approval
            $table->decimal('threshold_amount', 18, 2)->nullable();
            $table->string('currency', 10)->default('BDT');
            $table->unsignedInteger('sequence')->default(1);
            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('module', 50);
            $table->string('record_type');
            $table->unsignedBigInteger('record_id');
            $table->foreignId('requested_by')->constrained('admins')->cascadeOnDelete();
            $table->string('status', 20)->default('pending'); // pending, approved, rejected
            $table->unsignedInteger('current_step')->default(1);
            $table->json('payload_json')->nullable();
            $table->timestamps();

            $table->index(['record_type', 'record_id']);
        });

        Schema::create('approval_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('approval_requests')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('admins')->cascadeOnDelete();
            $table->string('decision', 20); // approved, rejected
            $table->text('remarks')->nullable();
            $table->timestamp('acted_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_actions');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_workflows');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('document_sequences');
        Schema::dropIfExists('fiscal_years');
        Schema::dropIfExists('cost_centers');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('companies');
    }
};

