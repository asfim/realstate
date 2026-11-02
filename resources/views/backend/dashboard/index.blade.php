@extends('backend.layouts.app')

@push('styles')
<style>
    /* ── Dashboard Custom Styles ── */
    :root {
        --dash-card-bg: #ffffff;
        --dash-card-border: rgba(226, 232, 240, 0.8);
        --dash-primary: #D4AF37;
    }

    /* Welcome Banner */
    .dash-hero-banner {
        background: linear-gradient(135deg, #0b1120 0%, #151d30 50%, #1e293b 100%);
        border: 1px solid rgba(212, 175, 55, 0.25);
        border-radius: 18px;
        padding: 26px 28px;
        color: #fff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px -5px rgba(11, 17, 32, 0.15);
    }

    .dash-hero-banner::after {
        content: '';
        position: absolute;
        right: -5%;
        top: -30%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .dash-hero-title {
        font-family: 'Outfit', 'Inter', sans-serif;
        font-weight: 700;
        font-size: 1.55rem;
        letter-spacing: -0.3px;
        margin-bottom: 6px;
    }

    .dash-hero-subtitle {
        color: #94a3b8;
        font-size: 0.9rem;
        margin-bottom: 16px;
        max-width: 600px;
    }

    .dash-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        color: #e2e8f0;
    }

    /* KPI Metric Cards */
    .kpi-card {
        background: #fff;
        border: 1px solid var(--dash-card-border);
        border-radius: 16px;
        padding: 22px 20px;
        height: 100%;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px -4px rgba(0, 0, 0, 0.08);
        border-color: rgba(212, 175, 55, 0.4);
    }

    .kpi-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .kpi-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .icon-gold { background: rgba(212, 175, 55, 0.12); color: #B89228; }
    .icon-blue { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
    .icon-emerald { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .icon-purple { background: rgba(147, 51, 234, 0.1); color: #7e22ce; }

    .kpi-label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
    }

    .kpi-value {
        font-size: 1.85rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        font-family: 'Outfit', 'Inter', sans-serif;
    }

    .kpi-footer {
        padding-top: 12px;
        margin-top: 12px;
        border-top: 1px solid #f1f5f9;
        font-size: 0.78rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #64748b;
    }

    .kpi-link {
        color: #B89228;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: transform 0.2s;
    }

    .kpi-link:hover {
        color: #8c6d1a;
        transform: translateX(3px);
    }

    /* Mini Stats Strip */
    .mini-stat-card {
        background: #fff;
        border: 1px solid var(--dash-card-border);
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s;
    }

    .mini-stat-card:hover {
        border-color: rgba(212, 175, 55, 0.35);
        background: #fdfcf9;
    }

    .mini-stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: #475569;
        flex-shrink: 0;
    }

    /* Section Card */
    .dash-section-card {
        background: #fff;
        border: 1px solid var(--dash-card-border);
        border-radius: 16px;
        padding: 22px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    .dash-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .dash-section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Table & Lead Rows */
    .lead-table th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 14px;
    }

    .lead-table td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .lead-table tr:hover td {
        background-color: #fafbfc;
    }

    .lead-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(30, 41, 59, 0.1) 100%);
        color: #997519;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.88rem;
        flex-shrink: 0;
    }

    .prop-thumbnail {
        width: 46px;
        height: 46px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
    }

    /* Status dot */
    .pulse-unread {
        width: 8px;
        height: 8px;
        background-color: #ef4444;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
    }

    .pulse-read {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    /* Quick Action Buttons */
    .btn-quick-shortcut {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        color: #334155;
        font-size: 0.86rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-quick-shortcut:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
        transform: translateX(3px);
    }

    .btn-quick-shortcut i.action-icon {
        font-size: 1.15rem;
        color: #B89228;
    }

    /* System status box */
    .system-status-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        font-size: 0.8rem;
    }

    .system-status-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px solid #edf2f7;
    }

    .system-status-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
</style>
@endpush

@section('content')

