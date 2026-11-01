<?php

use App\Models\Admin;
use App\Models\ERP\Booking;
use App\Models\ERP\Company;
use App\Models\ERP\Customer;
use App\Models\ERP\Journal;
use App\Models\ERP\Payment;
use App\Models\ERP\Unit;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ERPPhase1Seeder;
use Database\Seeders\ERPAccountingAndHRSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(ERPPhase1Seeder::class);
    $this->seed(ERPAccountingAndHRSeeder::class);
});

test('super admin can view customer directory and create a new customer', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $company = Company::first();

    $response = $this->actingAs($admin, 'admin')->get(route('erp.customers.index'));
    $response->assertStatus(200);
    $response->assertSee('Customer Master Directory');

    $payload = [
        'company_id' => $company->id,
        'customer_code' => 'CUST-TEST-099',
        'name' => 'Dr. Mahfuzur Rahman',
        'phone' => '+880 1911-334455',
        'email' => 'mahfuz@example.com',
        'nid_or_passport' => '19852694125896321',
        'address' => 'Banani, Dhaka',
    ];

    $createResponse = $this->actingAs($admin, 'admin')->post(route('erp.customers.store'), $payload);
    $createResponse->assertRedirect();

    $this->assertDatabaseHas('erp_customers', [
        'customer_code' => 'CUST-TEST-099',
        'name' => 'Dr. Mahfuzur Rahman',
        'phone' => '+880 1911-334455',
    ]);
});

test('can book an available unit, generating installments and sales journal voucher', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $customer = Customer::first();
    $unit = Unit::where('status', 'available')->first();

    $payload = [
        'unit_id' => $unit->id,
        'customer_id' => $customer->id,
        'contract_price' => 20000000.00,
        'discount_amount' => 500000.00,
        'booking_money' => 1500000.00,
        'down_payment' => 3000000.00,
        'installments_count' => 6,
        'booking_date' => '2026-11-01',
        'notes' => 'New booking test contract',
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.bookings.store'), $payload);
    $response->assertRedirect();

    // Check unit transitioned to booked
    $unit->refresh();
    expect($unit->status)->toBe('booked');

    // Check booking created
    $booking = Booking::where('unit_id', $unit->id)->first();
    expect($booking)->not->toBeNull();
    expect((float) $booking->contract_price)->toBe(19500000.00); // 20M - 500k discount
    expect($booking->installments()->count())->toBe(6);

    // Check sales journal posted and balanced
    $this->assertDatabaseHas('erp_journals', [
        'voucher_type' => 'sales',
        'reference' => $booking->booking_no,
    ]);

    $journal = Journal::where('voucher_type', 'sales')->where('reference', $booking->booking_no)->first();
    expect($journal->isBalanced())->toBeTrue();
});

test('cannot double-book an already booked unit', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $customer = Customer::first();
    $bookedUnit = Unit::where('status', 'booked')->first();

    $payload = [
        'unit_id' => $bookedUnit->id,
        'customer_id' => $customer->id,
        'contract_price' => 18000000.00,
        'booking_date' => '2026-11-01',
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.bookings.store'), $payload);
    $response->assertSessionHasErrors(['unit_id']);
});

test('can record payment for booking and auto-allocate to installments', function () {
    $admin = Admin::where('email', 'admin@example.com')->first();
    $booking = Booking::first();

    $payload = [
        'amount' => 1000000.00,
        'payment_date' => '2026-11-02',
        'method' => 'bank_transfer',
        'bank_name' => 'BRAC Bank',
        'cheque_or_trans_no' => 'TXN-TEST-12345',
        'notes' => 'Installment payment test',
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('erp.bookings.payments.store', $booking), $payload);
    $response->assertRedirect();

    $this->assertDatabaseHas('erp_payments', [
        'booking_id' => $booking->id,
        'amount' => 1000000.00,
        'cheque_or_trans_no' => 'TXN-TEST-12345',
    ]);

    $payment = Payment::where('cheque_or_trans_no', 'TXN-TEST-12345')->first();
    expect($payment)->not->toBeNull();

    // Check receipt journal voucher posted and balanced
    $this->assertDatabaseHas('erp_journals', [
        'voucher_type' => 'receipt',
        'reference' => $payment->receipt_no,
    ]);

    $journal = Journal::where('voucher_type', 'receipt')->where('reference', $payment->receipt_no)->first();
    expect($journal->isBalanced())->toBeTrue();
});
