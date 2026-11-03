@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.agency-deals.index') }}" class="text-decoration-none">Brokerage Deals</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $agencyDeal->deal_code }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-dark">Deal Settlement {{ $agencyDeal->deal_code }}</h4>
            <span class="badge {{ $agencyDeal->status === 'settled' ? 'bg-success' : ($agencyDeal->status === 'pending_settlement' ? 'bg-warning text-dark' : 'bg-danger') }} px-3 py-1">
                {{ strtoupper(str_replace('_', ' ', $agencyDeal->status)) }}
            </span>
        </div>
        <p class="text-muted small mb-0">Deal Date: {{ \Carbon\Carbon::parse($agencyDeal->deal_date)->format('F d, Y') }} | Closing Agent: {{ $agencyDeal->agent->name ?? 'Direct' }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.agency-deals.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Deals
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Voucher
        </button>
        @if($agencyDeal->status === 'pending_settlement')
            <form action="{{ route('erp.agency-deals.settle', $agencyDeal) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm px-3" onclick="return confirm('Confirm settlement of this brokerage deal?')">
                    <i class="bi bi-check2-circle me-1"></i> Mark as Settled
                </button>
            </form>
        @endif
    </div>
</div>

@php
    $netCompany = max(0, $agencyDeal->company_commission_amount - $agencyDeal->agent_commission_amount - $agencyDeal->external_broker_commission_amount);
@endphp

<!-- Financial Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Closing Transaction Price</div>
            <div class="fs-4 fw-bold text-dark mt-1">৳ {{ number_format($agencyDeal->closing_price, 2) }}</div>
            <div class="text-muted extra-small mt-1">Type: {{ ucfirst($agencyDeal->deal_type) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Gross Commission</div>
            <div class="fs-4 fw-bold text-info mt-1">৳ {{ number_format($agencyDeal->company_commission_amount, 2) }}</div>
            <div class="text-muted extra-small mt-1">Revenue Inflow</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Agent Incentive ({{ $agencyDeal->agent_commission_percent }}%)</div>
            <div class="fs-4 fw-bold text-warning mt-1">৳ {{ number_format($agencyDeal->agent_commission_amount, 2) }}</div>
            <div class="text-muted extra-small mt-1">Agent: {{ $agencyDeal->agent->name ?? 'None' }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Company Net Profit</div>
            <div class="fs-4 fw-bold text-success mt-1">৳ {{ number_format($netCompany, 2) }}</div>
            <div class="text-success extra-small mt-1 fw-semibold">Net Retained</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Property Info -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-building text-erp-gold me-2"></i> Transacted Property Mandate
            </h6>
            @if($agencyDeal->listing)
                <div class="row g-2 small">
                    <div class="col-sm-4 text-muted">Listing Code:</div>
                    <div class="col-sm-8 fw-bold">
                        <a href="{{ route('erp.agency-listings.show', $agencyDeal->listing_id) }}" class="text-decoration-none">
                            {{ $agencyDeal->listing->listing_code }}
                        </a>
                    </div>

                    <div class="col-sm-4 text-muted">Title:</div>
                    <div class="col-sm-8 text-dark fw-semibold">{{ $agencyDeal->listing->title }}</div>

                    <div class="col-sm-4 text-muted">Location:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyDeal->listing->location }}</div>

                    <div class="col-sm-4 text-muted">Original Asking:</div>
                    <div class="col-sm-8 text-dark">৳ {{ number_format($agencyDeal->listing->asking_price, 2) }}</div>

                    <div class="col-sm-4 text-muted">Property Seller:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyDeal->listing->landlord_seller_name }} ({{ $agencyDeal->listing->landlord_seller_phone }})</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Buyer / Purchaser Info -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-person-fill text-erp-gold me-2"></i> Buyer / Acquiring Client
            </h6>
            @if($agencyDeal->customer)
                <div class="row g-2 small">
                    <div class="col-sm-4 text-muted">Customer Name:</div>
                    <div class="col-sm-8 fw-bold text-dark">{{ $agencyDeal->customer->name }} ({{ $agencyDeal->customer->customer_code }})</div>

                    <div class="col-sm-4 text-muted">Phone Number:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyDeal->customer->phone }}</div>

                    <div class="col-sm-4 text-muted">Email Address:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyDeal->customer->email ?? '—' }}</div>

                    <div class="col-sm-4 text-muted">NID / Passport:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyDeal->customer->nid_or_passport ?? '—' }}</div>

                    <div class="col-sm-4 text-muted">Address:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyDeal->customer->present_address ?? '—' }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Commission Split Ledger -->
<div class="card card-custom mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-pie-chart-fill text-erp-gold me-2"></i> Commission Distribution Breakdown
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0 align-middle">
            <thead class="table-light small">
                <tr>
                    <th>Beneficiary / Party</th>
                    <th>Role</th>
                    <th>Share %</th>
                    <th class="text-end pe-3">Payout Amount (BDT)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ $agencyDeal->company->name ?? 'Aurelia ERP' }}</strong></td>
                    <td>Brokerage Agency Operating Account</td>
                    <td>—</td>
                    <td class="text-end pe-3 fw-bold text-info">৳ {{ number_format($agencyDeal->company_commission_amount, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>{{ $agencyDeal->agent->name ?? 'Internal Agent' }}</strong></td>
                    <td>Closing Agent Incentive</td>
                    <td>{{ $agencyDeal->agent_commission_percent }}% of gross</td>
                    <td class="text-end pe-3 fw-semibold text-warning">৳ {{ number_format($agencyDeal->agent_commission_amount, 2) }}</td>
                </tr>
                @if($agencyDeal->external_broker_name)
                    <tr>
                        <td><strong>{{ $agencyDeal->external_broker_name }}</strong> ({{ $agencyDeal->external_broker_phone ?? '—' }})</td>
                        <td>External Co-Broker Partner</td>
                        <td>Fixed Cut</td>
                        <td class="text-end pe-3 fw-semibold text-danger">৳ {{ number_format($agencyDeal->external_broker_commission_amount, 2) }}</td>
                    </tr>
                @endif
                <tr class="table-light">
                    <td colspan="3" class="fw-bold text-end">Net Retained by Company:</td>
                    <td class="text-end pe-3 fw-bold text-success fs-5">৳ {{ number_format($netCompany, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@if($agencyDeal->notes)
    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold text-dark mb-2">Deal Settlement Notes</h6>
        <p class="text-muted small mb-0">{{ $agencyDeal->notes }}</p>
    </div>
@endif
@endsection

