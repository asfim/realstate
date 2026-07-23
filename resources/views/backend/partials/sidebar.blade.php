<nav id="sidebar">
    <div class="sidebar-header">
        <h3>AURELIA<span>ESTATES</span></h3>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.homepage_settings.index') }}"
                class="{{ request()->routeIs('admin.homepage_settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                Homepage Settings
            </a>
        </li>
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
                <li>
                    <a href="{{ route('admin.properties.create') }}"
                        class="{{ request()->routeIs('admin.properties.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i> Add Property
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.properties.manage.index') }}"
                        class="{{ request()->routeIs('admin.properties.manage.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-fill"></i> Manage Property
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.property-types.index') }}"
                        class="{{ request()->routeIs('admin.property-types.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Property Types
                    </a>
                </li>
            </ul>
        @php
            $unreadSubmissionsCount = \App\Models\Submission::where('is_read', false)->count();
        @endphp
        <li>
            <a href="{{ route('admin.submissions.index') }}" class="{{ request()->routeIs('admin.submissions.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-inbox-fill"></i>
                    Submissions
                </span>
                @if($unreadSubmissionsCount > 0)
                    <span class="badge bg-danger rounded-pill">{{ $unreadSubmissionsCount }}</span>
                @endif
            </a>
        </li>
        <li>
            <a href="#servicesSubmenu" data-bs-toggle="collapse"
                class="{{ request()->routeIs('admin.services.*') || request()->routeIs('admin.amenities.*') ? 'active' : '' }} d-flex justify-content-between">
                <span><i class="bi bi-briefcase-fill"></i> Services</span>
                <i class="bi bi-chevron-down small"></i>
            </a>
            <ul class="collapse {{ request()->routeIs('admin.services.*') || request()->routeIs('admin.amenities.*') ? 'show' : '' }} sidebar-submenu"
                id="servicesSubmenu">
                <li>
                    <a href="{{ route('admin.services.index') }}"
                        class="{{ request()->routeIs('admin.services.index') || request()->routeIs('admin.services.edit') ? 'active' : '' }}">
                        <i class="bi bi-list-ul"></i> All Services
                    </a>
                </li>
              
                <li>
                    <a href="{{ route('admin.amenities.index') }}"
                        class="{{ request()->routeIs('admin.amenities.*') ? 'active' : '' }}">
                        <i class="bi bi-stars"></i> Amenities
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#aboutSubmenu" data-bs-toggle="collapse"
                class="{{ request()->routeIs('admin.about_settings.*') || request()->routeIs('admin.journeys.*') || request()->routeIs('admin.team-members.*') ? 'active' : '' }} d-flex justify-content-between">
                <span><i class="bi bi-info-circle-fill"></i> About Us</span>
                <i class="bi bi-chevron-down small"></i>
            </a>
            <ul class="collapse {{ request()->routeIs('admin.about_settings.*') || request()->routeIs('admin.journeys.*') || request()->routeIs('admin.team-members.*') ? 'show' : '' }} sidebar-submenu"
                id="aboutSubmenu">
                <li>
                    <a href="{{ route('admin.about_settings.index') }}"
                        class="{{ request()->routeIs('admin.about_settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-fill"></i> Page Content & Stats
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.journeys.index') }}"
                        class="{{ request()->routeIs('admin.journeys.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i> Milestones / Journey
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.team-members.index') }}"
                        class="{{ request()->routeIs('admin.team-members.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Team Members
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{ route('admin.galleries.index') }}" class="{{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}">
                <i class="bi bi-images"></i>
                Media Gallery
            </a>
        </li>
        
        <li>
            <a href="{{ route('admin.settings.footer') }}"
                class="{{ request()->routeIs('admin.settings.footer') ? 'active' : '' }}">
                <i class="bi bi-layout-text-window-reverse"></i>
                Footer Settings
            </a>
        </li>
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
