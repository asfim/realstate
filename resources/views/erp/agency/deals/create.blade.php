@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.agency-deals.index') }}" class="text-decoration-none">Brokerage Deals</a></li>
                <li class="breadcrumb-item active" aria-current="page">Record Closed Deal</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Close Brokerage Deal & Commission Settlement</h4>
        <p class="text-muted small mb-0">Record transaction closing, compute brokerage commission, internal agent incentive split, and external co-broker share.</p>
    </div>
    <div>
        <a href="{{ route('erp.agency-deals.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Deals
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please correct the following errors:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-custom p-4">
    <form action="{{ route('erp.agency-deals.store') }}" method="POST">
        @csrf

        <!-- Deal Parties -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-building-check text-erp-gold me-2"></i> Listing & Purchaser Details
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Agency Listing Mandate <span class="text-danger">*</span></label>
                <select name="listing_id" id="listingSelector" class="form-select form-select-sm" required>
                    <option value="">-- Choose Listing Mandate --</option>
                    @foreach($listings as $item)
                        <option value="{{ $item->id }}" 
                                data-asking="{{ $item->asking_price }}" 
                                data-rate="{{ $item->commission_rate_percent }}"
                                data-type="{{ $item->listing_type }}"
                                {{ (old('listing_id', request('listing_id')) == $item->id || ($selectedListing && $selectedListing->id == $item->id)) ? 'selected' : '' }}>
                            {{ $item->listing_code }} - {{ $item->title }} (৳{{ number_format($item->asking_price) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Buyer / Purchaser / Client <span class="text-danger">*</span></label>
                <select name="customer_id" class="form-select form-select-sm" required>
                    <option value="">-- Select Registered Client --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->phone }}) - {{ $c->customer_code }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Client registered in CRM. <a href="{{ route('erp.crm-customers.create') }}" target="_blank">Register new client</a></div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Deal Type <span class="text-danger">*</span></label>
                <select name="deal_type" id="dealType" class="form-select form-select-sm" required>
                    <option value="resale" {{ old('deal_type') === 'resale' ? 'selected' : '' }}>Resale / Outright Sale</option>
                    <option value="rental" {{ old('deal_type') === 'rental' ? 'selected' : '' }}>Rental Lease</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Closing / Contract Date <span class="text-danger">*</span></label>
                <input type="date" name="deal_date" class="form-control form-control-sm" value="{{ old('deal_date', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Lead Closing Agent</label>
                <select name="agent_id" class="form-select form-select-sm">
                    <option value="">-- Direct Deal / Unassigned --</option>
                    @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" {{ old('agent_id', auth('admin')->id()) == $ag->id ? 'selected' : '' }}>
                            {{ $ag->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Financial Settlement & Commission Sharing Matrix -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-cash-stack text-erp-gold me-2"></i> Financial Valuation & Commission Sharing Matrix
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Final Closing Transaction Price (BDT) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="closing_price" id="closingPrice" class="form-control form-control-sm" placeholder="e.g. 24000000" value="{{ old('closing_price') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Total Brokerage Commission Received (BDT) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="company_commission_amount" id="companyCommission" class="form-control form-control-sm" placeholder="e.g. 480000" value="{{ old('company_commission_amount') }}" required>
                <div class="form-text">Gross commission earned by Aurelia.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Agent Commission Incentive Rate (%)</label>
                <input type="number" step="0.01" name="agent_commission_percent" id="agentPercent" class="form-control form-control-sm" value="{{ old('agent_commission_percent', '20.00') }}">
                <div class="form-text">% of company commission paid to internal sales agent.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">External Co-Broker / Partner Name (Optional)</label>
                <input type="text" name="external_broker_name" class="form-control form-control-sm" placeholder="e.g. BD Property Hub Ltd." value="{{ old('external_broker_name') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Co-Broker Phone</label>
                <input type="text" name="external_broker_phone" class="form-control form-control-sm" placeholder="+880 1..." value="{{ old('external_broker_phone') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">External Co-Broker Cut / Payout (BDT)</label>
                <input type="number" step="0.01" name="external_broker_commission_amount" id="externalBrokerCommission" class="form-control form-control-sm" value="{{ old('external_broker_commission_amount', '0') }}">
            </div>
        </div>

        <!-- Net Earnings Live Breakdown Card -->
        <div class="card bg-light border p-3 mb-4">
            <div class="row text-center">
                <div class="col-md-3 border-end">
                    <div class="text-muted extra-small text-uppercase fw-semibold">Gross Commission</div>
                    <div class="fs-5 fw-bold text-dark" id="dispGrossComm">৳ 0.00</div>
                </div>
                <div class="col-md-3 border-end">
                    <div class="text-muted extra-small text-uppercase fw-semibold">Agent Incentive Cut</div>
                    <div class="fs-5 fw-bold text-primary" id="dispAgentCut">৳ 0.00</div>
                </div>
                <div class="col-md-3 border-end">
                    <div class="text-muted extra-small text-uppercase fw-semibold">External Broker Cut</div>
                    <div class="fs-5 fw-bold text-danger" id="dispCoBrokerCut">৳ 0.00</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted extra-small text-uppercase fw-semibold">Company Net Commission</div>
                    <div class="fs-4 fw-bold text-success" id="dispCompanyNet">৳ 0.00</div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Deal Closing Notes / Settlement Terms</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Cheque received at deed signing, deed registration scheduled on...">{{ old('notes') }}</textarea>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('erp.agency-deals.index') }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-erp-gold px-5 fw-semibold">
                <i class="bi bi-file-earmark-check me-1"></i> Register Closed Deal
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const listingSelector = document.getElementById('listingSelector');
    const closingPrice = document.getElementById('closingPrice');
    const companyCommission = document.getElementById('companyCommission');
    const agentPercent = document.getElementById('agentPercent');
    const externalBrokerCommission = document.getElementById('externalBrokerCommission');
    const dealType = document.getElementById('dealType');

    const dispGrossComm = document.getElementById('dispGrossComm');
    const dispAgentCut = document.getElementById('dispAgentCut');
    const dispCoBrokerCut = document.getElementById('dispCoBrokerCut');
    const dispCompanyNet = document.getElementById('dispCompanyNet');

    function calculateDeal() {
        const selected = listingSelector.options[listingSelector.selectedIndex];
        if (selected && selected.dataset.asking && !closingPrice.value) {
            closingPrice.value = selected.dataset.asking;
            const rate = parseFloat(selected.dataset.rate) || 2;
            companyCommission.value = ((parseFloat(selected.dataset.asking) * rate) / 100).toFixed(2);
            if (selected.dataset.type) {
                dealType.value = selected.dataset.type === 'resale' ? 'resale' : 'rental';
            }
        }

        const gross = parseFloat(companyCommission.value) || 0;
        const agRate = parseFloat(agentPercent.value) || 0;
        const coBroker = parseFloat(externalBrokerCommission.value) || 0;

        const agentCut = (gross * agRate) / 100;
        const companyNet = Math.max(0, gross - agentCut - coBroker);

        dispGrossComm.innerText = '৳ ' + Number(gross.toFixed(2)).toLocaleString();
        dispAgentCut.innerText = '৳ ' + Number(agentCut.toFixed(2)).toLocaleString();
        dispCoBrokerCut.innerText = '৳ ' + Number(coBroker.toFixed(2)).toLocaleString();
        dispCompanyNet.innerText = '৳ ' + Number(companyNet.toFixed(2)).toLocaleString();
    }

    if (listingSelector) listingSelector.addEventListener('change', calculateDeal);
    if (closingPrice) closingPrice.addEventListener('input', calculateDeal);
    if (companyCommission) companyCommission.addEventListener('input', calculateDeal);
    if (agentPercent) agentPercent.addEventListener('input', calculateDeal);
    if (externalBrokerCommission) externalBrokerCommission.addEventListener('input', calculateDeal);

    calculateDeal();
});
</script>
@endsection

