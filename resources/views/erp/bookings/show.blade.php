@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-secondary bg-opacity-10 text-dark border">{{ $booking->booking_no }}</span>
            <span class="badge bg-success">{{ ucfirst($booking->status) }}</span>
        </div>
        <h3 class="fw-bold text-dark mb-0">Booking #{{ $booking->booking_no }}</h3>
        <div class="text-muted small mt-1">
            Customer: <a href="{{ route('erp.customers.show', $booking->customer) }}" class="fw-bold text-decoration-none">{{ $booking->customer->name }}</a>
            &bull; Unit: <a href="{{ route('erp.units.show', $booking->unit) }}" class="fw-bold text-decoration-none">{{ $booking->unit->unit_code }}</a>
            ({{ $booking->unit->project->name }})
        </div>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
            <i class="bi bi-cash-stack me-1"></i> Collect Payment
        </button>
        <a href="{{ route('erp.accounting.customer-ledger', ['customer_id' => $booking->customer_id]) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-journal-text me-1"></i> Subledger
        </a>
        <a href="{{ route('erp.bookings.index') }}" class="btn btn-light btn-sm border">
            Back to Bookings
        </a>
    </div>
</div>

{{-- Financial Summary KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-dark">
            <div class="text-muted small text-uppercase fw-semibold">Contract Price</div>
            <h4 class="mb-0 fw-bold mt-1 text-dark">৳ {{ number_format($booking->contract_price, 2) }}</h4>
            <div class="text-muted text-xs">Agreed sales value</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-success">
            <div class="text-muted small text-uppercase fw-semibold">Total Collected</div>
            <h4 class="mb-0 fw-bold mt-1 text-success">৳ {{ number_format($booking->total_paid, 2) }}</h4>
            <div class="text-muted text-xs">{{ $booking->contract_price > 0 ? round(($booking->total_paid / $booking->contract_price) * 100, 1) : 0 }}% collected</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-danger">
            <div class="text-muted small text-uppercase fw-semibold">Remaining Due</div>
            <h4 class="mb-0 fw-bold mt-1 text-danger">৳ {{ number_format($booking->due_amount, 2) }}</h4>
            <div class="text-muted text-xs">Outstanding receivable</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-primary">
            <div class="text-muted small text-uppercase fw-semibold">Installment Tenure</div>
            <h4 class="mb-0 fw-bold mt-1 text-primary">{{ $booking->installments->count() }} Schedules</h4>
            <div class="text-muted text-xs">Agreement Date: {{ $booking->booking_date?->format('d M Y') }}</div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom p-0">
        <ul class="nav nav-tabs card-header-tabs mx-3 mt-2" id="bookingTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="installments-tab" data-bs-toggle="tab" data-bs-target="#installments" type="button" role="tab">
                    <i class="bi bi-calendar3 me-1"></i> Installment Plan ({{ $booking->installments->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                    <i class="bi bi-receipt me-1"></i> Receipts & Collections ({{ $booking->payments->count() }})
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="bookingTabContent">
            {{-- Tab 1: Installments --}}
            <div class="tab-pane fade show active" id="installments" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Scheduled Monthly & Milestone Due Dates</h6>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="bi bi-cash-stack me-1"></i> Record Collection
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>#</th>
                                <th>Installment Title</th>
                                <th>Due Date</th>
                                <th class="text-end">Scheduled Amount</th>
                                <th class="text-end">Paid Amount</th>
                                <th class="text-end">Balance Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->installments as $inst)
                                <tr>
                                    <td class="fw-bold font-monospace">{{ $inst->installment_no }}</td>
                                    <td>{{ $inst->title }}</td>
                                    <td>{{ $inst->due_date?->format('d M Y') }}</td>
                                    <td class="text-end font-monospace">৳ {{ number_format($inst->amount, 2) }}</td>
                                    <td class="text-end font-monospace text-success fw-bold">৳ {{ number_format($inst->paid_amount, 2) }}</td>
                                    <td class="text-end font-monospace text-danger fw-bold">৳ {{ number_format($inst->remaining_due, 2) }}</td>
                                    <td>
                                        @php
                                            $instBadge = match($inst->status) {
                                                'paid' => 'bg-success',
                                                'partially_paid' => 'bg-warning text-dark',
                                                'overdue' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $instBadge }}">{{ ucfirst(str_replace('_', ' ', $inst->status)) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No installments created.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 2: Payments / Receipts --}}
            <div class="tab-pane fade" id="payments" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Receipt No</th>
                                <th>Date</th>
                                <th>Payment Method</th>
                                <th>Bank / Trans Ref</th>
                                <th class="text-end">Amount Paid</th>
                                <th>Collected By</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->payments as $p)
                                <tr>
                                    <td class="fw-bold font-monospace text-primary">
                                        <a href="{{ route('erp.payments.show', $p) }}" class="text-decoration-none">
                                            {{ $p->receipt_no }}
                                        </a>
                                    </td>
                                    <td>{{ $p->payment_date?->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border text-uppercase">
                                            {{ str_replace('_', ' ', $p->method) }}
                                        </span>
                                    </td>
                                    <td class="small">{{ $p->bank_name ?? '—' }} ({{ $p->cheque_or_trans_no ?? 'N/A' }})</td>
                                    <td class="text-end font-monospace fw-bold text-success">৳ {{ number_format($p->amount, 2) }}</td>
                                    <td class="small">{{ $p->collectedBy?->name ?? 'Admin' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('erp.payments.show', $p) }}" class="btn btn-sm btn-light border" title="Print Receipt">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No collections recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Collect Payment Modal --}}
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.bookings.payments.store', $booking) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Record Customer Payment Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Customer</label>
                    <div class="fw-bold text-dark">{{ $booking->customer->name }} ({{ $booking->customer->customer_code }})</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Remaining Due Balance</label>
                    <div class="fs-5 fw-bold text-danger">৳ {{ number_format($booking->due_amount, 2) }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Collection Amount (BDT) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="e.g. 500000.00" max="{{ $booking->due_amount }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                    <select name="method" class="form-select" required>
                        <option value="bank_transfer">Bank Transfer / Online Deposit</option>
                        <option value="cheque">Cheque Deposit</option>
                        <option value="mfs_bkash">bKash Merchant</option>
                        <option value="mfs_nagad">Nagad</option>
                        <option value="cash">Cash on Desk</option>
                        <option value="card">POS / Credit Card</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Bank Name / Gateway</label>
                    <input type="text" name="bank_name" class="form-control" placeholder="e.g. BRAC Bank, City Bank">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Cheque No / Transaction ID</label>
                    <input type="text" name="cheque_or_trans_no" class="form-control" placeholder="e.g. CHQ-481920 or TrxID">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Collection Notes</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Remarks..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i> Issue Receipt & Post to GL
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

