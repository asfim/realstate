<nav id="sidebar">
    <div class="sidebar-header">
        <h3>{{ strtoupper(setting('site_title', 'AURELIA ESTATES')) }}</h3>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard
            </a>
        </li>

        {{-- ── ERP Real Estate Core ────────────────────── --}}
        <li class="sidebar-heading px-3 mt-3 mb-1 text-uppercase text-xs" style="color: var(--primary); font-size: 0.72rem; letter-spacing: 1px; font-weight: 700;">
            <i class="bi bi-shield-check me-1"></i> ERP Real Estate Core
        </li>

        @can('erp.dashboard.view')
            <li>
                <a href="{{ route('erp.dashboard') }}" class="{{ request()->routeIs('erp.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 text-warning"></i>
                    ERP Dashboard
                </a>
            </li>
        @endcan

        @can('erp.projects.view')
            <li>
                <a href="#erpProjectsSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('erp.projects.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-buildings"></i> ERP Projects</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('erp.projects.*') ? 'show' : '' }} sidebar-submenu"
                    id="erpProjectsSubmenu">
                    <li>
                        <a href="{{ route('erp.projects.index') }}" class="{{ request()->routeIs('erp.projects.index') ? 'active' : '' }}">
                            <i class="bi bi-list-ul"></i> All Projects
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('erp.projects.create') }}" class="{{ request()->routeIs('erp.projects.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-circle"></i> Create Project
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @can('erp.units.view')
            <li>
                <a href="#erpUnitsSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('erp.units.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-grid-3x3-gap"></i> Unit Inventory</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('erp.units.*') ? 'show' : '' }} sidebar-submenu"
                    id="erpUnitsSubmenu">
                    <li>
                        <a href="{{ route('erp.units.index') }}" class="{{ request()->routeIs('erp.units.index') ? 'active' : '' }}">
                            <i class="bi bi-collection"></i> Inventory Master
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('erp.units.create') }}" class="{{ request()->routeIs('erp.units.create') ? 'active' : '' }}">
                            <i class="bi bi-plus-square"></i> Add New Unit
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @can('erp.companies.view')
            <li>
                <a href="{{ route('erp.companies.index') }}" class="{{ request()->routeIs('erp.companies.*') ? 'active' : '' }}">
                    <i class="bi bi-building-gear"></i>
                    Companies & Branches
                </a>
            </li>
        @endcan

        {{-- Sales & Bookings --}}
        @can('erp.bookings.view')
            <li>
                <a href="#erpSalesSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('erp.bookings.*') || request()->routeIs('erp.customers.*') || request()->routeIs('erp.payments.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-file-earmark-check"></i> Sales & Bookings</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('erp.bookings.*') || request()->routeIs('erp.customers.*') || request()->routeIs('erp.payments.*') ? 'show' : '' }} sidebar-submenu"
                    id="erpSalesSubmenu">
                    <li>
                        <a href="{{ route('erp.customers.index') }}" class="{{ request()->routeIs('erp.customers.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i> Customers / Buyers
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('erp.bookings.index') }}" class="{{ request()->routeIs('erp.bookings.*') ? 'active' : '' }}">
                            <i class="bi bi-card-checklist"></i> Sales Bookings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('erp.payments.index') }}" class="{{ request()->routeIs('erp.payments.*') ? 'active' : '' }}">
                            <i class="bi bi-receipt"></i> Payment Receipts
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        {{-- Accounts & Finance (Double Entry & P&L) --}}
        @can('erp.accounting.view')
            <li>
                <a href="#erpAccountsSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('erp.accounting.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-cash-coin"></i> Accounts & Finance</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('erp.accounting.*') ? 'show' : '' }} sidebar-submenu"
                    id="erpAccountsSubmenu">
                    <li>
                        <a href="{{ route('erp.accounting.profit-and-loss') }}" class="{{ request()->routeIs('erp.accounting.profit-and-loss') ? 'active' : '' }}">
                            <i class="bi bi-graph-up-arrow text-success"></i> Profit & Loss (P&L)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('erp.accounting.trial-balance') }}" class="{{ request()->routeIs('erp.accounting.trial-balance') ? 'active' : '' }}">
                            <i class="bi bi-scale"></i> Trial Balance
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('erp.accounting.balance-sheet') }}" class="{{ request()->routeIs('erp.accounting.balance-sheet') ? 'active' : '' }}">
                            <i class="bi bi-bank"></i> Balance Sheet
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('erp.accounting.accounts') }}" class="{{ request()->routeIs('erp.accounting.accounts') ? 'active' : '' }}">
                            <i class="bi bi-diagram-3"></i> Chart of Accounts
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('erp.accounting.journals') }}" class="{{ request()->routeIs('erp.accounting.journals') ? 'active' : '' }}">
                            <i class="bi bi-journal-text"></i> Vouchers & Journals
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('erp.accounting.customer-ledger') }}" class="{{ request()->routeIs('erp.accounting.customer-ledger') ? 'active' : '' }}">
                            <i class="bi bi-person-lines-fill"></i> Customer Subledger
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        {{-- Staff & HRM --}}
        @can('erp.hrm.view')
            <li>
                <a href="#erpHrmSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('erp.employees.*') || request()->routeIs('erp.payroll.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-person-badge"></i> Staff & HRM</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('erp.employees.*') || request()->routeIs('erp.payroll.*') ? 'show' : '' }} sidebar-submenu"
                    id="erpHrmSubmenu">
                    <li>
                        <a href="{{ route('erp.employees.index') }}" class="{{ request()->routeIs('erp.employees.*') ? 'active' : '' }}">
                            <i class="bi bi-person-lines-fill"></i> Staff Directory
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('erp.payroll.index') }}" class="{{ request()->routeIs('erp.payroll.*') ? 'active' : '' }}">
                            <i class="bi bi-wallet2"></i> Monthly Payroll
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        <li class="sidebar-heading px-3 mt-3 mb-1 text-uppercase text-xs" style="color: var(--text-light); font-size: 0.72rem; letter-spacing: 1px; font-weight: 600;">
            CMS & Public Website
        </li>

        @can('homepage-settings.view')
            <li>
                <a href="{{ route('admin.homepage_settings.index') }}"
                    class="{{ request()->routeIs('admin.homepage_settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i>
                    Homepage Settings
                </a>
            </li>
        @endcan

        @can('projects.view')
            <li>
                <a href="#projectsSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('admin.featured-project.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-star-fill"></i> Projects</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('admin.featured-project.*') ? 'show' : '' }} sidebar-submenu"
                    id="projectsSubmenu">
                    <li>
                        <a href="{{ route('admin.featured-project.index') }}"
                            class="{{ request()->routeIs('admin.featured-project.index') || request()->routeIs('admin.featured-project.create') || request()->routeIs('admin.featured-project.edit') ? 'active' : '' }}">
                            <i class="bi bi-list-ul"></i> All Projects
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.featured-project.details.index') }}"
                            class="{{ request()->routeIs('admin.featured-project.details.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text"></i> Project Details
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @can('properties.view')
            <li>
                <a href="#propertiesSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('admin.properties.*') || request()->routeIs('admin.property-types.*') || request()->routeIs('admin.properties.manage.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-building"></i> Properties</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('admin.properties.*') || request()->routeIs('admin.property-types.*') || request()->routeIs('admin.properties.manage.*') ? 'show' : '' }} sidebar-submenu"
                    id="propertiesSubmenu">
                    <li>
                        <a href="{{ route('admin.properties.index') }}"
                            class="{{ request()->routeIs('admin.properties.index') || request()->routeIs('admin.properties.edit') ? 'active' : '' }}">
                            <i class="bi bi-list-ul"></i> All Properties
                        </a>
                    </li>
                    @can('properties.create')
                        <li>
                            <a href="{{ route('admin.properties.create') }}"
                                class="{{ request()->routeIs('admin.properties.create') ? 'active' : '' }}">
                                <i class="bi bi-plus-circle"></i> Add Property
                            </a>
                        </li>
                    @endcan
                    <li>
                        <a href="{{ route('admin.properties.manage.index') }}"
                            class="{{ request()->routeIs('admin.properties.manage.*') ? 'active' : '' }}">
                            <i class="bi bi-gear-fill"></i> Manage Property
                        </a>
                    </li>
                    @can('property-types.view')
                        <li>
                            <a href="{{ route('admin.property-types.index') }}"
                                class="{{ request()->routeIs('admin.property-types.*') ? 'active' : '' }}">
                                <i class="bi bi-tags"></i> Property Types
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan

        @can('submissions.view')
            @php
                $unreadSubmissionsCount = \App\Models\Submission::where('is_read', false)->count();
            @endphp
            <li>
                <a href="{{ route('admin.submissions.index') }}"
                    class="{{ request()->routeIs('admin.submissions.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-inbox-fill"></i>
                        Submissions
                    </span>
                    @if ($unreadSubmissionsCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $unreadSubmissionsCount }}</span>
                    @endif
                </a>
            </li>
        @endcan

        @can('newsletters.view')
            @php
                $subscribersCount = \App\Models\Subscriber::count();
            @endphp
            <li>
                <a href="{{ route('admin.newsletters.index') }}"
                    class="{{ request()->routeIs('admin.newsletters.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-envelope-check-fill"></i>
                        Newsletters
                    </span>
                    @if ($subscribersCount > 0)
                        <span class="badge bg-gold rounded-pill">{{ $subscribersCount }}</span>
                    @endif
                </a>
            </li>
        @endcan

        @if (auth('admin')->user()->can('services.view') || auth('admin')->user()->can('amenities.view'))
            <li>
                <a href="#servicesSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('admin.services.*') || request()->routeIs('admin.amenities.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-briefcase-fill"></i> Services</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('admin.services.*') || request()->routeIs('admin.amenities.*') ? 'show' : '' }} sidebar-submenu"
                    id="servicesSubmenu">
                    @can('services.view')
                        <li>
                            <a href="{{ route('admin.services.index') }}"
                                class="{{ request()->routeIs('admin.services.index') || request()->routeIs('admin.services.edit') ? 'active' : '' }}">
                                <i class="bi bi-list-ul"></i> All Services
                            </a>
                        </li>
                    @endcan
                    @can('amenities.view')
                        <li>
                            <a href="{{ route('admin.amenities.index') }}"
                                class="{{ request()->routeIs('admin.amenities.*') ? 'active' : '' }}">
                                <i class="bi bi-stars"></i> Amenities
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        @if (auth('admin')->user()->can('about-settings.view') ||
                auth('admin')->user()->can('journeys.view') ||
                auth('admin')->user()->can('team-members.view') ||
                auth('admin')->user()->can('testimonials.view'))
            <li>
                <a href="#aboutSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('admin.about_settings.*') || request()->routeIs('admin.journeys.*') || request()->routeIs('admin.team-members.*') || request()->routeIs('admin.testimonials.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-info-circle-fill"></i> About Us</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('admin.about_settings.*') || request()->routeIs('admin.journeys.*') || request()->routeIs('admin.team-members.*') || request()->routeIs('admin.testimonials.*') ? 'show' : '' }} sidebar-submenu"
                    id="aboutSubmenu">
                    @can('about-settings.view')
                        <li>
                            <a href="{{ route('admin.about_settings.index') }}"
                                class="{{ request()->routeIs('admin.about_settings.*') ? 'active' : '' }}">
                                <i class="bi bi-gear-fill"></i> Page Content & Stats
                            </a>
                        </li>
                    @endcan
                    @can('journeys.view')
                        <li>
                            <a href="{{ route('admin.journeys.index') }}"
                                class="{{ request()->routeIs('admin.journeys.*') ? 'active' : '' }}">
                                <i class="bi bi-diagram-3"></i> Milestones / Journey
                            </a>
                        </li>
                    @endcan
                    @can('team-members.view')
                        <li>
                            <a href="{{ route('admin.team-members.index') }}"
                                class="{{ request()->routeIs('admin.team-members.*') ? 'active' : '' }}">
                                <i class="bi bi-people-fill"></i> Team Members
                            </a>
                        </li>
                    @endcan
                    @can('testimonials.view')
                        <li>
                            <a href="{{ route('admin.testimonials.index') }}"
                                class="{{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                                <i class="bi bi-chat-quote-fill"></i> Testimonials
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        @can('galleries.view')
            <li>
                <a href="{{ route('admin.galleries.index') }}"
                    class="{{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}">
                    <i class="bi bi-images"></i>
                    Media Gallery
                </a>
            </li>
        @endcan

        @if (auth('admin')->user()->can('posts.view') || auth('admin')->user()->can('faqs.view'))
            <li>
                <a href="#blogSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('admin.posts.*') || request()->routeIs('admin.faqs.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-journal-richtext"></i> Blog & FAQs</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('admin.posts.*') || request()->routeIs('admin.faqs.*') ? 'show' : '' }} sidebar-submenu"
                    id="blogSubmenu">
                    @can('posts.view')
                        <li>
                            <a href="{{ route('admin.posts.index') }}"
                                class="{{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                                <i class="bi bi-newspaper"></i> Blog Posts
                            </a>
                        </li>
                    @endcan
                    @can('faqs.view')
                        <li>
                            <a href="{{ route('admin.faqs.index') }}"
                                class="{{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                                <i class="bi bi-question-circle-fill"></i> FAQ Items
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        @if (auth('admin')->user()->can('admins.view') || auth('admin')->user()->can('roles.view'))
            <li>
                <a href="#accessSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('admin.admins.*') || request()->routeIs('admin.roles.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-shield-lock-fill"></i> Access Control</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('admin.admins.*') || request()->routeIs('admin.roles.*') ? 'show' : '' }} sidebar-submenu"
                    id="accessSubmenu">
                    @can('admins.view')
                        <li>
                            <a href="{{ route('admin.admins.index') }}"
                                class="{{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                                <i class="bi bi-person-fill-gear"></i> Admin Users
                            </a>
                        </li>
                    @endcan
                    @can('roles.view')
                        <li>
                            <a href="{{ route('admin.roles.index') }}"
                                class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <i class="bi bi-key-fill"></i> Roles & Permissions
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif

        @can('settings.view')
            <li>
                <a href="{{ route('admin.settings.site') }}"
                    class="{{ request()->routeIs('admin.settings.site') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i>
                    Site Settings
                </a>
            </li>
        @endcan

        @can('contact-settings.view')
            <li>
                <a href="{{ route('admin.contact_settings.index') }}"
                    class="{{ request()->routeIs('admin.contact_settings.*') ? 'active' : '' }}">
                    <i class="bi bi-telephone-fill"></i>
                    Contact Settings
                </a>
            </li>
        @endcan

        @can('settings.view')
            <li>
                <a href="{{ route('admin.settings.footer') }}"
                    class="{{ request()->routeIs('admin.settings.footer') ? 'active' : '' }}">
                    <i class="bi bi-layout-text-window-reverse"></i>
                    Footer Settings
                </a>
            </li>
        @endcan

        <li>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</nav>
