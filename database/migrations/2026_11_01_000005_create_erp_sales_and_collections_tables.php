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
        Schema::create('erp_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->string('customer_code', 50);
            $table->string('name', 255);
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->string('nid_or_passport', 50)->nullable();
            $table->string('tin', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact', 100)->nullable();
            $table->string('status', 32)->default('active'); // active, inactive
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'customer_code']);
            $table->index(['company_id', 'phone']);
        });

        Schema::create('erp_unit_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('erp_units')->restrictOnDelete();
            $table->foreignId('customer_id')->constrained('erp_customers')->restrictOnDelete();
            $table->dateTime('held_until');
            $table->decimal('token_deposit', 18, 2)->default(0);
            $table->string('status', 32)->default('active'); // active, expired, converted, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('erp_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('erp_units')->restrictOnDelete();
            $table->foreignId('customer_id')->constrained('erp_customers')->restrictOnDelete();
            $table->string('booking_no', 50);
            $table->date('booking_date');
            $table->decimal('contract_price', 18, 2);
            $table->decimal('booking_money', 18, 2)->default(0);
            $table->decimal('down_payment', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('balance_amount', 18, 2);
            $table->string('status', 32)->default('confirmed'); // pending, confirmed, completed, cancelled
            $table->string('active_token')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'booking_no']);
            $table->index(['unit_id', 'status']);
        });

        Schema::create('erp_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('booking_id')->constrained('erp_bookings')->cascadeOnDelete();
            $table->unsignedInteger('installment_no');
            $table->string('title', 100);
            $table->date('due_date');
            $table->decimal('amount', 18, 2);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->string('status', 32)->default('unpaid'); // unpaid, partially_paid, paid, overdue
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['booking_id', 'installment_no']);
            $table->index(['company_id', 'due_date', 'status']);
        });

        Schema::create('erp_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('erp_companies')->restrictOnDelete();
            $table->foreignId('booking_id')->constrained('erp_bookings')->restrictOnDelete();
            $table->string('receipt_no', 50);
            $table->decimal('amount', 18, 2);
            $table->date('payment_date');
            $table->string('method', 32)->default('bank_transfer'); // cash, bank_transfer, cheque, mfs_bkash, mfs_nagad, card
            $table->string('status', 32)->default('verified'); // pending, verified, rejected, reversed
            $table->string('bank_name', 100)->nullable();
            $table->string('cheque_or_trans_no', 100)->nullable();
            $table->string('idempotency_key', 100)->nullable()->unique();
            $table->foreignId('collected_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'receipt_no']);
        });

        Schema::create('erp_payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('erp_payments')->cascadeOnDelete();
            $table->foreignId('installment_id')->constrained('erp_installments')->cascadeOnDelete();
            $table->decimal('amount', 18, 2);
            $table->timestamps();

            $table->unique(['payment_id', 'installment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_payment_allocations');
        Schema::dropIfExists('erp_payments');
        Schema::dropIfExists('erp_installments');
        Schema::dropIfExists('erp_bookings');
        Schema::dropIfExists('erp_unit_holds');
        Schema::dropIfExists('erp_customers');
    }
};
