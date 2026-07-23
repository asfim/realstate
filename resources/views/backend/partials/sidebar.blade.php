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
            <a href="{{ route('admin.homepage_settings.index') }}" class="{{ request()->routeIs('admin.homepage_settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                Homepage Settings
            </a>
        </li>
        <li>
            <a href="{{ route('admin.featured-properties.index') }}" class="{{ request()->routeIs('admin.featured-properties.*') ? 'active' : '' }}">
                <i class="bi bi-house-heart-fill"></i>
                Featured Properties
            </a>
        </li>
        <li>
            <a href="{{ route('admin.settings.footer') }}" class="{{ request()->routeIs('admin.settings.footer') ? 'active' : '' }}">
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
