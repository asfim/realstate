@extends('erp.layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Top Welcome Banner -->
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: linear-gradient(135deg, #090e1a 0%, #1e293b 100%); color: #fff;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark px-3 py-1 mb-2 fw-semibold" style="letter-spacing: 0.5px;">ENTERPRISE REAL ESTATE ERP</span>
                    <h2 class="fw-bold mb-2">{{ $currentCompany->legal_name ?? 'Aurelia Estates & Developers Ltd.' }}</h2>
                    <p class="text-white-50 mb-3" style="max-width: 650px;">
                        Centralized corporate multi-branch workspace for Developer Project Sales, Agency Brokerages, Cost Centres, and Double-Entry General Ledgers.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-dark border border-secondary text-light px-3 py-2">
                            <i class="bi bi-geo-alt-fill text-warning me-1"></i> Active Branch: <strong>{{ $currentBranch->name ?? 'Dhaka Corporate HQ' }}</strong>
                        </span>
                        <span class="badge bg-dark border border-secondary text-light px-3 py-2">
                            <i class="bi bi-calendar3 text-info me-1"></i> Current Fiscal: <strong>{{ $stats['current_fy']->name ?? 'FY 2026-2027' }}</strong>
                        </span>
                        <span class="badge bg-dark border border-secondary text-light px-3 py-2">
                            <i class="bi bi-currency-exchange text-success me-1"></i> Currency: <strong>{{ $currentCompany->base_currency ?? 'BDT' }}</strong>
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <a href="{{ route('erp.branches.create') }}" class="btn btn-erp-gold btn-sm px-3 py-2 me-2 mb-2">
                        <i class="bi bi-plus-circle me-1"></i> Add Branch
                    </a>
                    <a href="{{ route('erp.audit-logs.index') }}" class="btn btn-outline-light btn-sm px-3 py-2 mb-2">
                        <i class="bi bi-shield-check me-1"></i> Audit Trail
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">OPERATING BRANCHES</span>
                    <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-buildings fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $stats['branches_count'] }}</h3>
                <a href="{{ route('erp.branches.index') }}" class="small text-decoration-none text-primary fw-medium">
                    Manage Branches <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">ORGANIZATION DEPTS</span>
                    <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-diagram-3 fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $stats['departments_count'] }}</h3>
                <a href="{{ route('erp.departments.index') }}" class="small text-decoration-none text-success fw-medium">
                    View Departments <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">COST CENTRES</span>
                    <div class="rounded-3 p-2 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-piggy-bank fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $stats['cost_centers_count'] }}</h3>
                <a href="{{ route('erp.cost-centers.index') }}" class="small text-decoration-none text-warning fw-medium">
                    Configure Cost Centres <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">DOCUMENT SEQUENCER</span>
                    <div class="rounded-3 p-2 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-hash fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">Active</h3>
                <a href="{{ route('erp.document-sequences.index') }}" class="small text-decoration-none text-info fw-medium">
                    Sequence Rules <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Phase Modules Navigation Grid -->
    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-grid-3x3-gap-fill text-warning me-2"></i>ERP Modules Directory</h5>
    <div class="row g-3">
        <!-- Module 1: Foundation (Live) -->
        <div class="col-md-6 col-lg-4">
            <div class="card card-custom h-100 p-4 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fw-bold text-dark fs-6">01. Organization Foundation</div>
                    <span class="badge bg-success">ACTIVE</span>
                </div>
                <p class="text-muted small mb-3">
                    Legal entities, branch structure, cost centers, fiscal year calendar, atomic document sequencers, and audit trail.
                </p>
                <div class="mt-auto">
                    <a href="{{ route('erp.companies.index') }}" class="btn btn-sm btn-outline-dark me-1 mb-1">Entities</a>
                    <a href="{{ route('erp.branches.index') }}" class="btn btn-sm btn-outline-dark me-1 mb-1">Branches</a>
                    <a href="{{ route('erp.departments.index') }}" class="btn btn-sm btn-outline-dark me-1 mb-1">Depts</a>
                    <a href="{{ route('erp.cost-centers.index') }}" class="btn btn-sm btn-outline-dark mb-1">Cost Centres</a>
                </div>
            </div>
        </div>

        <!-- Module 2: CRM & Lead Engine -->
        <div class="col-md-6 col-lg-4">
            <div class="card card-custom h-100 p-4 border-start border-4 border-secondary">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fw-bold text-dark fs-6">02. CRM & Lead Engine</div>
                    <span class="badge bg-secondary">PHASE 2</span>
                </div>
                <p class="text-muted small mb-3">
                    Multi-channel lead ingestion (Meta/Web/Walk-in), lead scoring, automated agent assignment, follow-up scheduler & site visits.
                </p>
                <div class="mt-auto">
                    <span class="text-muted small fst-italic">Architecture ready for deployment</span>
                </div>
            </div>
        </div>

        <!-- Module 3: Land & Feasibility -->
        <div class="col-md-6 col-lg-4">
            <div class="card card-custom h-100 p-4 border-start border-4 border-secondary">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fw-bold text-dark fs-6">03. Land & Feasibility</div>
                    <span class="badge bg-secondary">PHASE 3</span>
                </div>
                <p class="text-muted small mb-3">
                    Land acquisition tracking, joint venture landowner sharing agreements, vetting status, soil test & zoning feasibility.
                </p>
                <div class="mt-auto">
                    <span class="text-muted small fst-italic">Architecture ready for deployment</span>
                </div>
            </div>
        </div>

        <!-- Module 4: Developer Sales & Inventory -->
        <div class="col-md-6 col-lg-4">
            <div class="card card-custom h-100 p-4 border-start border-4 border-secondary">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fw-bold text-dark fs-6">04. Sales & Inventory</div>
                    <span class="badge bg-secondary">PHASE 4</span>
                </div>
                <p class="text-muted small mb-3">
                    Apartments, commercial floors, plots, parking allocations, milestone payment schedules, bookings, cancellations & transfer deeds.
                </p>
                <div class="mt-auto">
                    <span class="text-muted small fst-italic">Architecture ready for deployment</span>
                </div>
            </div>
        </div>

        <!-- Module 5: Brokerage & Agency -->
        <div class="col-md-6 col-lg-4">
            <div class="card card-custom h-100 p-4 border-start border-4 border-secondary">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fw-bold text-dark fs-6">05. Brokerage & Agency</div>
                    <span class="badge bg-secondary">PHASE 5</span>
                </div>
                <p class="text-muted small mb-3">
                    Third-party resale listings, landlord mandates, commission sharing matrix, tenant leases, security deposits & key tracking.
                </p>
                <div class="mt-auto">
                    <span class="text-muted small fst-italic">Architecture ready for deployment</span>
                </div>
            </div>
        </div>

        <!-- Module 6: Construction & Materials -->
        <div class="col-md-6 col-lg-4">
            <div class="card card-custom h-100 p-4 border-start border-4 border-secondary">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="fw-bold text-dark fs-6">06. Construction & Material</div>
                    <span class="badge bg-secondary">PHASE 6</span>
                </div>
                <p class="text-muted small mb-3">
                    Bill of Quantities (BOQ), site material requisitions, purchase orders, Good Receipt Notes (GRN), and site inventory stock.
                </p>
                <div class="mt-auto">
                    <span class="text-muted small fst-italic">Architecture ready for deployment</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
