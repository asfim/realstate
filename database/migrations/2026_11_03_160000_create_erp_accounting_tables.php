<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Chart of Accounts (COA)
        Schema::create('acc_chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('acc_chart_of_accounts')->nullOnDelete();
            $table->string('account_code', 50); // e.g. 1010, 1020, 2010, 4010, 5010
            $table->string('account_name');
            $table->string('account_type', 50); // asset, liability, equity, revenue, expense
            $table->string('normal_balance', 10); // debit, credit
            $table->boolean('is_reconciled')->default(false);
            $table->boolean('is_system_locked')->default(false);
            $table->string('status', 50)->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'account_code']);
            $table->index(['company_id', 'account_type']);
        });

        // 2. Journal Vouchers Header
        Schema::create('acc_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('fiscal_year_id')->nullable()->constrained('fiscal_years')->nullOnDelete();
            $table->string('voucher_code', 50)->unique();
            $table->string('voucher_type', 30)->default('JV'); // JV, CPV, CRV, BPV, BRV
            $table->date('entry_date');
            $table->decimal('total_amount', 18, 2);
            $table->text('narration');
            $table->string('reference_type', 100)->nullable(); // DevSalesBooking, ConPurchaseOrder, AgencyDeal
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('status', 50)->default('posted'); // draft, posted, reversed
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'entry_date']);
            $table->index(['company_id', 'status']);
        });

        // 3. Double-Entry Journal Lines (Debit / Credit)
        Schema::create('acc_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('acc_journals')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('acc_chart_of_accounts')->cascadeOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->string('entry_narration')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'cost_center_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_journal_entries');
        Schema::dropIfExists('acc_journals');
        Schema::dropIfExists('acc_chart_of_accounts');
    }
};
