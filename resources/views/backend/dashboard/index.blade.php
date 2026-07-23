@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Dashboard Overview</h4>
    <div class="text-muted small">
        <i class="bi bi-calendar-event me-1"></i> {{ date('F d, Y') }}
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="widget-icon me-3">
                    <i class="bi bi-buildings"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Properties</h6>
                    <h3 class="mb-0 fw-bold">0</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="widget-icon me-3">
                    <i class="bi bi-map"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Projects</h6>
                    <h3 class="mb-0 fw-bold">0</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="widget-icon me-3">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Team Members</h6>
                    <h3 class="mb-0 fw-bold">0</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="widget-icon me-3">
                    <i class="bi bi-chat-quote"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Testimonials</h6>
                    <h3 class="mb-0 fw-bold">0</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card p-4 h-100">
            <h5 class="fw-bold mb-4">Recent Updates</h5>
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                <p>No recent updates to show.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card p-4 h-100">
            <h5 class="fw-bold mb-4">Quick Actions</h5>
            <div class="d-grid gap-3">
                <a href="#" class="btn btn-outline-secondary text-start">
                    <i class="bi bi-gear me-2 text-primary"></i> Homepage Settings
                </a>
                <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary text-start">
                    <i class="bi bi-globe me-2 text-success"></i> Visit Website
                </a>
                <a href="#" class="btn btn-outline-secondary text-start">
                    <i class="bi bi-arrow-clockwise me-2 text-warning"></i> Clear Cache
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
