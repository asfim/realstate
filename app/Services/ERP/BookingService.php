<?php

namespace App\Services\ERP;

use App\Models\Admin;
use App\Models\ERP\Account;
use App\Models\ERP\Booking;
use App\Models\ERP\Customer;
use App\Models\ERP\Installment;
use App\Models\ERP\Payment;
use App\Models\ERP\PaymentAllocation;
use App\Models\ERP\Unit;
use App\Models\ERP\UnitStatusEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        protected AccountingService $accountingService
    ) {}

    /**
     * Create a booking safely with unit row locking.
     */
    public function createBooking(Unit $unit, Customer $customer, array $payload, ?Admin $actor = null): Booking
    {
        return DB::transaction(function () use ($unit, $customer, $payload, $actor) {
            $lockedUnit = Unit::query()->whereKey($unit->id)->lockForUpdate()->firstOrFail();

            if (!in_array($lockedUnit->status, ['available', 'held'])) {
                throw ValidationException::withMessages([
                    'unit_id' => "This unit ({$lockedUnit->unit_code}) is already {$lockedUnit->status} and cannot be booked.",
                ]);
            }

            $contractPrice = (float) $payload['contract_price'];
            $discount = (float) ($payload['discount_amount'] ?? 0);
            $bookingMoney = (float) ($payload['booking_money'] ?? 0);
            $downPayment = (float) ($payload['down_payment'] ?? 0);
            $netPrice = $contractPrice - $discount;
            $balance = max(0, $netPrice - $bookingMoney - $downPayment);

            $count = Booking::where('company_id', $lockedUnit->company_id)->count() + 1;
            $bookingNo = sprintf('BKG-%s-%04d', date('Ym'), $count);

            $booking = Booking::create([
                'company_id' => $lockedUnit->company_id,
                'unit_id' => $lockedUnit->id,
                'customer_id' => $customer->id,
                'booking_no' => $bookingNo,
                'booking_date' => $payload['booking_date'] ?? now()->toDateString(),
                'contract_price' => $netPrice,
                'booking_money' => $bookingMoney,
                'down_payment' => $downPayment,
                'discount_amount' => $discount,
                'balance_amount' => $balance,
                'status' => 'confirmed',
                'active_token' => Str::random(32),
                'notes' => $payload['notes'] ?? null,
                'created_by' => $actor?->id,
            ]);

            // Update unit status to 'booked' and increment lock_version
            $lockedUnit->status = 'booked';
            $lockedUnit->lock_version += 1;
            $lockedUnit->save();

            // Log unit status transition
            UnitStatusEvent::create([
                'company_id' => $lockedUnit->company_id,
                'unit_id' => $lockedUnit->id,
                'from_status' => 'available',
                'to_status' => 'booked',
                'actor_id' => $actor?->id,
                'notes' => "Unit booked by {$customer->name} (Booking #{$bookingNo})",
                'created_at' => now(),
            ]);

            // Create initial Installment Plan
            $installmentsCount = (int) ($payload['installments_count'] ?? 12);
            $installmentAmount = $installmentsCount > 0 ? round($balance / $installmentsCount, 2) : 0;

            for ($i = 1; $i <= $installmentsCount; $i++) {
                $dueDate = now()->addMonths($i)->startOfMonth()->addDays(9)->toDateString(); // 10th of every month
                Installment::create([
                    'company_id' => $lockedUnit->company_id,
                    'booking_id' => $booking->id,
                    'installment_no' => $i,
                    'title' => "Monthly Installment {$i} of {$installmentsCount}",
                    'due_date' => $dueDate,
                    'amount' => $installmentAmount,
                    'paid_amount' => 0,
                    'status' => 'unpaid',
                ]);
            }

            // Post Sales Journal Entry: Dr Accounts Receivable (1200), Cr Sales Revenue (4010)
            $arAccount = Account::where('company_id', $lockedUnit->company_id)->where('code', '1200')->first();
            $salesAccount = Account::where('company_id', $lockedUnit->company_id)->where('code', '4010')->first();

            if ($arAccount && $salesAccount) {
                $this->accountingService->createJournal([
                    'company_id' => $lockedUnit->company_id,
                    'project_id' => $lockedUnit->project_id,
                    'date' => $booking->booking_date,
                    'voucher_type' => 'sales',
                    'reference' => $booking->booking_no,
                    'narration' => "Sales booking for {$lockedUnit->unit_code} - Customer: {$customer->name}",
                    'posted_by' => $actor?->id,
                ], [
                    [
                        'account_id' => $arAccount->id,
                        'project_id' => $lockedUnit->project_id,
                        'debit' => $netPrice,
                        'credit' => 0,
                        'description' => "Receivable for booking {$booking->booking_no}",
                    ],
                    [
                        'account_id' => $salesAccount->id,
                        'project_id' => $lockedUnit->project_id,
                        'debit' => 0,
                        'credit' => $netPrice,
                        'description' => "Property sales revenue for {$lockedUnit->unit_code}",
                    ],
                ]);
            }

            return $booking;
        });
    }

    /**
     * Record a customer collection payment and post receipt voucher.
     */
    public function recordPayment(Booking $booking, array $payload, ?Admin $actor = null): Payment
    {
        return DB::transaction(function () use ($booking, $payload, $actor) {
            $amount = (float) $payload['amount'];
            $count = Payment::where('company_id', $booking->company_id)->count() + 1;
            $receiptNo = sprintf('RCT-%s-%04d', date('Ym'), $count);

            $payment = Payment::create([
                'company_id' => $booking->company_id,
                'booking_id' => $booking->id,
                'receipt_no' => $receiptNo,
                'amount' => $amount,
                'payment_date' => $payload['payment_date'] ?? now()->toDateString(),
                'method' => $payload['method'] ?? 'bank_transfer',
                'status' => 'verified',
                'bank_name' => $payload['bank_name'] ?? null,
                'cheque_or_trans_no' => $payload['cheque_or_trans_no'] ?? null,
                'collected_by' => $actor?->id,
                'verified_by' => $actor?->id,
                'notes' => $payload['notes'] ?? null,
            ]);

            // Allocate amount against unpaid installments
            $remainingPayment = $amount;
            $unpaidInstallments = $booking->installments()->where('status', '!=', 'paid')->orderBy('installment_no')->get();

            foreach ($unpaidInstallments as $inst) {
                if ($remainingPayment <= 0) break;

                $dueOnInst = (float)$inst->amount - (float)$inst->paid_amount;
                $alloc = min($remainingPayment, $dueOnInst);

                $inst->paid_amount += $alloc;
                $inst->status = ($inst->paid_amount >= $inst->amount) ? 'paid' : 'partially_paid';
                $inst->save();

                PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'installment_id' => $inst->id,
                    'amount' => $alloc,
                ]);

                $remainingPayment -= $alloc;
            }

            // Post Receipt Journal Entry: Dr Cash/Bank (1010/1020), Cr Customer Receivable (1200)
            $assetCode = ($payload['method'] === 'cash') ? '1010' : '1020';
            $cashBankAcc = Account::where('company_id', $booking->company_id)->where('code', $assetCode)->first();
            $arAccount = Account::where('company_id', $booking->company_id)->where('code', '1200')->first();

            if ($cashBankAcc && $arAccount) {
                $this->accountingService->createJournal([
                    'company_id' => $booking->company_id,
                    'project_id' => $booking->unit->project_id,
                    'date' => $payment->payment_date,
                    'voucher_type' => 'receipt',
                    'reference' => $receiptNo,
                    'narration' => "Collection received from {$booking->customer->name} for {$booking->unit->unit_code} ({$receiptNo})",
                    'posted_by' => $actor?->id,
                ], [
                    [
                        'account_id' => $cashBankAcc->id,
                        'project_id' => $booking->unit->project_id,
                        'debit' => $amount,
                        'credit' => 0,
                        'description' => "Funds received via {$payment->method}",
                    ],
                    [
                        'account_id' => $arAccount->id,
                        'project_id' => $booking->unit->project_id,
                        'debit' => 0,
                        'credit' => $amount,
                        'description' => "Receivable credit for receipt {$receiptNo}",
                    ],
                ]);
            }

            return $payment;
        });
    }
}
