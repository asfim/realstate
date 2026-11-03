<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ERP\CrmCustomer;
use App\Models\ERP\CrmLead;
use App\Models\ERP\DevBookingInstallment;
use App\Models\ERP\DevParkingSlot;
use App\Models\ERP\DevProject;
use App\Models\ERP\DevProjectUnit;
use App\Models\ERP\DevSalesBooking;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevSalesBookingController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected DocumentSequenceService $sequenceService,
        protected AuditLogService $auditLog
    ) {
    }

    public function index(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = DevSalesBooking::where('company_id', $currentCompany?->id)
            ->with(['customer', 'project', 'unit', 'salesAgent'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $query->paginate(15)->withQueryString();
        $projects = DevProject::where('company_id', $currentCompany?->id)->get();

        return view('erp.developer.bookings.index', compact('bookings', 'projects'));
    }

    public function create(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $projects = DevProject::where('company_id', $currentCompany?->id)->get();
        $customers = CrmCustomer::where('company_id', $currentCompany?->id)->where('status', 'active')->get();
        $agents = Admin::all();

        $selectedProject = null;
        $availableUnits = collect();
        $availableParking = collect();

        if ($request->filled('project_id')) {
            $selectedProject = DevProject::find($request->project_id);
            if ($selectedProject) {
                $availableUnits = $selectedProject->units()->where('status', 'available')->get();
                $availableParking = $selectedProject->parkingSlots()->where('status', 'available')->get();
            }
        }

        return view('erp.developer.bookings.create', compact('projects', 'customers', 'agents', 'selectedProject', 'availableUnits', 'availableParking'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'project_id'             => 'required|exists:dev_projects,id',
            'unit_id'                => 'required|exists:dev_project_units,id',
            'parking_slot_id'        => 'nullable|exists:dev_parking_slots,id',
            'customer_id'            => 'required|exists:crm_customers,id',
            'lead_id'                => 'nullable|exists:crm_leads,id',
            'sales_agent_id'         => 'nullable|exists:admins,id',
            'agreed_price_sqft'      => 'required|numeric|min:1',
            'agreed_parking_price'   => 'nullable|numeric|min:0',
            'agreed_utility_charge'  => 'nullable|numeric|min:0',
            'downpayment_amount'     => 'required|numeric|min:0',
            'downpayment_due_date'   => 'nullable|date',
            'payment_plan_type'      => 'required|string',
            'total_installments'     => 'required|integer|min:1|max:120',
            'booking_date'           => 'required|date',
            'agreement_date'         => 'nullable|date',
            'expected_handover_date' => 'nullable|date',
            'commission_rate'        => 'nullable|numeric|min:0|max:10',
            'special_conditions'     => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $currentCompany, $currentBranch) {
            $unit = DevProjectUnit::lockForUpdate()->findOrFail($validated['unit_id']);
            if ($unit->status !== 'available') {
                return back()->with('error', "Unit {$unit->unit_number} is no longer available.");
            }

            $agreedUnitPrice = $unit->gross_area_sqft * $validated['agreed_price_sqft'];
            $parkingPrice = $validated['agreed_parking_price'] ?? 0;
            $utilityCharge = $validated['agreed_utility_charge'] ?? 0;
            $totalContract = $agreedUnitPrice + $parkingPrice + $utilityCharge;

            $bookingCode = $this->sequenceService->getNextNumber('booking', $currentCompany->id, $currentBranch?->id);

            $commRate = $validated['commission_rate'] ?? 0;
            $commAmount = ($totalContract * $commRate) / 100;

            $booking = DevSalesBooking::create([
                'company_id'             => $currentCompany->id,
                'branch_id'              => $currentBranch?->id,
                'booking_code'           => $bookingCode,
                'customer_id'            => $validated['customer_id'],
                'lead_id'                => $validated['lead_id'] ?? null,
                'project_id'             => $validated['project_id'],
                'unit_id'                => $unit->id,
                'parking_slot_id'        => $validated['parking_slot_id'] ?? null,
                'sales_agent_id'         => $validated['sales_agent_id'] ?? auth('admin')->id(),
                'agreed_price_sqft'      => $validated['agreed_price_sqft'],
                'agreed_unit_price'      => $agreedUnitPrice,
                'agreed_parking_price'   => $parkingPrice,
                'agreed_utility_charge'  => $utilityCharge,
                'total_contract_amount'  => $totalContract,
                'downpayment_amount'     => $validated['downpayment_amount'],
                'downpayment_due_date'   => $validated['downpayment_due_date'] ?? $validated['booking_date'],
                'payment_plan_type'      => $validated['payment_plan_type'],
                'total_installments'     => $validated['total_installments'],
                'booking_date'           => $validated['booking_date'],
                'agreement_date'         => $validated['agreement_date'] ?? $validated['booking_date'],
                'expected_handover_date' => $validated['expected_handover_date'] ?? null,
                'commission_rate'        => $commRate,
                'commission_amount'      => $commAmount,
                'status'                 => 'active',
                'special_conditions'     => $validated['special_conditions'] ?? null,
                'created_by'             => auth('admin')->id(),
            ]);

            // Update Unit Status
            $unit->update(['status' => 'booked']);

            // Update Parking Status
            if (!empty($validated['parking_slot_id'])) {
                DevParkingSlot::where('id', $validated['parking_slot_id'])->update([
                    'status'           => 'allocated',
                    'assigned_unit_id' => $unit->id,
                ]);
            }

            // Generate Payment Schedule Milestones
            $this->generateMilestoneInstallments($booking);

            $this->auditLog->log('sales.booking_created', $booking, null, $booking->toArray(), $currentCompany->id, $booking->branch_id);

            return redirect()->route('erp.dev-bookings.show', $booking)->with('success', "Booking Contract {$booking->booking_code} generated.");
        });
    }

    public function show(DevSalesBooking $devBooking)
    {
        $devBooking->load(['customer', 'project', 'unit', 'parkingSlot', 'salesAgent', 'installments']);
        return view('erp.developer.bookings.show', compact('devBooking'));
    }

    public function recordPayment(Request $request, DevBookingInstallment $installment)
    {
        $validated = $request->validate([
            'paid_amount'       => 'required|numeric|min:1',
            'paid_date'         => 'required|date',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        $newPaid = $installment->paid_amount + $validated['paid_amount'];
        $balance = max(0, $installment->due_amount - $newPaid);
        $status = $balance <= 0 ? 'paid' : 'partially_paid';

        $installment->update([
            'paid_amount'       => $newPaid,
            'balance_due'       => $balance,
            'status'            => $status,
            'paid_date'         => $validated['paid_date'],
            'payment_reference' => $validated['payment_reference'],
        ]);

        return back()->with('success', 'Installment payment recorded.');
    }

    public function cancel(Request $request, DevSalesBooking $devBooking)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        $devBooking->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        $devBooking->unit->update(['status' => 'available']);

        if ($devBooking->parkingSlot) {
            $devBooking->parkingSlot->update(['status' => 'available', 'assigned_unit_id' => null]);
        }

        $this->auditLog->log('sales.booking_cancelled', $devBooking, null, $devBooking->toArray(), $devBooking->company_id, $devBooking->branch_id);

        return back()->with('success', 'Booking has been cancelled and unit released back to inventory.');
    }

    public function edit(DevSalesBooking $devBooking)
    {
        $agents = Admin::all();
        return view('erp.developer.bookings.edit', compact('devBooking', 'agents'));
    }

    public function update(Request $request, DevSalesBooking $devBooking)
    {
        $validated = $request->validate([
            'sales_agent_id'         => 'nullable|exists:admins,id',
            'expected_handover_date' => 'nullable|date',
            'special_conditions'     => 'nullable|string',
            'notes'                  => 'nullable|string',
        ]);

        $old = $devBooking->toArray();
        $devBooking->update($validated);
        $this->auditLog->log('sales.booking_updated', $devBooking, $old, $devBooking->toArray(), $devBooking->company_id, $devBooking->branch_id);

        return redirect()->route('erp.dev-bookings.show', $devBooking)->with('success', 'Booking details updated.');
    }

    public function destroy(DevSalesBooking $devBooking)
    {
        if ($devBooking->status === 'active') {
            return back()->with('error', 'Please cancel the booking first before deleting it.');
        }

        $old = $devBooking->toArray();
        $devBooking->installments()->delete();
        $devBooking->delete();
        $this->auditLog->log('sales.booking_deleted', null, $old, null, $devBooking->company_id, $devBooking->branch_id);

        return redirect()->route('erp.dev-bookings.index')->with('success', 'Booking record deleted.');
    }

    /**
     * Auto-generate installment milestones based on plan.
     */
    protected function generateMilestoneInstallments(DevSalesBooking $booking): void
    {
        $startDate = Carbon::parse($booking->booking_date);

        // 1. Downpayment milestone
        if ($booking->downpayment_amount > 0) {
            DevBookingInstallment::create([
                'booking_id'         => $booking->id,
                'milestone_name'     => 'Booking & Downpayment',
                'installment_number' => 1,
                'due_date'           => $booking->downpayment_due_date ?? $startDate,
                'due_amount'         => $booking->downpayment_amount,
                'paid_amount'        => 0,
                'balance_due'        => $booking->downpayment_amount,
                'status'             => 'pending',
            ]);
        }

        $balanceToInstall = $booking->total_contract_amount - $booking->downpayment_amount;
        $numInstallments = max(1, $booking->total_installments);
        $installmentAmount = round($balanceToInstall / $numInstallments, 2);

        $currentDue = $startDate->copy();

        for ($i = 1; $i <= $numInstallments; $i++) {
            $currentDue->addMonth();
            $dueAmt = ($i === $numInstallments) ? ($balanceToInstall - ($installmentAmount * ($numInstallments - 1))) : $installmentAmount;

            DevBookingInstallment::create([
                'booking_id'         => $booking->id,
                'milestone_name'     => "Installment #{$i} of {$numInstallments}",
                'installment_number' => ($booking->downpayment_amount > 0 ? $i + 1 : $i),
                'due_date'           => $currentDue->copy(),
                'due_amount'         => $dueAmt,
                'paid_amount'        => 0,
                'balance_due'        => $dueAmt,
                'status'             => 'pending',
            ]);
        }
    }
}
