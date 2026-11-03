@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.dev-bookings.index') }}" class="text-decoration-none">Sales Bookings</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $devBooking->booking_code }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-dark">Contract {{ $devBooking->booking_code }}</h4>
            @php
                $statusBadges = [
                    'draft' => 'bg-secondary',
                    'active' => 'bg-primary',
                    'registered_deed' => 'bg-info',
                    'handed_over' => 'bg-success',
                    'cancelled' => 'bg-danger',
                ];
            @endphp
            <span class="badge {{ $statusBadges[$devBooking->status] ?? 'bg-secondary' }} px-3 py-1">
                {{ strtoupper(str_replace('_', ' ', $devBooking->status)) }}
            </span>
        </div>
        <p class="text-muted small mb-0">Booking Date: {{ \Carbon\Carbon::parse($devBooking->booking_date)->format('F d, Y') }} | Created By: {{ $devBooking->salesAgent->name ?? 'System' }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.dev-bookings.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Dossier
        </button>
        @if($devBooking->status === 'active')
            <button type="button" class="btn btn-outline-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#cancelBookingModal">
                <i class="bi bi-x-circle me-1"></i> Cancel Contract
            </button>
        @endif
    </div>
</div>

@php
    $totalPaid = $devBooking->installments->sum('paid_amount');
    $totalDue = $devBooking->total_contract_amount;
    $balanceDue = max(0, $totalDue - $totalPaid);
    $percentPaid = $totalDue > 0 ? round(($totalPaid / $totalDue) * 100, 1) : 0;
@endphp

<!-- Financial Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Total Contract Value</div>
            <div class="fs-4 fw-bold text-dark mt-1">৳ {{ number_format($devBooking->total_contract_amount, 2) }}</div>
            <div class="text-muted extra-small mt-1">Base: ৳{{ number_format($devBooking->agreed_price_sqft) }}/sft</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Total Collected</div>
            <div class="fs-4 fw-bold text-success mt-1">৳ {{ number_format($totalPaid, 2) }}</div>
            <div class="text-success extra-small mt-1 fw-semibold">{{ $percentPaid }}% Settled</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-danger h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Outstanding Balance</div>
            <div class="fs-4 fw-bold text-danger mt-1">৳ {{ number_format($balanceDue, 2) }}</div>
            <div class="text-muted extra-small mt-1">{{ count($devBooking->installments->where('status', '!=', 'paid')) }} pending installments</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Handover Target</div>
            <div class="fs-5 fw-bold text-dark mt-1">
                {{ $devBooking->expected_handover_date ? \Carbon\Carbon::parse($devBooking->expected_handover_date)->format('M Y') : 'Not Scheduled' }}
            </div>
            <div class="text-muted extra-small mt-1">Plan: {{ ucfirst($devBooking->payment_plan_type) }}</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Customer / Buyer Dossier -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-person-fill text-erp-gold me-2"></i> Buyer / Purchaser Information
            </h6>
            @if($devBooking->customer)
                <div class="row g-2 small">
                    <div class="col-sm-4 text-muted">Customer Name:</div>
                    <div class="col-sm-8 fw-bold text-dark">{{ $devBooking->customer->name }} ({{ $devBooking->customer->customer_code }})</div>

                    <div class="col-sm-4 text-muted">Phone Number:</div>
                    <div class="col-sm-8 text-dark">{{ $devBooking->customer->phone }}</div>

                    <div class="col-sm-4 text-muted">Email Address:</div>
                    <div class="col-sm-8 text-dark">{{ $devBooking->customer->email ?? '—' }}</div>

                    <div class="col-sm-4 text-muted">National ID / NID:</div>
                    <div class="col-sm-8 text-dark">{{ $devBooking->customer->nid_or_passport ?? '—' }}</div>

                    <div class="col-sm-4 text-muted">Present Address:</div>
                    <div class="col-sm-8 text-dark">{{ $devBooking->customer->present_address ?? '—' }}</div>

                    <div class="col-sm-4 text-muted">Permanent Address:</div>
                    <div class="col-sm-8 text-dark">{{ $devBooking->customer->permanent_address ?? '—' }}</div>
                </div>
            @else
                <div class="text-muted">Customer record unavailable.</div>
            @endif
        </div>
    </div>

    <!-- Unit & Project Specs -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-building-check text-erp-gold me-2"></i> Unit & Allotment Specifications
            </h6>
            <div class="row g-2 small">
                <div class="col-sm-4 text-muted">Project Name:</div>
                <div class="col-sm-8 fw-bold text-dark">
                    <a href="{{ route('erp.dev-projects.show', $devBooking->project_id) }}" class="text-decoration-none">
                        {{ $devBooking->project->name ?? '—' }}
                    </a>
                </div>

                <div class="col-sm-4 text-muted">Location:</div>
                <div class="col-sm-8 text-dark">{{ $devBooking->project->location ?? '—' }}</div>

                <div class="col-sm-4 text-muted">Allotted Unit:</div>
                <div class="col-sm-8 fw-bold text-dark">
                    Unit {{ $devBooking->unit->unit_number ?? '—' }} (Floor {{ $devBooking->unit->floor_number ?? '—' }})
                </div>

                <div class="col-sm-4 text-muted">Gross Area:</div>
                <div class="col-sm-8 text-dark">{{ number_format($devBooking->unit->gross_area_sqft ?? 0) }} sft ({{ $devBooking->unit->bedrooms ?? 0 }} Bed, {{ $devBooking->unit->bathrooms ?? 0 }} Bath)</div>

                <div class="col-sm-4 text-muted">Parking Bay:</div>
                <div class="col-sm-8 text-dark">
                    @if($devBooking->parkingSlot)
                        Slot {{ $devBooking->parkingSlot->slot_number }} ({{ $devBooking->parkingSlot->basement_level }})
                    @else
                        <span class="text-muted">None Allocated</span>
                    @endif
                </div>

                <div class="col-sm-4 text-muted">Pricing Breakdown:</div>
                <div class="col-sm-8 text-dark">
                    Unit: ৳ {{ number_format($devBooking->agreed_unit_price, 2) }} <br>
                    Parking: ৳ {{ number_format($devBooking->agreed_parking_price, 2) }} <br>
                    Utility: ৳ {{ number_format($devBooking->agreed_utility_charge, 2) }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Milestones Payment Schedule -->
<div class="card card-custom mb-4">
    <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-calendar2-check-fill text-erp-gold me-2"></i> Payment Milestones Schedule
            </h6>
            <span class="text-muted extra-small">Track installments, downpayments, and record customer pay-ins.</span>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark border">
                Total Milestones: {{ count($devBooking->installments) }}
            </span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Milestone Name</th>
                    <th>Due Date</th>
                    <th>Due Amount</th>
                    <th>Paid Amount</th>
                    <th>Balance Due</th>
                    <th>Status</th>
                    <th>Payment Reference</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($devBooking->installments as $idx => $inst)
                    @php
                        $instStatusBadges = [
                            'pending' => 'bg-secondary',
                            'partially_paid' => 'bg-warning text-dark',
                            'paid' => 'bg-success',
                            'overdue' => 'bg-danger',
                        ];
                    @endphp
                    <tr>
                        <td class="ps-3 fw-bold">{{ $inst->installment_number ?? ($idx + 1) }}</td>
                        <td class="fw-semibold text-dark">{{ $inst->milestone_name }}</td>
                        <td class="small">{{ \Carbon\Carbon::parse($inst->due_date)->format('M d, Y') }}</td>
                        <td class="fw-semibold">৳ {{ number_format($inst->due_amount, 2) }}</td>
                        <td class="text-success fw-bold">৳ {{ number_format($inst->paid_amount, 2) }}</td>
                        <td class="fw-semibold {{ $inst->balance_due > 0 ? 'text-danger' : 'text-muted' }}">
                            ৳ {{ number_format($inst->balance_due, 2) }}
                        </td>
                        <td>
                            <span class="badge {{ $instStatusBadges[$inst->status] ?? 'bg-secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $inst->status)) }}
                            </span>
                        </td>
                        <td class="extra-small text-muted">
                            {{ $inst->payment_reference ?? '—' }}
                            @if($inst->paid_date)
                                <div class="text-muted">{{ \Carbon\Carbon::parse($inst->paid_date)->format('M d, Y') }}</div>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            @if($inst->status !== 'paid' && $devBooking->status === 'active')
                                <button type="button" class="btn btn-sm btn-outline-success px-2 py-1"
                                        onclick="openPaymentModal({{ $inst->id }}, '{{ addslashes($inst->milestone_name) }}', {{ $inst->balance_due }})">
                                    <i class="bi bi-wallet2 me-1"></i> Pay
                                </button>
                            @else
                                <span class="badge bg-light text-muted border">Completed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No milestone schedule defined for this booking contract.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($devBooking->special_conditions)
    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold text-dark mb-2">Special Conditions & Legal Agreement Terms</h6>
        <p class="text-muted mb-0 small" style="white-space: pre-line;">{{ $devBooking->special_conditions }}</p>
    </div>
@endif

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="paymentForm" method="POST" action="">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="paymentModalTitle">Record Milestone Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Amount (BDT) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="paid_amount" id="modalPaidAmount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="paid_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Reference / Cheque / Bank Txn #</label>
                        <input type="text" name="payment_reference" class="form-control" placeholder="e.g. City Bank Cheque #983472">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold px-4">Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Contract Modal -->
@if($devBooking->status === 'active')
<div class="modal fade" id="cancelBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('erp.dev-bookings.cancel', $devBooking) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">Cancel Sales Booking Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">
                        Cancelling this contract will mark the booking as <strong>Cancelled</strong> and restore the associated unit (Unit {{ $devBooking->unit->unit_number ?? '' }}) and parking bay back to <strong>Available</strong> status in developer inventory.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Cancellation <span class="text-danger">*</span></label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" placeholder="Buyer requested cancellation, refund terms, etc." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Go Back</button>
                    <button type="submit" class="btn btn-danger px-4">Confirm Contract Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function openPaymentModal(installmentId, milestoneName, balanceDue) {
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const form = document.getElementById('paymentForm');
    const title = document.getElementById('paymentModalTitle');
    const amtInput = document.getElementById('modalPaidAmount');

    form.action = `/erp/developer/installments/${installmentId}/pay`;
    title.innerText = `Record Payment: ${milestoneName}`;
    amtInput.value = balanceDue;
    amtInput.max = balanceDue;

    modal.show();
}
</script>
@endsection
