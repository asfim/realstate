@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.dev-bookings.index') }}" class="text-decoration-none">Sales Bookings</a></li>
                <li class="breadcrumb-item active" aria-current="page">New Booking Contract</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Create Developer Sales Booking</h4>
        <p class="text-muted small mb-0">Select project unit, assign buyer, compute total contract value, and generate installment payment milestones.</p>
    </div>
    <div>
        <a href="{{ route('erp.dev-bookings.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please check form errors:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-custom p-4">
    <!-- Step 1: Select Project selector form to refresh available inventory -->
    <div class="p-3 bg-light rounded-3 mb-4 border">
        <label class="form-label fw-bold text-dark mb-1">1. Choose Development Project</label>
        <p class="text-muted extra-small mb-2">Changing project reloads available floor units and parking inventory for this project.</p>
        <form method="GET" action="{{ route('erp.dev-bookings.create') }}" id="projectSelectForm">
            <div class="row g-2 align-items-center">
                <div class="col-md-8">
                    <select name="project_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Choose a Project --</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ (request('project_id') == $proj->id || ($selectedProject && $selectedProject->id == $proj->id)) ? 'selected' : '' }}>
                                {{ $proj->name }} ({{ $proj->project_code }}) - {{ $proj->location }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-dark btn-sm">Load Inventory</button>
                </div>
            </div>
        </form>
    </div>

    @if(!$selectedProject)
        <div class="text-center py-5 text-muted">
            <i class="bi bi-building-check fs-1 text-secondary opacity-50 d-block mb-2"></i>
            Please select a project from above to view available units, parking bays, and complete the sales contract.
        </div>
    @else
        <form action="{{ route('erp.dev-bookings.store') }}" method="POST" id="bookingForm">
            @csrf
            <input type="hidden" name="project_id" value="{{ $selectedProject->id }}">

            <!-- Customer & Agent Information -->
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-person-badge-fill text-erp-gold me-2"></i> Buyer & Sales Agent Assignment
            </h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Customer / Buyer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select form-select-sm" required>
                        <option value="">-- Select Registered Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->phone }}) - {{ $c->customer_code }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Customer must be active in CRM. <a href="{{ route('erp.crm-customers.create') }}" target="_blank">Add new customer</a></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Lead Reference (Optional)</label>
                    <input type="number" name="lead_id" class="form-control form-control-sm" placeholder="Lead ID if converted from pipeline" value="{{ old('lead_id') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Assigned Sales Agent / Relationship Manager</label>
                    <select name="sales_agent_id" class="form-select form-select-sm">
                        <option value="">-- Direct Sales / Unassigned --</option>
                        @foreach($agents as $ag)
                            <option value="{{ $ag->id }}" {{ old('sales_agent_id', auth('admin')->id()) == $ag->id ? 'selected' : '' }}>
                                {{ $ag->name }} ({{ $ag->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Agent Commission Rate (%)</label>
                    <input type="number" step="0.01" name="commission_rate" class="form-control form-control-sm" placeholder="e.g. 1.50" value="{{ old('commission_rate', '1.00') }}">
                </div>
            </div>

            <!-- Unit & Parking Selection -->
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-grid-fill text-erp-gold me-2"></i> Unit & Parking Selection
            </h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Select Available Unit <span class="text-danger">*</span></label>
                    <select name="unit_id" id="unitSelector" class="form-select form-select-sm" required>
                        <option value="">-- Choose Unit --</option>
                        @foreach($availableUnits as $unit)
                            <option value="{{ $unit->id }}" 
                                    data-sft="{{ $unit->gross_area_sqft }}" 
                                    data-rate="{{ $unit->base_price_sqft }}"
                                    {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                Unit {{ $unit->unit_number }} (Floor {{ $unit->floor_number }}) - {{ number_format($unit->gross_area_sqft) }} sft [Base Rate: ৳{{ number_format($unit->base_price_sqft) }}]
                            </option>
                        @endforeach
                    </select>
                    @if($availableUnits->isEmpty())
                        <div class="text-danger extra-small mt-1">No units with 'available' status found in this project.</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Assigned Parking Bay (Optional)</label>
                    <select name="parking_slot_id" id="parkingSelector" class="form-select form-select-sm">
                        <option value="" data-price="0">-- No Dedicated Parking --</option>
                        @foreach($availableParking as $slot)
                            <option value="{{ $slot->id }}" data-price="{{ $slot->price }}" {{ old('parking_slot_id') == $slot->id ? 'selected' : '' }}>
                                Bay {{ $slot->slot_number }} ({{ $slot->basement_level }}) - ৳{{ number_format($slot->price) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Pricing & Contract Calculation -->
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-cash-stack text-erp-gold me-2"></i> Pricing & Payment Terms
            </h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Agreed Price / Sqft (BDT) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="agreed_price_sqft" id="agreedPriceSqft" class="form-control form-control-sm" value="{{ old('agreed_price_sqft', '10000') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Parking Charges (BDT)</label>
                    <input type="number" step="0.01" name="agreed_parking_price" id="agreedParkingPrice" class="form-control form-control-sm" value="{{ old('agreed_parking_price', '0') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Utility / Connection Charges (BDT)</label>
                    <input type="number" step="0.01" name="agreed_utility_charge" id="agreedUtilityCharge" class="form-control form-control-sm" value="{{ old('agreed_utility_charge', '300000') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Downpayment / Booking Token (BDT) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="downpayment_amount" id="downpaymentAmount" class="form-control form-control-sm" value="{{ old('downpayment_amount', '1000000') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Downpayment Due Date</label>
                    <input type="date" name="downpayment_due_date" class="form-control form-control-sm" value="{{ old('downpayment_due_date', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Payment Plan Type <span class="text-danger">*</span></label>
                    <select name="payment_plan_type" class="form-select form-select-sm" required>
                        <option value="installment" {{ old('payment_plan_type') == 'installment' ? 'selected' : '' }}>Monthly Equal Installments (EMI)</option>
                        <option value="construction_linked" {{ old('payment_plan_type') == 'construction_linked' ? 'selected' : '' }}>Construction Milestone Linked</option>
                        <option value="full_cash" {{ old('payment_plan_type') == 'full_cash' ? 'selected' : '' }}>Full Cash / Down Payment</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Number of Installments <span class="text-danger">*</span></label>
                    <input type="number" name="total_installments" id="totalInstallments" class="form-control form-control-sm" min="1" max="120" value="{{ old('total_installments', '24') }}" required>
                    <div class="form-text">Remaining balance will be divided into monthly milestones.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Booking Date <span class="text-danger">*</span></label>
                    <input type="date" name="booking_date" class="form-control form-control-sm" value="{{ old('booking_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Expected Handover Date</label>
                    <input type="date" name="expected_handover_date" class="form-control form-control-sm" value="{{ old('expected_handover_date', now()->addYears(2)->format('Y-m-d')) }}">
                </div>
            </div>

            <!-- Total Calculation Summary Banner -->
            <div class="card bg-light border p-3 mb-4">
                <div class="row text-center">
                    <div class="col-md-3 border-end">
                        <div class="text-muted extra-small text-uppercase fw-semibold">Unit Size</div>
                        <div class="fs-5 fw-bold text-dark" id="displayUnitSize">0 sft</div>
                    </div>
                    <div class="col-md-3 border-end">
                        <div class="text-muted extra-small text-uppercase fw-semibold">Unit Total Price</div>
                        <div class="fs-5 fw-bold text-dark" id="displayUnitPrice">৳ 0</div>
                    </div>
                    <div class="col-md-3 border-end">
                        <div class="text-muted extra-small text-uppercase fw-semibold">Grand Total Contract</div>
                        <div class="fs-4 fw-bold text-success" id="displayGrandTotal">৳ 0</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted extra-small text-uppercase fw-semibold">Approx. Monthly Installment</div>
                        <div class="fs-5 fw-bold text-primary" id="displayMonthlyEmi">৳ 0</div>
                    </div>
                </div>
            </div>

            <!-- Notes & Terms -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Special Conditions / Clause</label>
                <textarea name="special_conditions" class="form-control" rows="3" placeholder="e.g. 5% rebate if paid in advance, deed registration within 60 days of completion...">{{ old('special_conditions') }}</textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="{{ route('erp.dev-bookings.index') }}" class="btn btn-light border px-4">Cancel</a>
                <button type="submit" class="btn btn-erp-gold px-5 fw-semibold">
                    <i class="bi bi-file-earmark-check me-1"></i> Finalize Contract & Generate Milestones
                </button>
            </div>
        </form>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const unitSelector = document.getElementById('unitSelector');
    const parkingSelector = document.getElementById('parkingSelector');
    const agreedPriceSqft = document.getElementById('agreedPriceSqft');
    const agreedParkingPrice = document.getElementById('agreedParkingPrice');
    const agreedUtilityCharge = document.getElementById('agreedUtilityCharge');
    const downpaymentAmount = document.getElementById('downpaymentAmount');
    const totalInstallments = document.getElementById('totalInstallments');

    const displayUnitSize = document.getElementById('displayUnitSize');
    const displayUnitPrice = document.getElementById('displayUnitPrice');
    const displayGrandTotal = document.getElementById('displayGrandTotal');
    const displayMonthlyEmi = document.getElementById('displayMonthlyEmi');

    function calculateSummary() {
        if (!unitSelector) return;
        const selectedUnit = unitSelector.options[unitSelector.selectedIndex];
        const sft = selectedUnit && selectedUnit.dataset.sft ? parseFloat(selectedUnit.dataset.sft) : 0;
        
        // Auto populate agreed sqft if empty
        if (selectedUnit && selectedUnit.dataset.rate && (!agreedPriceSqft.value || agreedPriceSqft.value == '10000')) {
            if (parseFloat(selectedUnit.dataset.rate) > 0) {
                agreedPriceSqft.value = selectedUnit.dataset.rate;
            }
        }

        const rate = parseFloat(agreedPriceSqft.value) || 0;
        const parking = parseFloat(agreedParkingPrice.value) || 0;
        const utility = parseFloat(agreedUtilityCharge.value) || 0;
        const dp = parseFloat(downpaymentAmount.value) || 0;
        const installments = parseInt(totalInstallments.value) || 1;

        const unitTotal = sft * rate;
        const grandTotal = unitTotal + parking + utility;
        const balance = Math.max(0, grandTotal - dp);
        const emi = installments > 0 ? (balance / installments) : 0;

        if (displayUnitSize) displayUnitSize.innerText = Number(sft).toLocaleString() + ' sft';
        if (displayUnitPrice) displayUnitPrice.innerText = '৳ ' + Number(unitTotal.toFixed(2)).toLocaleString();
        if (displayGrandTotal) displayGrandTotal.innerText = '৳ ' + Number(grandTotal.toFixed(2)).toLocaleString();
        if (displayMonthlyEmi) displayMonthlyEmi.innerText = '৳ ' + Number(emi.toFixed(2)).toLocaleString() + ' /mo';
    }

    if (unitSelector) {
        unitSelector.addEventListener('change', calculateSummary);
    }
    if (parkingSelector) {
        parkingSelector.addEventListener('change', function () {
            const selectedParking = parkingSelector.options[parkingSelector.selectedIndex];
            if (selectedParking && selectedParking.dataset.price) {
                agreedParkingPrice.value = selectedParking.dataset.price;
            }
            calculateSummary();
        });
    }
    if (agreedPriceSqft) agreedPriceSqft.addEventListener('input', calculateSummary);
    if (agreedParkingPrice) agreedParkingPrice.addEventListener('input', calculateSummary);
    if (agreedUtilityCharge) agreedUtilityCharge.addEventListener('input', calculateSummary);
    if (downpaymentAmount) downpaymentAmount.addEventListener('input', calculateSummary);
    if (totalInstallments) totalInstallments.addEventListener('input', calculateSummary);

    calculateSummary();
});
</script>
@endsection

