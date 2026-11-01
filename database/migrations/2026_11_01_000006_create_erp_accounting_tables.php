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
        Schema::create('erp_fiscal_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->string('name', 100); // e.g. FY 2025-2026
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->unique(['company_id', 'name']);
        });

        Schema::create('erp_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->string('code', 50); // e.g. 1010, 1020, 2010, 4010, 5010
            $table->string('name', 255);
            $table->string('type', 32); // asset, liability, equity, revenue, expense
            $table->foreignId('parent_id')->nullable()->constrained('erp_accounts')->nullOnDelete();
            $table->text('description')->nullable();
            $table->decimal('opening_balance', 18, 2)->default(0);
            $table->decimal('current_balance', 18, 2)->default(0);
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'type']);
        });

        Schema::create('erp_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('fiscal_period_id')->nullable()->constrained('erp_fiscal_periods')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('erp_projects')->nullOnDelete();
            $table->string('journal_no', 50);
            $table->date('date');
            $table->string('voucher_type', 32)->default('journal'); // journal, receipt, payment, sales, contra
            $table->string('reference', 100)->nullable();
            $table->text('narration')->nullable();
            $table->decimal('total_debit', 18, 2)->default(0);
            $table->decimal('total_credit', 18, 2)->default(0);
            $table->string('status', 32)->default('posted'); // draft, posted, reversed
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('reversed_journal_id')->nullable()->constrained('erp_journals')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'journal_no']);
            $table->index(['company_id', 'date', 'voucher_type']);
        });

        Schema::create('erp_journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('erp_journals')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('erp_accounts')->restrictOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('erp_projects')->nullOnDelete();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['journal_id', 'account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_journal_lines');
        Schema::dropIfExists('erp_journals');
        Schema::dropIfExists('erp_accounts');
        Schema::dropIfExists('erp_fiscal_periods');
    }
};

