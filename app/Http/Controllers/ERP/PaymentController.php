<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Booking;
use App\Models\ERP\Payment;
use App\Services\ERP\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function index(Request $request): View
    {
        $companyId = $request->input('company_id', 1);

        $query = Payment::where('company_id', $companyId)->with(['booking.unit', 'booking.customer', 'collectedBy']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                  ->orWhere('cheque_or_trans_no', 'like', "%{$search}%")
                  ->orWhereHas('booking.customer', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest('payment_date')->paginate(15)->withQueryString();

        return view('erp.payments.index', compact('payments'));
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'method' => ['required', 'in:cash,bank_transfer,cheque,mfs_bkash,mfs_nagad,card'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'cheque_or_trans_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment = $this->bookingService->recordPayment(
            $booking,
            $validated,
            auth('admin')->user()
        );

        return redirect()->route('erp.payments.show', $payment)
            ->with('success', "Payment receipt #{$payment->receipt_no} generated and ledger updated.");
    }

    public function show(Payment $payment): View
    {
        $payment->load([
            'company',
            'booking.customer',
            'booking.unit.project',
            'allocations.installment',
            'collectedBy',
        ]);

        return view('erp.payments.show', compact('payment'));
    }
}

