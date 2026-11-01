<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Booking;
use App\Models\ERP\Customer;
use App\Models\ERP\Unit;
use App\Services\ERP\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function index(Request $request): View
    {
        $companyId = $request->input('company_id', 1);

        $query = Booking::where('company_id', $companyId)->with(['unit.project', 'customer']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('booking_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('unit', function ($u) use ($search) {
                      $u->where('unit_code', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->latest('booking_date')->paginate(15)->withQueryString();

        return view('erp.bookings.index', compact('bookings'));
    }

    public function create(Request $request): View
    {
        $companyId = $request->input('company_id', 1);
        $selectedUnitId = $request->input('unit_id');
        $selectedCustomerId = $request->input('customer_id');

        $customers = Customer::where('company_id', $companyId)->where('status', 'active')->get();
        $units = Unit::where('company_id', $companyId)->whereIn('status', ['available', 'held'])->with('project')->get();

        $selectedUnit = $selectedUnitId ? Unit::find($selectedUnitId) : null;

        return view('erp.bookings.create', compact('customers', 'units', 'selectedUnit', 'selectedCustomerId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'unit_id' => ['required', 'exists:erp_units,id'],
            'customer_id' => ['required', 'exists:erp_customers,id'],
            'contract_price' => ['required', 'numeric', 'gt:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'booking_money' => ['nullable', 'numeric', 'min:0'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'installments_count' => ['nullable', 'integer', 'min:1', 'max:120'],
            'booking_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $unit = Unit::findOrFail($validated['unit_id']);
        $customer = Customer::findOrFail($validated['customer_id']);

        $booking = $this->bookingService->createBooking(
            $unit,
            $customer,
            $validated,
            auth('admin')->user()
        );

        return redirect()->route('erp.bookings.show', $booking)
            ->with('success', 'Booking confirmed and installment schedule generated.');
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'unit.project',
            'unit.building',
            'unit.floor',
            'customer',
            'installments.allocations.payment',
            'payments.collectedBy',
        ]);

        return view('erp.bookings.show', compact('booking'));
    }
}