<!-- Welcome Hero Banner -->
<div class="dash-hero-banner">
    <div class="row align-items-center">
        <div class="col-lg-8">
            @php
                $hour = date('H');
                $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
            @endphp
            <h2 class="dash-hero-title">
                {{ $greeting }}, {{ auth('admin')->user()->name ?? 'Administrator' }}! 👋
            </h2>
            <p class="dash-hero-subtitle">
                Welcome to the <strong>{{ setting('site_title', 'Aurelia Estates') }}</strong> management hub. Review live listings, inquiries, and customer interactions in real time.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <div class="dash-hero-badge">
                    <i class="bi bi-calendar3 text-warning"></i>
                    <span>{{ date('l, F j, Y') }}</span>
                </div>
                <div class="dash-hero-badge">
                    <i class="bi bi-shield-check text-success"></i>
                    <span>Role: <strong>{{ auth('admin')->user()->roles->first()?->name ?? 'Administrator' }}</strong></span>
                </div>
                @if($stats['unread_submissions'] > 0)
                    <a href="{{ route('admin.submissions.index') }}" class="dash-hero-badge text-decoration-none bg-danger-subtle text-danger border-danger">
                        <span class="pulse-unread"></span>
                        <span><strong>{{ $stats['unread_submissions'] }}</strong> New Lead Inquiries</span>
                    </a>
                @endif
            </div>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                @can('properties.create')
                    <a href="{{ route('admin.properties.create') }}" class="btn btn-gold btn-sm px-3 py-2 fw-semibold rounded-3 shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add Property
                    </a>
                @endcan
                <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-light btn-sm px-3 py-2 fw-semibold rounded-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Live Site
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Primary KPI Cards Row -->
<div class="row g-3 mb-4">
    <!-- Properties Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div>
                <div class="kpi-top">
                    <div>
                        <div class="kpi-label">Properties</div>
                        <div class="kpi-value">{{ $stats['total_properties'] }}</div>
                    </div>
                    <div class="kpi-icon-box icon-gold">
                        <i class="bi bi-buildings-fill"></i>
                    </div>
                </div>
                <div class="text-muted small">
                    <span class="text-success fw-semibold"><i class="bi bi-check2-circle"></i> {{ $stats['active_properties'] }} Active</span>
                    <span class="mx-1">&bull;</span>
                    <span>{{ $stats['total_properties'] - $stats['active_properties'] }} Draft</span>
                </div>
            </div>
            <div class="kpi-footer">
                <span>Real Estate Listings</span>
                @can('properties.view')
                    <a href="{{ route('admin.properties.index') }}" class="kpi-link">
                        Manage <i class="bi bi-arrow-right"></i>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Featured Projects Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div>
                <div class="kpi-top">
                    <div>
                        <div class="kpi-label">Featured Projects</div>
                        <div class="kpi-value">{{ $stats['total_projects'] }}</div>
                    </div>
                    <div class="kpi-icon-box icon-blue">
                        <i class="bi bi-gem"></i>
                    </div>
                </div>
                <div class="text-muted small">
                    <span class="text-primary fw-semibold"><i class="bi bi-star-fill"></i> {{ $stats['active_projects'] }} Flagship</span>
                    <span class="mx-1">&bull;</span>
                    <span>Developments</span>
                </div>
            </div>
            <div class="kpi-footer">
                <span>Luxury Portfolios</span>
                @can('projects.view')
                    <a href="{{ route('admin.featured-project.index') }}" class="kpi-link">
                        Manage <i class="bi bi-arrow-right"></i>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Client Submissions Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div>
                <div class="kpi-top">
                    <div>
                        <div class="kpi-label">Client Inquiries</div>
                        <div class="kpi-value">{{ $stats['total_submissions'] }}</div>
                    </div>
                    <div class="kpi-icon-box icon-emerald">
                        <i class="bi bi-envelope-check-fill"></i>
                    </div>
                </div>
                <div class="text-muted small">
                    @if($stats['unread_submissions'] > 0)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                            <span class="pulse-unread"></span> {{ $stats['unread_submissions'] }} Pending Action
                        </span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            <i class="bi bi-check-all"></i> All Caught Up
                        </span>
                    @endif
                </div>
            </div>
            <div class="kpi-footer">
                <span>Buyer & Tenant Leads</span>
                @can('submissions.view')
                    <a href="{{ route('admin.submissions.index') }}" class="kpi-link">
                        View Leads <i class="bi bi-arrow-right"></i>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Newsletter Subscribers Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div>
                <div class="kpi-top">
                    <div>
                        <div class="kpi-label">Subscribers</div>
                        <div class="kpi-value">{{ $stats['total_subscribers'] }}</div>
                    </div>
                    <div class="kpi-icon-box icon-purple">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                </div>
                <div class="text-muted small">
                    <span class="text-secondary fw-semibold"><i class="bi bi-people"></i> Newsletter Audience</span>
                </div>
            </div>
            <div class="kpi-footer">
                <span>Audience Reach</span>
                @can('newsletters.view')
                    <a href="{{ route('admin.newsletters.index') }}" class="kpi-link">
                        View List <i class="bi bi-arrow-right"></i>
                    </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Secondary Mini Metrics Strip -->
