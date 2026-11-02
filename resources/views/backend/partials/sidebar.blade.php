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

        <li>
            <a href="{{ route('erp.dashboard') }}" class="text-warning fw-semibold" style="background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.2);">
                <i class="bi bi-buildings-fill text-warning"></i>
                Enterprise ERP
            </a>
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

        @if (auth('admin')->user()->can('accounts.view') || auth('admin')->user()->can('payroll.view'))
            <li>
                <a href="#accountsSubmenu" data-bs-toggle="collapse"
                    class="{{ request()->routeIs('admin.accounts.*') || request()->routeIs('admin.incomes.*') || request()->routeIs('admin.expenses.*') || request()->routeIs('admin.payrolls.*') || request()->routeIs('admin.account-categories.*') ? 'active' : '' }} d-flex justify-content-between">
                    <span><i class="bi bi-wallet2"></i> Accounts & Finance</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="collapse {{ request()->routeIs('admin.accounts.*') || request()->routeIs('admin.incomes.*') || request()->routeIs('admin.expenses.*') || request()->routeIs('admin.payrolls.*') || request()->routeIs('admin.account-categories.*') ? 'show' : '' }} sidebar-submenu"
                    id="accountsSubmenu">
                    @can('accounts.view')
                        <li>
                            <a href="{{ route('admin.accounts.index') }}"
                                class="{{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}">
                                <i class="bi bi-graph-up-arrow"></i> Financial Overview
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.incomes.index') }}"
                                class="{{ request()->routeIs('admin.incomes.*') ? 'active' : '' }}">
                                <i class="bi bi-arrow-down-left-circle-fill text-success"></i> Income & Revenue
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.expenses.index') }}"
                                class="{{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                                <i class="bi bi-arrow-up-right-circle-fill text-danger"></i> Expenses & Costs
                            </a>
                        </li>
                    @endcan
                    @can('payroll.view')
                        <li>
                            <a href="{{ route('admin.payrolls.index') }}"
                                class="{{ request()->routeIs('admin.payrolls.*') ? 'active' : '' }}">
                                <i class="bi bi-cash-stack text-warning"></i> Staff Salary & Payroll
                            </a>
                        </li>
                    @endcan
                    @can('accounts.view')
                        <li>
                            <a href="{{ route('admin.account-categories.index') }}"
                                class="{{ request()->routeIs('admin.account-categories.*') ? 'active' : '' }}">
                                <i class="bi bi-tag-fill"></i> Categories
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
