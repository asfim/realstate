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
        Schema::create('erp_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('currency', 3)->default('BDT');
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('erp_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50);
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('erp_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'name']);
        });

        Schema::create('erp_staff_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('erp_branches')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('erp_departments')->nullOnDelete();
            $table->string('designation')->nullable();
            $table->string('role_context')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'admin_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_staff_assignments');
        Schema::dropIfExists('erp_departments');
        Schema::dropIfExists('erp_branches');
        Schema::dropIfExists('erp_companies');
    }
};