<div class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon text-info">
                <i class="bi bi-journal-text"></i>
            </div>
            <div>
                <div class="text-muted small">Blog Articles</div>
                <div class="fw-bold fs-6">{{ $stats['total_posts'] }} Published</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon text-warning">
                <i class="bi bi-briefcase"></i>
            </div>
            <div>
                <div class="text-muted small">Services</div>
                <div class="fw-bold fs-6">{{ $stats['total_services'] }} Offered</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon text-primary">
                <i class="bi bi-person-badge"></i>
            </div>
            <div>
                <div class="text-muted small">Team Members</div>
                <div class="fw-bold fs-6">{{ $stats['total_team'] }} Professionals</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon text-success">
                <i class="bi bi-chat-heart"></i>
            </div>
            <div>
                <div class="text-muted small">Testimonials</div>
                <div class="fw-bold fs-6">{{ $stats['total_testimonials'] }} Reviews</div>
            </div>
        </div>
    </div>
</div>

@can('accounts.view')
<!-- Financial Accounts Quick Summary Banner -->
<div class="card p-3 mb-4 border-0 shadow-sm bg-light">
    <div class="row align-items-center g-3">
        <div class="col-12 col-lg-3">
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 bg-white rounded-3 shadow-sm text-warning">
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">ACCOUNTS & FINANCE</div>
                    <div class="fw-bold text-dark fs-6">Financial Overview</div>
                </div>
            </div>
        </div>
        <div class="col-4 col-lg-2">
            <span class="text-muted small d-block">Revenue / Income</span>
            <span class="fw-bold text-success">${{ number_format($stats['total_income'], 2) }}</span>
        </div>
        <div class="col-4 col-lg-2">
            <span class="text-muted small d-block">Expenses</span>
            <span class="fw-bold text-danger">${{ number_format($stats['total_expense'], 2) }}</span>
        </div>
        <div class="col-4 col-lg-2">
            <span class="text-muted small d-block">Staff Payroll</span>
            <span class="fw-bold text-warning">${{ number_format($stats['total_payroll'], 2) }}</span>
        </div>
        <div class="col-12 col-lg-3 text-lg-end">
            <a href="{{ route('admin.accounts.index') }}" class="btn btn-sm btn-gold rounded-pill px-3 fw-semibold">
                <i class="bi bi-graph-up me-1"></i> Full Accounts Hub
            </a>
        </div>
    </div>
</div>
@endcan

