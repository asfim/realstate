@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-dark font-monospace px-2 py-1">{{ $customer->customer_code }}</span>
            <h4 class="fw-bold mb-0 text-dark">{{ $customer->name }}</h4>
            <span class="badge {{ $customer->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                {{ ucfirst($customer->status) }}
            </span>
        </div>
        <p class="text-muted small mb-0">Verified customer KYC master file, nominee profile, and buyer account history.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit KYC File
        </a>
        <a href="{{ route('erp.customers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Directory
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Personal KYC -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person-vcard text-warning me-2"></i>Personal & Contact Details</h6>
            
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <small class="text-muted d-block">Full Name</small>
                    <div class="fw-bold text-dark">{{ $customer->name }}</div>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Father / Husband Name</small>
                    <div class="fw-medium text-dark">{{ $customer->father_or_husband_name ?? '&mdash;' }}</div>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Mother's Name</small>
                    <div class="fw-medium text-dark">{{ $customer->mother_name ?? '&mdash;' }}</div>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Primary Phone</small>
                    <div class="fw-bold text-primary"><i class="bi bi-telephone-fill me-1"></i>{{ $customer->phone }}</div>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Email Address</small>
                    <div class="fw-medium text-dark">{{ $customer->email ?? 'No email recorded' }}</div>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Emergency Contact Phone</small>
                    <div class="fw-medium text-secondary">{{ $customer->emergency_contact ?? '&mdash;' }}</div>
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4"><i class="bi bi-shield-check text-success me-2"></i>Official Identification</h6>
            <div class="row g-3">
                <div class="col-sm-4">
                    <small class="text-muted d-block">National ID (NID)</small>
                    <div class="fw-bold text-dark font-monospace">{{ $customer->nid_number ?? 'Not provided' }}</div>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block">TIN Certificate</small>
                    <div class="fw-bold text-dark font-monospace">{{ $customer->tin_number ?? 'Not provided' }}</div>
                </div>
                <div class="col-sm-4">
                    <small class="text-muted d-block">Passport No.</small>
                    <div class="fw-bold text-dark font-monospace">{{ $customer->passport_number ?? 'N/A' }}</div>
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4"><i class="bi bi-geo-alt text-danger me-2"></i>Residential Addresses</h6>
            <div class="mb-3">
                <small class="text-muted d-block">Present / Mailing Address</small>
                <div class="fw-medium text-dark">{{ $customer->present_address ?? 'Not recorded' }}</div>
            </div>
            <div>
                <small class="text-muted d-block">Permanent Address</small>
                <div class="fw-medium text-dark">{{ $customer->permanent_address ?? 'Not recorded' }}</div>
            </div>
        </div>
    </div>

    <!-- Right Column: Nominee & Originating Lead -->
    <div class="col-lg-6">
        <!-- Nominee Card -->
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-people-fill text-warning me-2"></i>Legal Nominee Information</h6>
            @if($customer->nominee_name)
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nominee Full Name</small>
                        <div class="fw-bold text-dark">{{ $customer->nominee_name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Relationship with Buyer</small>
                        <span class="badge bg-light text-dark border">{{ $customer->nominee_relation ?? 'Nominee' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nominee NID</small>
                        <div class="fw-medium text-dark font-monospace">{{ $customer->nominee_nid ?? '&mdash;' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nominee Phone</small>
                        <div class="fw-medium text-dark">{{ $customer->nominee_phone ?? '&mdash;' }}</div>
                    </div>
                </div>
            @else
                <p class="text-muted small mb-0">No legal nominee recorded for this buyer yet.</p>
            @endif
        </div>

        <!-- Conversion Trace -->
        <div class="card card-custom p-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-diagram-3 text-info me-2"></i>Originating Lead & CRM Audit</h6>
            @if($customer->lead)
                <div class="p-3 bg-light rounded-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark">{{ $customer->lead->lead_code }}</span>
                        <span class="badge bg-success">Booked & Converted</span>
                    </div>
                    <p class="small text-secondary mb-2">
                        Lead captured via <strong>{{ ucfirst(str_replace('_', ' ', $customer->lead->source)) }}</strong> on {{ $customer->lead->created_at->format('d M Y') }}.
                    </p>
                    <a href="{{ route('erp.leads.show', $customer->lead) }}" class="btn btn-sm btn-outline-dark">
                        <i class="bi bi-clock-history me-1"></i> View Full Lead Timeline
                    </a>
                </div>
            @else
                <p class="text-muted small mb-0">Customer was registered directly into Customer Master.</p>
            @endif
        </div>
    </div>
</div>
@endsection
