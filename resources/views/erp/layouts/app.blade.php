<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Enterprise ERP | {{ $currentCompany->trade_name ?? $currentCompany->legal_name ?? 'Aurelia ERP' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --erp-navy: #090e1a;
            --erp-dark: #121927;
            --erp-dark-card: #1b2436;
            --erp-border: rgba(255, 255, 255, 0.08);
            --erp-gold: #cba135;
            --erp-gold-hover: #b48c26;
            --erp-emerald: #10b981;
            --erp-sky: #0ea5e9;
            --erp-text-main: #f1f5f9;
            --erp-text-muted: #94a3b8;
            --erp-sidebar-width: 275px;
            --erp-light-bg: #f8fafc;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--erp-light-bg);
            color: #1e293b;
            margin: 0; padding: 0;
            overflow-x: hidden;
        }

        /* ── Modern Enterprise Sidebar ───────────────────── */
        #erpSidebar {
            width: var(--erp-sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: linear-gradient(180deg, var(--erp-navy) 0%, var(--erp-dark) 100%);
            color: #fff;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1050;
            overflow-y: auto;
            border-right: 1px solid var(--erp-border);
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.15) transparent;
        }

        #erpSidebar::-webkit-scrollbar { width: 5px; }
        #erpSidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }

        .erp-brand-header {
            padding: 22px 20px 18px 20px;
            border-bottom: 1px solid var(--erp-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .erp-brand-logo {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: 0.5px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .erp-brand-badge {
            font-size: 0.65rem;
            letter-spacing: 1px;
            padding: 3px 8px;
            background: rgba(203, 161, 53, 0.2);
            color: var(--erp-gold);
            border: 1px solid rgba(203, 161, 53, 0.4);
            border-radius: 4px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .erp-nav-section-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #64748b;
            padding: 16px 22px 6px 22px;
        }

        .erp-menu { list-style: none; padding: 0 12px; margin: 0; }
        .erp-menu-item { margin-bottom: 3px; }
        .erp-menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 14px;
            color: var(--erp-text-muted);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .erp-menu-link i.menu-icon {
            font-size: 1.05rem;
            color: #94a3b8;
            width: 22px;
            text-align: center;
            transition: color 0.2s ease;
        }
        .erp-menu-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.06);
        }
        .erp-menu-link:hover i.menu-icon {
            color: var(--erp-gold);
        }
        .erp-menu-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(203, 161, 53, 0.2) 0%, rgba(203, 161, 53, 0.05) 100%);
            border-left: 3px solid var(--erp-gold);
            font-weight: 600;
        }
        .erp-menu-link.active i.menu-icon {
            color: var(--erp-gold);
        }

        .erp-submenu {
            list-style: none;
            padding-left: 36px;
            padding-right: 8px;
            margin: 4px 0 8px 0;
        }
        .erp-submenu-link {
            display: block;
            padding: 6px 12px;
            color: #94a3b8;
            font-size: 0.82rem;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .erp-submenu-link:hover, .erp-submenu-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }
        .erp-submenu-link.active {
            color: var(--erp-gold);
            font-weight: 600;
        }

        /* ── Top Navbar ──────────────────────────────────── */
        #erpMain {
            margin-left: var(--erp-sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .erp-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .context-select-pill {
            display: flex;
            align-items: center;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 4px 12px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: all 0.2s;
        }
        .context-select-pill:hover {
            border-color: var(--erp-gold);
            background: #fff;
        }

        .erp-content {
            padding: 24px;
            flex-grow: 1;
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        .btn-erp-gold {
            background-color: var(--erp-gold);
            color: #000;
            font-weight: 600;
            border: none;
            transition: all 0.2s;
        }
        .btn-erp-gold:hover {
            background-color: var(--erp-gold-hover);
            color: #fff;
        }

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            background: #fff;
        }

        /* ── Mobile Sidebar Overlay Backdrop ─────────── */
        .erp-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(9, 14, 26, 0.68);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            z-index: 1045;
            transition: opacity 0.3s ease;
            opacity: 0;
        }
        .erp-sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ── Responsive Utility Classes ───────────────── */
        .erp-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 1.5rem;
        }

        .erp-scroll-ribbon {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            gap: 8px;
            padding-bottom: 6px;
        }
        .erp-scroll-ribbon::-webkit-scrollbar {
            display: none;
        }
        .erp-scroll-ribbon .btn {
            white-space: nowrap;
            flex-shrink: 0;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px;
        }

        @media (max-width: 991.98px) {
            #erpSidebar {
                transform: translateX(calc(-1 * var(--erp-sidebar-width)));
                box-shadow: none;
            }
            #erpSidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 35px rgba(0, 0, 0, 0.5);
            }
            #erpMain {
                margin-left: 0 !important;
            }
            .erp-navbar {
                padding: 10px 14px;
            }
            .erp-content {
                padding: 14px 12px;
            }
        }

        @media (max-width: 768px) {
            .table-responsive table {
                min-width: 580px;
            }
            .table {
                font-size: 0.82rem;
            }
            .table th, .table td {
                padding: 8px 10px;
                vertical-align: middle;
            }
            .table th {
                white-space: nowrap;
                font-size: 0.72rem;
                letter-spacing: 0.5px;
            }
        }

        @media (max-width: 576px) {
            .erp-content {
                padding: 12px 10px;
            }
            .context-select-pill {
                max-width: 160px;
                padding: 4px 8px;
                font-size: 0.78rem;
            }
            .context-select-pill .context-company-name {
                display: inline-block;
                max-width: 125px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                vertical-align: bottom;
            }
            .card-custom.p-4, .card-custom .card-body.p-4 {
                padding: 14px !important;
            }
            h2, .h2 { font-size: 1.35rem !important; }
            h3, .h3 { font-size: 1.15rem !important; }
            h4, .h4 { font-size: 1.05rem !important; }
            .table {
                font-size: 0.8rem;
            }
            .table th, .table td {
                padding: 6px 8px;
            }

            /* Responsive Page Header Rows across all ERP & CRM Views */
            .erp-header-row,
            .erp-content > .d-flex.justify-content-between.mb-4,
            .erp-content > .d-flex.align-items-center.justify-content-between.mb-4,
            .erp-content > .d-flex.flex-wrap.align-items-center.justify-content-between.mb-4,
            .erp-content > .container-fluid > .d-flex.justify-content-between.mb-4 {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
            }
            .erp-header-row > div:last-child,
            .erp-content > .d-flex.justify-content-between.mb-4 > div:last-child,
            .erp-content > .d-flex.align-items-center.justify-content-between.mb-4 > div:last-child,
            .erp-content > .d-flex.flex-wrap.align-items-center.justify-content-between.mb-4 > div:last-child,
            .erp-content > .container-fluid > .d-flex.justify-content-between.mb-4 > div:last-child {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                width: 100%;
            }
            .erp-header-row > div:last-child .btn,
            .erp-header-row > div:last-child a.btn,
            .erp-content > .d-flex.justify-content-between.mb-4 > div:last-child .btn,
            .erp-content > .d-flex.justify-content-between.mb-4 > div:last-child a.btn,
            .erp-content > .d-flex.align-items-center.justify-content-between.mb-4 > div:last-child .btn,
            .erp-content > .d-flex.align-items-center.justify-content-between.mb-4 > div:last-child a.btn,
            .erp-content > .d-flex.flex-wrap.align-items-center.justify-content-between.mb-4 > div:last-child .btn,
            .erp-content > .d-flex.flex-wrap.align-items-center.justify-content-between.mb-4 > div:last-child a.btn {
                flex: 1 1 auto;
                text-align: center;
            }

            /* Responsive Modals on Mobile */
            .modal-dialog {
                margin: 10px;
                max-width: calc(100vw - 20px) !important;
            }
            .modal-footer {
                flex-direction: column-reverse;
                gap: 8px;
            }
            .modal-footer .btn {
                width: 100%;
                margin: 0 !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="erpSidebarOverlay" class="erp-sidebar-overlay"></div>

    <!-- ERP Sidebar -->
    <nav id="erpSidebar">
        <div class="erp-brand-header">
            <a href="{{ route('erp.dashboard') }}" class="erp-brand-logo">
                <i class="bi bi-buildings text-warning"></i>
                <span>AURELIA <span style="color: var(--erp-gold)">ERP</span></span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="erp-brand-badge d-none d-sm-inline-block">Enterprise</span>
                <button type="button" class="btn btn-sm btn-outline-light border-0 d-lg-none p-1" id="erpSidebarCloseBtn" aria-label="Close sidebar">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
        </div>

        <div class="p-3">
            <div class="card p-2 border-0 bg-dark text-white rounded-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <small class="text-uppercase text-secondary" style="font-size: 0.68rem; font-weight: 700;">Active Branch</small>
                    <span class="badge bg-success" style="font-size: 0.65rem;">LIVE</span>
                </div>
                <div class="fw-bold text-truncate" style="font-size: 0.88rem; color: #f8fafc;">
                    <i class="bi bi-geo-alt-fill text-warning me-1"></i> {{ $currentBranch->name ?? 'Head Office' }}
                </div>
                <small class="text-truncate text-muted" style="font-size: 0.72rem;">
                    {{ $currentCompany->legal_name ?? 'Aurelia Estates' }}
                </small>
            </div>
        </div>

        <div class="erp-nav-section-title">Core Modules</div>
        <ul class="erp-menu">
            <li class="erp-menu-item">
                <a href="{{ route('erp.dashboard') }}" class="erp-menu-link {{ request()->routeIs('erp.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 menu-icon"></i>
                    <span>Executive Dashboard</span>
                </a>
            </li>

            <li class="erp-menu-item">
                <a href="{{ route('erp.ai-assistant.index') }}" class="erp-menu-link {{ request()->routeIs('erp.ai-assistant.*') ? 'active' : '' }}" style="{{ request()->routeIs('erp.ai-assistant.*') ? 'background: rgba(203, 161, 53, 0.2); border-left: 3px solid var(--erp-gold);' : '' }}">
                    <i class="bi bi-robot menu-icon text-warning"></i>
                    <span class="fw-bold">AI Database Assistant</span>
                    <span class="badge bg-warning text-dark ms-auto" style="font-size:0.62rem;">LIVE</span>
                </a>
            </li>

            <li class="erp-menu-item">
                <a href="#orgSubmenu" data-bs-toggle="collapse" class="erp-menu-link {{ request()->routeIs('erp.companies.*') || request()->routeIs('erp.branches.*') || request()->routeIs('erp.departments.*') || request()->routeIs('erp.designations.*') || request()->routeIs('erp.cost-centers.*') || request()->routeIs('erp.fiscal-years.*') || request()->routeIs('erp.document-sequences.*') || request()->routeIs('erp.audit-logs.*') ? 'active' : '' }} d-flex justify-content-between">
                    <div>
                        <i class="bi bi-diagram-3-fill menu-icon"></i>
                        <span>Organization</span>
                    </div>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('erp.companies.*') || request()->routeIs('erp.branches.*') || request()->routeIs('erp.departments.*') || request()->routeIs('erp.designations.*') || request()->routeIs('erp.cost-centers.*') || request()->routeIs('erp.fiscal-years.*') || request()->routeIs('erp.document-sequences.*') || request()->routeIs('erp.audit-logs.*') ? 'show' : '' }}" id="orgSubmenu">
                    <ul class="erp-submenu">
                        <li><a href="{{ route('erp.companies.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.companies.*') ? 'active' : '' }}">Companies & Legal Entities</a></li>
                        <li><a href="{{ route('erp.branches.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.branches.*') ? 'active' : '' }}">Branches & Operating Units</a></li>
                        <li><a href="{{ route('erp.departments.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.departments.*') ? 'active' : '' }}">Departments</a></li>
                        <li><a href="{{ route('erp.designations.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.designations.*') ? 'active' : '' }}">Designations & Roles</a></li>
                        <li><a href="{{ route('erp.cost-centers.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.cost-centers.*') ? 'active' : '' }}">Cost Centres</a></li>
                        <li><a href="{{ route('erp.fiscal-years.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.fiscal-years.*') ? 'active' : '' }}">Fiscal Periods & Years</a></li>
                        <li><a href="{{ route('erp.document-sequences.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.document-sequences.*') ? 'active' : '' }}">Document Sequences (Prefixes)</a></li>
                        <li><a href="{{ route('erp.audit-logs.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.audit-logs.*') ? 'active' : '' }}">Immutable Audit Logs</a></li>
                    </ul>
                </div>
            </li>
        </ul>

        <div class="erp-nav-section-title">Real Estate Operations</div>
        <ul class="erp-menu">
            <li class="erp-menu-item">
                <a href="#crmSubmenu" data-bs-toggle="collapse" class="erp-menu-link {{ request()->routeIs('erp.leads.*') || request()->routeIs('erp.site-visits.*') || request()->routeIs('erp.customers.*') || request()->routeIs('erp.crm-integrations.*') ? 'active' : '' }} d-flex justify-content-between">
                    <div>
                        <i class="bi bi-person-lines-fill menu-icon"></i>
                        <span>CRM & Lead Engine</span>
                    </div>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('erp.leads.*') || request()->routeIs('erp.site-visits.*') || request()->routeIs('erp.customers.*') || request()->routeIs('erp.crm-integrations.*') ? 'show' : '' }}" id="crmSubmenu">
                    <ul class="erp-submenu">
                        <li><a href="{{ route('erp.leads.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.leads.index') ? 'active' : '' }}">Leads Pipeline</a></li>
                        <li><a href="{{ route('erp.leads.create') }}" class="erp-submenu-link {{ request()->routeIs('erp.leads.create') ? 'active' : '' }}">New Lead Intake</a></li>
                        <li><a href="{{ route('erp.site-visits.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.site-visits.*') ? 'active' : '' }}">Site Visits Schedule</a></li>
                        <li><a href="{{ route('erp.customers.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.customers.*') ? 'active' : '' }}">Customer Master (KYC)</a></li>
                        <li><a href="{{ route('erp.crm-integrations.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.crm-integrations.*') ? 'active' : '' }}"><i class="bi bi-meta text-primary me-1"></i> Meta & WhatsApp Sync</a></li>
                    </ul>
                </div>
            </li>
            <li class="erp-menu-item">
                <a href="#landSubmenu" data-bs-toggle="collapse" class="erp-menu-link {{ request()->routeIs('erp.land-parcels.*') || request()->routeIs('erp.land-feasibility.*') ? 'active' : '' }} d-flex justify-content-between">
                    <div>
                        <i class="bi bi-map-fill menu-icon"></i>
                        <span>Land & Feasibility</span>
                    </div>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('erp.land-parcels.*') || request()->routeIs('erp.land-feasibility.*') ? 'show' : '' }}" id="landSubmenu">
                    <ul class="erp-submenu">
                        <li><a href="{{ route('erp.land-parcels.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.land-parcels.index') ? 'active' : '' }}">Land Parcels Bank</a></li>
                        <li><a href="{{ route('erp.land-parcels.create') }}" class="erp-submenu-link {{ request()->routeIs('erp.land-parcels.create') ? 'active' : '' }}">New Parcel Intake</a></li>
                    </ul>
                </div>
            </li>
            <li class="erp-menu-item">
                <a href="#devSubmenu" data-bs-toggle="collapse" class="erp-menu-link {{ request()->routeIs('erp.dev-projects.*') || request()->routeIs('erp.dev-bookings.*') ? 'active' : '' }} d-flex justify-content-between">
                    <div>
                        <i class="bi bi-house-check-fill menu-icon"></i>
                        <span>Developer Sales</span>
                    </div>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('erp.dev-projects.*') || request()->routeIs('erp.dev-bookings.*') ? 'show' : '' }}" id="devSubmenu">
                    <ul class="erp-submenu">
                        <li><a href="{{ route('erp.dev-projects.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.dev-projects.*') ? 'active' : '' }}">Projects & Towers</a></li>
                        <li><a href="{{ route('erp.dev-bookings.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.dev-bookings.index') ? 'active' : '' }}">Sales Bookings Register</a></li>
                        <li><a href="{{ route('erp.dev-bookings.create') }}" class="erp-submenu-link {{ request()->routeIs('erp.dev-bookings.create') ? 'active' : '' }}">New Booking Contract</a></li>
                    </ul>
                </div>
            </li>
            <li class="erp-menu-item">
                <a href="#agencySubmenu" data-bs-toggle="collapse" class="erp-menu-link {{ request()->routeIs('erp.agency-listings.*') || request()->routeIs('erp.agency-deals.*') || request()->routeIs('erp.agency-leases.*') ? 'active' : '' }} d-flex justify-content-between">
                    <div>
                        <i class="bi bi-briefcase-fill menu-icon"></i>
                        <span>Agency & Brokerage</span>
                    </div>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('erp.agency-listings.*') || request()->routeIs('erp.agency-deals.*') || request()->routeIs('erp.agency-leases.*') ? 'show' : '' }}" id="agencySubmenu">
                    <ul class="erp-submenu">
                        <li><a href="{{ route('erp.agency-listings.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.agency-listings.*') ? 'active' : '' }}">Mandate Listings</a></li>
                        <li><a href="{{ route('erp.agency-deals.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.agency-deals.*') ? 'active' : '' }}">Brokerage Deals</a></li>
                        <li><a href="{{ route('erp.agency-leases.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.agency-leases.*') ? 'active' : '' }}">Tenant Leases</a></li>
                    </ul>
                </div>
            </li>
            <li class="erp-menu-item">
                <a href="#conSubmenu" data-bs-toggle="collapse" class="erp-menu-link {{ request()->routeIs('erp.con-vendors.*') || request()->routeIs('erp.con-boq.*') || request()->routeIs('erp.con-requisitions.*') || request()->routeIs('erp.con-pos.*') || request()->routeIs('erp.con-grns.*') || request()->routeIs('erp.con-stocks.*') ? 'active' : '' }} d-flex justify-content-between">
                    <div>
                        <i class="bi bi-truck-flatbed menu-icon"></i>
                        <span>Construction & Procurement</span>
                    </div>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('erp.con-vendors.*') || request()->routeIs('erp.con-boq.*') || request()->routeIs('erp.con-requisitions.*') || request()->routeIs('erp.con-pos.*') || request()->routeIs('erp.con-grns.*') || request()->routeIs('erp.con-stocks.*') ? 'show' : '' }}" id="conSubmenu">
                    <ul class="erp-submenu">
                        <li><a href="{{ route('erp.con-vendors.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.con-vendors.*') ? 'active' : '' }}">Vendors & Suppliers</a></li>
                        <li><a href="{{ route('erp.con-boq.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.con-boq.*') ? 'active' : '' }}">Project BOQ Budget</a></li>
                        <li><a href="{{ route('erp.con-requisitions.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.con-requisitions.*') ? 'active' : '' }}">Site Requisitions</a></li>
                        <li><a href="{{ route('erp.con-pos.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.con-pos.*') ? 'active' : '' }}">Purchase Orders (PO)</a></li>
                        <li><a href="{{ route('erp.con-grns.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.con-grns.*') ? 'active' : '' }}">Goods Receipts (GRN)</a></li>
                        <li><a href="{{ route('erp.con-stocks.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.con-stocks.*') ? 'active' : '' }}">Site Stock Inventory</a></li>
                    </ul>
                </div>
            </li>
            <li class="erp-menu-item">
                <a href="#accSubmenu" data-bs-toggle="collapse" class="erp-menu-link {{ request()->routeIs('erp.acc-coa.*') || request()->routeIs('erp.acc-journals.*') || request()->routeIs('erp.acc-reports.*') ? 'active' : '' }} d-flex justify-content-between">
                    <div>
                        <i class="bi bi-journal-bookmark-fill menu-icon"></i>
                        <span>Double-Entry Accounts</span>
                    </div>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('erp.acc-coa.*') || request()->routeIs('erp.acc-journals.*') || request()->routeIs('erp.acc-reports.*') ? 'show' : '' }}" id="accSubmenu">
                    <ul class="erp-submenu">
                        <li><a href="{{ route('erp.acc-coa.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.acc-coa.*') ? 'active' : '' }}">Chart of Accounts (COA)</a></li>
                        <li><a href="{{ route('erp.acc-journals.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.acc-journals.*') ? 'active' : '' }}">Journal Vouchers</a></li>
                        <li><a href="{{ route('erp.acc-reports.trial-balance') }}" class="erp-submenu-link {{ request()->routeIs('erp.acc-reports.trial-balance') ? 'active' : '' }}">Trial Balance</a></li>
                        <li><a href="{{ route('erp.acc-reports.profit-loss') }}" class="erp-submenu-link {{ request()->routeIs('erp.acc-reports.profit-loss') ? 'active' : '' }}">Profit & Loss (P&L)</a></li>
                        <li><a href="{{ route('erp.acc-reports.balance-sheet') }}" class="erp-submenu-link {{ request()->routeIs('erp.acc-reports.balance-sheet') ? 'active' : '' }}">Balance Sheet</a></li>
                    </ul>
                </div>
            </li>
            <!-- Phase 8: HR & Payroll -->
            <li class="erp-menu-item">
                <a href="#hrSubmenu" data-bs-toggle="collapse" class="erp-menu-link {{ request()->routeIs('erp.hr-employees.*') || request()->routeIs('erp.hr-payrolls.*') || request()->routeIs('erp.hr-labor.*') || request()->routeIs('erp.departments.*') || request()->routeIs('erp.designations.*') ? 'active' : '' }} d-flex justify-content-between">
                    <div>
                        <i class="bi bi-person-badge-fill menu-icon"></i>
                        <span>HR, Workforce & Payroll</span>
                    </div>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('erp.hr-employees.*') || request()->routeIs('erp.hr-payrolls.*') || request()->routeIs('erp.hr-labor.*') || request()->routeIs('erp.departments.*') || request()->routeIs('erp.designations.*') ? 'show' : '' }}" id="hrSubmenu">
                    <ul class="erp-submenu">
                        <li><a href="{{ route('erp.hr-employees.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.hr-employees.index') ? 'active' : '' }}">Permanent Staff (Employees)</a></li>
                        <li><a href="{{ route('erp.hr-employees.create') }}" class="erp-submenu-link {{ request()->routeIs('erp.hr-employees.create') ? 'active' : '' }}">Onboard New Employee</a></li>
                        <li><a href="{{ route('erp.hr-payrolls.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.hr-payrolls.*') ? 'active' : '' }}">Monthly Staff Payroll</a></li>
                        <li><a href="{{ route('erp.hr-labor.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.hr-labor.index') ? 'active' : '' }}">Site Labor Registry</a></li>
                        <li><a href="{{ route('erp.hr-labor.muster') }}" class="erp-submenu-link {{ request()->routeIs('erp.hr-labor.muster*') ? 'active' : '' }}">Daily Labor Muster Sheet</a></li>
                        <li><a href="{{ route('erp.departments.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.departments.*') ? 'active' : '' }}">Departments</a></li>
                        <li><a href="{{ route('erp.designations.index') }}" class="erp-submenu-link {{ request()->routeIs('erp.designations.*') ? 'active' : '' }}">Designations & Roles</a></li>
                    </ul>
                </div>
            </li>
            <li class="erp-menu-item">
                <a href="#" class="erp-menu-link text-secondary" title="Phase 9">
                    <i class="bi bi-bar-chart-line-fill menu-icon"></i>
                    <span>Reports & Financial BI</span>
                    <span class="badge bg-secondary ms-auto" style="font-size:0.6rem;">P9</span>
                </a>
            </li>
            <li class="erp-menu-item">
                <a href="#" class="erp-menu-link text-secondary" title="Phase 10">
                    <i class="bi bi-shield-check menu-icon"></i>
                    <span>Approval Workflows</span>
                    <span class="badge bg-secondary ms-auto" style="font-size:0.6rem;">P10</span>
                </a>
            </li>
        </ul>

        <div class="p-3 mt-4">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-arrow-left"></i> Return to Website CMS
            </a>
        </div>
    </nav>

    <!-- Main Content Shell -->
    <div id="erpMain">
        <!-- Top Navbar -->
        <header class="erp-navbar">
            <div class="d-flex align-items-center gap-2 gap-sm-3">
                <button class="btn btn-sm btn-light d-lg-none" id="erpToggleBtn" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <!-- Company & Branch Context Switcher Dropdown -->
                <div class="dropdown">
                    <button class="context-select-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ $currentCompany->legal_name ?? 'Default Entity' }} ({{ $currentBranch->name ?? 'All Branches' }})">
                        <i class="bi bi-buildings-fill text-warning me-1"></i>
                        <span class="context-company-name">{{ $currentCompany->trade_name ?? $currentCompany->legal_name ?? 'Entity' }}</span>
                        <span class="mx-1 text-muted d-none d-sm-inline">/</span>
                        <span class="text-primary d-none d-sm-inline">{{ $currentBranch->name ?? 'All Branches' }}</span>
                    </button>
                    <div class="dropdown-menu shadow-lg p-3" style="min-width: 290px; max-width: 90vw;">
                        <h6 class="dropdown-header px-0 text-uppercase text-secondary fw-bold" style="font-size: 0.72rem;">Switch Active Company & Branch</h6>
                        <form action="{{ route('erp.switch-context') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">Company / Entity</label>
                                <select name="company_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach($accessibleCompanies as $comp)
                                        <option value="{{ $comp->id }}" {{ $currentCompany && $currentCompany->id == $comp->id ? 'selected' : '' }}>
                                            {{ $comp->legal_name }} ({{ $comp->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Operating Branch</label>
                                <select name="branch_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach($accessibleBranches as $br)
                                        <option value="{{ $br->id }}" {{ $currentBranch && $currentBranch->id == $br->id ? 'selected' : '' }}>
                                            {{ $br->name }} {{ $br->is_head_office ? '(HQ)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <span class="badge bg-light text-dark border px-2 py-1 d-none d-md-inline-block">
                    <i class="bi bi-cash-stack text-success me-1"></i> Base: {{ $currentCompany->base_currency ?? 'BDT' }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-2 gap-sm-3">
                <a href="{{ route('erp.ai-assistant.index') }}" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1 shadow-sm px-2 px-sm-3" style="border-color: var(--erp-gold); color: #946c00; font-weight: 600;">
                    <i class="bi bi-robot text-warning"></i>
                    <span class="d-none d-sm-inline">Ask AI Agent</span>
                    <span class="d-sm-none">AI</span>
                    <span class="badge bg-warning text-dark ms-1 d-none d-md-inline" style="font-size:0.6rem;">NEW</span>
                </a>

                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center gap-1">
                    <i class="bi bi-grid-fill"></i> CMS Backend
                </a>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()?->name ?? 'Admin') }}&background=090e1a&color=cba135" class="rounded-circle" width="34" height="34" alt="User">
                        <span class="small fw-semibold d-none d-md-inline">{{ Auth::guard('admin')->user()?->name ?? 'Administrator' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-layout-text-sidebar-reverse me-2"></i> Website CMS</a></li>
                        <li><a class="dropdown-item" href="{{ route('erp.audit-logs.index') }}"><i class="bi bi-clock-history me-2"></i> My Audit Trail</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="erp-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <strong>Please check the following issues:</strong>
                    <ul class="mb-0 mt-1 small">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @if(!request()->routeIs('erp.ai-assistant.index'))
    <!-- Floating Ask AI Button -->
    <button type="button" class="btn btn-warning position-fixed shadow-lg d-flex align-items-center gap-2 rounded-pill px-3 py-2" id="floatingAiBtn" data-bs-toggle="offcanvas" data-bs-target="#aiDrawerOffcanvas" aria-controls="aiDrawerOffcanvas" style="bottom: 24px; right: 24px; z-index: 1040; background-color: #cba135; border: 2px solid #b48c26; color: #000; font-weight: 700;">
        <i class="bi bi-robot fs-5 text-dark"></i>
        <span class="d-none d-sm-inline">Ask AI Agent</span>
    </button>

    <!-- Offcanvas Quick AI Assistant Drawer -->
    <div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="aiDrawerOffcanvas" aria-labelledby="aiDrawerOffcanvasLabel" style="width: 440px; max-width: 95vw; z-index: 1060;">
        <div class="offcanvas-header text-white" style="background: linear-gradient(135deg, #090e1a 0%, #1b2436 100%);">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(203, 161, 53, 0.2); border: 1px solid #cba135;">
                    <i class="bi bi-robot text-warning"></i>
                </div>
                <div>
                    <h6 class="offcanvas-title fw-bold mb-0 text-white" id="aiDrawerOffcanvasLabel">Aurelia AI Assistant</h6>
                    <small class="text-secondary" style="font-size: 0.68rem;">Live Database Intelligence</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('erp.ai-assistant.index') }}" class="btn btn-xs btn-outline-warning py-1 px-2 text-decoration-none" title="Open Fullscreen" style="font-size:0.75rem;">
                    <i class="bi bi-arrows-fullscreen"></i> Full View
                </a>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
        </div>
        <div class="offcanvas-body d-flex flex-column p-3 bg-light" style="overflow-y: auto;">
            <div class="flex-grow-1 overflow-y-auto mb-3" id="drawerChatStream" style="max-height: calc(100vh - 200px);">
                <div class="p-3 bg-white rounded-3 shadow-sm border mb-3">
                    <p class="small mb-1 fw-semibold text-dark">কীভাবে সাহায্য করতে পারি?</p>
                    <p class="small text-muted mb-2">যেকোনো প্রশ্ন লিখুন অথবা নিচের বোতামে চাপ দিন:</p>
                    <div class="d-flex flex-wrap gap-1">
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-1 px-2 drawer-chip" style="font-size: 0.72rem;" data-query="আজকের সেল কয়টা হয়েছে?">আজকের সেল?</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-1 px-2 drawer-chip" style="font-size: 0.72rem;" data-query="আজকের লাভ কত?">আজকের লাভ?</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-1 px-2 drawer-chip" style="font-size: 0.72rem;" data-query="কত টাকা বকেয়া আছে?">বকেয়া কত?</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-1 px-2 drawer-chip" style="font-size: 0.72rem;" data-query="ইউনিট কয়টা বাকি আছে?">খালি ইউনিট?</button>
                    </div>
                </div>
            </div>

            <!-- Drawer Input -->
            <form id="drawerChatForm" class="mt-auto">
                <div class="input-group">
                    <input type="text" id="drawerQueryInput" class="form-control form-control-sm shadow-none" placeholder="প্রশ্ন লিখুন (e.g. ajker sell koyta)..." required autocomplete="off">
                    <button class="btn btn-warning btn-sm" type="submit" id="drawerSendBtn" style="background-color: #cba135; border-color: #cba135; color: #000;">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Marked.js for fast Markdown table & typography rendering -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('erpToggleBtn');
            const sidebar = document.getElementById('erpSidebar');
            const overlay = document.getElementById('erpSidebarOverlay');
            const closeBtn = document.getElementById('erpSidebarCloseBtn');

            function openSidebar() {
                if (sidebar) sidebar.classList.add('open');
                if (overlay) overlay.classList.add('show');
                if (window.innerWidth < 992) {
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (sidebar && sidebar.classList.contains('open')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeSidebar);
            }

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            });

            // Auto-close on mobile when navigating non-collapse links
            document.querySelectorAll('#erpSidebar a:not([data-bs-toggle])').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        closeSidebar();
                    }
                });
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    closeSidebar();
                }
            });

            // Drawer AI Logic
            const drawerForm = document.getElementById('drawerChatForm');
            const drawerInput = document.getElementById('drawerQueryInput');
            const drawerStream = document.getElementById('drawerChatStream');
            const drawerSendBtn = document.getElementById('drawerSendBtn');

            if (drawerForm) {
                document.querySelectorAll('.drawer-chip').forEach(btn => {
                    btn.addEventListener('click', function() {
                        drawerInput.value = this.getAttribute('data-query');
                        drawerForm.dispatchEvent(new Event('submit'));
                    });
                });

                drawerForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const q = drawerInput.value.trim();
                    if (!q) return;

                    drawerStream.insertAdjacentHTML('beforeend', `
                        <div class="d-flex justify-content-end mb-2">
                            <div class="p-2 rounded-3 text-white small" style="background: #090e1a; max-width: 85%;">
                                ${q}
                            </div>
                        </div>
                    `);
                    drawerInput.value = '';
                    drawerInput.disabled = true;
                    drawerSendBtn.disabled = true;

                    const loadId = 'd_load_' + Date.now();
                    drawerStream.insertAdjacentHTML('beforeend', `
                        <div class="d-flex mb-2" id="${loadId}">
                            <div class="p-2 rounded-3 bg-white border small text-muted">
                                <span class="spinner-border spinner-border-sm me-1"></span> ডাটা অনুসন্ধান হচ্ছে...
                            </div>
                        </div>
                    `);
                    drawerStream.scrollTop = drawerStream.scrollHeight;

                    fetch("{{ route('erp.ai-assistant.query') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ query: q })
                    })
                    .then(r => r.json())
                    .then(res => {
                        const el = document.getElementById(loadId);
                        if (el) el.remove();
                        const ans = res.data ? res.data.answer : 'কোনো ফলাফল পাওয়া যায়নি।';
                        const rendered = typeof marked !== 'undefined' ? marked.parse(ans) : ans;
                        drawerStream.insertAdjacentHTML('beforeend', `
                            <div class="d-flex mb-3">
                                <div class="p-2 rounded-3 bg-white border shadow-sm small text-dark ai-rendered-content" style="max-width: 95%; line-height: 1.4; overflow-x: auto;">
                                    ${rendered}
                                </div>
                            </div>
                        `);
                        drawerStream.scrollTop = drawerStream.scrollHeight;
                    })
                    .catch(() => {
                        const el = document.getElementById(loadId);
                        if (el) el.remove();
                        drawerStream.insertAdjacentHTML('beforeend', `
                            <div class="d-flex mb-2">
                                <div class="p-2 rounded-3 bg-danger bg-opacity-10 text-danger border border-danger small">
                                    ত্রুটি হয়েছে, অনুগ্রহ করে আবার চেষ্টা করুন।
                                </div>
                            </div>
                        `);
                    })
                    .finally(() => {
                        drawerInput.disabled = false;
                        drawerSendBtn.disabled = false;
                        drawerInput.focus();
                    });
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