<!-- Main 2-Column Section -->
<div class="row g-4">
    
    <!-- Left Column: Inquiries & Latest Properties -->
    <div class="col-12 col-xl-8">
        
        <!-- Recent Leads & Inquiries -->
        <div class="dash-section-card">
            <div class="dash-section-header">
                <h5 class="dash-section-title">
                    <i class="bi bi-inbox text-warning"></i>
                    <span>Recent Client Inquiries</span>
                </h5>
                @can('submissions.view')
                    <a href="{{ route('admin.submissions.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        View All ({{ $stats['total_submissions'] }})
                    </a>
                @endcan
            </div>

            @if($recentSubmissions->count() > 0)
                <div class="table-responsive">
                    <table class="table lead-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Client Details</th>
                                <th>Inquiry Target</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSubmissions as $sub)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="lead-avatar">
                                                {{ strtoupper(substr($sub->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $sub->name }}</div>
                                                <div class="text-muted small" style="font-size: 0.76rem;">
                                                    <i class="bi bi-envelope me-1"></i>{{ $sub->email }}
                                                    @if($sub->phone)
                                                        <span class="ms-1">&bull; <i class="bi bi-telephone me-1"></i>{{ $sub->phone }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($sub->property)
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-building me-1 text-primary"></i> {{ Str::limit($sub->property->title, 20) }}
                                            </span>
                                        @elseif($sub->featuredProject)
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-gem me-1 text-warning"></i> {{ Str::limit($sub->featuredProject->title, 20) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                General Message
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$sub->is_read)
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1">
                                                <span class="pulse-unread"></span> New
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border rounded-pill px-2 py-1">
                                                <span class="pulse-read"></span> Read
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        {{ $sub->created_at->diffForHumans() }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.submissions.show', $sub->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" title="View Message">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-chat-square-dots fs-1 mb-2 d-block text-secondary opacity-50"></i>
                    <p class="mb-0">No client inquiries received yet.</p>
                </div>
            @endif
        </div>

        <!-- Latest Properties Showcase -->
        <div class="dash-section-card">
            <div class="dash-section-header">
                <h5 class="dash-section-title">
                    <i class="bi bi-buildings text-primary"></i>
                    <span>Latest Properties</span>
                </h5>
                @can('properties.view')
                    <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        All Properties
                    </a>
                @endcan
            </div>

            @if($recentProperties->count() > 0)
                <div class="table-responsive">
                    <table class="table lead-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Property</th>
                                <th>Type & Location</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentProperties as $prop)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($prop->image)
                                                <img src="{{ asset_url($prop->image) }}" alt="{{ $prop->title }}" class="prop-thumbnail">
                                            @else
                                                <div class="prop-thumbnail d-flex align-items-center justify-content-center bg-light text-muted">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark">{{ $prop->title }}</div>
                                                <div class="text-muted small" style="font-size: 0.74rem;">
                                                    {{ $prop->beds ?? 0 }} Beds &bull; {{ $prop->baths ?? 0 }} Baths &bull; {{ $prop->area ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge bg-light text-dark border mb-1">{{ $prop->type ?? 'Apartment' }}</div>
                                        <div class="text-muted small" style="font-size: 0.76rem;">
                                            <i class="bi bi-geo-alt"></i> {{ $prop->location }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">{{ $prop->price ? (is_numeric($prop->price) ? '$' . number_format((float)$prop->price) : $prop->price) : 'Price on Call' }}</span>
                                    </td>
                                    <td>
                                        @if($prop->is_active)
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @can('properties.edit')
                                            <a href="{{ route('admin.properties.edit', $prop->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-house-door fs-1 mb-2 d-block text-secondary opacity-50"></i>
                    <p class="mb-0">No properties added yet.</p>
                    @can('properties.create')
                        <a href="{{ route('admin.properties.create') }}" class="btn btn-sm btn-gold mt-2">
                            Add First Property
                        </a>
                    @endcan
                </div>
            @endif
        </div>

    </div>

    <!-- Right Column: Shortcuts & System Info -->
    <div class="col-12 col-xl-4">
        
        <!-- Quick Action Shortcuts -->
        <div class="dash-section-card">
            <h5 class="dash-section-title mb-3">
                <i class="bi bi-lightning-charge text-warning"></i>
                <span>Quick Actions</span>
            </h5>
            
            <div class="d-flex flex-column gap-2">
                @can('properties.create')
                    <a href="{{ route('admin.properties.create') }}" class="btn-quick-shortcut">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-plus-square action-icon"></i>
                            <span>Create New Property</span>
                        </span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                @endcan

                @can('accounts.create')
                    <a href="{{ route('admin.incomes.create') }}" class="btn-quick-shortcut">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-down-left-circle action-icon text-success"></i>
                            <span>Add Income Entry</span>
                        </span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('admin.expenses.create') }}" class="btn-quick-shortcut">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-up-right-circle action-icon text-danger"></i>
                            <span>Record Expense</span>
                        </span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                @endcan

                @can('payroll.create')
                    <a href="{{ route('admin.payrolls.create') }}" class="btn-quick-shortcut">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-cash-stack action-icon text-warning"></i>
                            <span>Disburse Staff Salary</span>
                        </span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                @endcan

                @can('projects.create')
                    <a href="{{ route('admin.featured-project.create') }}" class="btn-quick-shortcut">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-star action-icon"></i>
                            <span>Add Featured Project</span>
                        </span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                @endcan

                @can('homepage-settings.view')
                    <a href="{{ route('admin.homepage_settings.index') }}" class="btn-quick-shortcut">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-sliders action-icon"></i>
                            <span>Homepage Content</span>
                        </span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                @endcan

                @can('settings.view')
                    <a href="{{ route('admin.settings.site') }}" class="btn-quick-shortcut">
                        <span class="d-flex align-items-center gap-2">
                            <i class="bi bi-gear action-icon"></i>
                            <span>Site Settings & Logo</span>
                        </span>
                        <i class="bi bi-chevron-right text-muted small"></i>
                    </a>
                @endcan

                <a href="{{ url('/') }}" target="_blank" class="btn-quick-shortcut">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-globe action-icon text-success"></i>
                        <span>Visit Public Site</span>
                    </span>
                    <i class="bi bi-box-arrow-up-right text-muted small"></i>
                </a>
            </div>
        </div>

        <!-- Featured Projects Snapshot -->
        @if($recentProjects->count() > 0)
            <div class="dash-section-card">
                <div class="dash-section-header">
                    <h5 class="dash-section-title">
                        <i class="bi bi-gem text-warning"></i>
                        <span>Featured Projects</span>
                    </h5>
                    @can('projects.view')
                        <a href="{{ route('admin.featured-project.index') }}" class="small text-decoration-none">View All</a>
                    @endcan
                </div>

                <div class="d-flex flex-column gap-3">
                    @foreach($recentProjects as $project)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light">
                            <div class="d-flex align-items-center gap-2">
                                @if($project->image)
                                    <img src="{{ asset_url($project->image) }}" alt="{{ $project->title }}" class="prop-thumbnail">
                                @else
                                    <div class="prop-thumbnail d-flex align-items-center justify-content-center bg-white text-muted">
                                        <i class="bi bi-gem"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold small text-dark">{{ Str::limit($project->title, 20) }}</div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">
                                        <i class="bi bi-geo-alt"></i> {{ Str::limit($project->location, 18) }}
                                    </div>
                                </div>
                            </div>
                            @can('projects.edit')
                                <a href="{{ route('admin.featured-project.edit', $project->id) }}" class="btn btn-sm btn-link text-muted p-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endcan
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- System & Security Info -->
        <div class="dash-section-card">
            <h5 class="dash-section-title mb-3">
                <i class="bi bi-shield-lock text-success"></i>
                <span>System Overview</span>
            </h5>

            <div class="system-status-box">
                <div class="system-status-item">
                    <span class="text-muted">Application</span>
                    <span class="fw-semibold">{{ setting('site_title', 'Aurelia Estates') }}</span>
                </div>
                <div class="system-status-item">
                    <span class="text-muted">Framework</span>
                    <span class="fw-semibold">Laravel {{ app()->version() }}</span>
                </div>
                <div class="system-status-item">
                    <span class="text-muted">Environment</span>
                    <span class="badge bg-success-subtle text-success">{{ app()->environment() }}</span>
                </div>
                <div class="system-status-item">
                    <span class="text-muted">Current Admin</span>
                    <span class="fw-semibold text-truncate" style="max-width: 140px;">{{ auth('admin')->user()->name }}</span>
                </div>
                <div class="system-status-item">
                    <span class="text-muted">Security Guard</span>
                    <span class="text-success"><i class="bi bi-check-circle-fill"></i> Protected (admin)</span>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
