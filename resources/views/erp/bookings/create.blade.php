@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-bag-plus text-primary me-2"></i> Create Sales Booking & Contract
        </h4>
        <div class="text-muted small">Reserve unit, lock contracted price snapshot and generate installment schedule</div>
    </div>
    <div>
        <a href="{{ route('erp.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Bookings
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('erp.bookings.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">1. Select Buyer & Available Unit</h6>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Customer / Buyer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                        <option value="">-- Choose Registered Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ (old('customer_id', $selectedCustomerId) == $c->id) ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->customer_code }}) &bull; {{ $c->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Sellable Inventory Unit <span class="text-danger">*</span></label>
                    <select name="unit_id" id="unitSelect" class="form-select @error('unit_id') is-invalid @enderror" required onchange="updatePrice(this)">
                        <option value="">-- Choose Available Unit --</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" data-price="{{ $u->asking_price }}" {{ (old('unit_id', $selectedUnit?->id) == $u->id) ? 'selected' : '' }}>
                                {{ $u->unit_code }} &bull; {{ $u->project?->name }} ({{ ucfirst($u->type) }}, ৳ {{ number_format($u->asking_price, 0) }})
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">2. Contract Pricing & Payment Terms (BDT)</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Contracted Price (BDT) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="contract_price" id="contractPriceInput" class="form-control @error('contract_price') is-invalid @enderror" value="{{ old('contract_price', $selectedUnit?->asking_price) }}" required>
                    </div>
                    @error('contract_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Approved Discount (BDT)</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="discount_amount" class="form-control" value="{{ old('discount_amount', 0) }}">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Booking Money / Deposit (BDT)</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="booking_money" class="form-control" value="{{ old('booking_money', 0) }}">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Down Payment (BDT)</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="down_payment" class="form-control" value="{{ old('down_payment', 0) }}">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Installment Schedule Tenure</label>
                    <select name="installments_count" class="form-select">
                        <option value="12">12 Months (1 Year)</option>
                        <option value="24" selected>24 Months (2 Years)</option>
                        <option value="36">36 Months (3 Years)</option>
                        <option value="48">48 Months (4 Years)</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Booking Agreement Date <span class="text-danger">*</span></label>
                    <input type="date" name="booking_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Special Contract Terms / Notes</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Parking slot allocation, handover commitments, payment milestones..."></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('erp.bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Confirm Booking & Post Sales Ledger
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updatePrice(select) {
    const selectedOption = select.options[select.selectedIndex];
    const price = selectedOption.getAttribute('data-price');
    if (price) {
        document.getElementById('contractPriceInput').value = price;
    }
}
</script>
@endsection
