<div class="top-navbar">
    <button class="btn-toggle" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>

    <div class="dropdown">
        <a href="#" class="user-dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name) }}&background=D4AF37&color=fff" alt="Admin" class="rounded-circle" width="35" height="35">
            <span class="user-name">{{ Auth::guard('admin')->user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
            @can('admins.view')
                <li><a class="dropdown-item" href="{{ route('admin.admins.index') }}"><i class="bi bi-person-gear me-2 text-warning"></i> Admin Accounts</a></li>
            @endcan
            <li><a class="dropdown-item" href="{{ url('/') }}" target="_blank"><i class="bi bi-globe me-2 text-success"></i> View Website</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>
