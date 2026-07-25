<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('site_title', 'Aurelia Estates') }} | Admin Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #D4AF37;
            --primary-hover: #b8962c;
            --bg-color: #f4f6f9;
            --sidebar-bg: #0b1120;
            --sidebar-hover: #1e293b;
            --text-main: #334155;
            --text-light: #94a3b8;
            --sidebar-width: 260px;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; overflow-x: hidden; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
        }

        /* ── Sidebar ─────────────────────────────────── */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background-color: var(--sidebar-bg);
            color: #fff;
            transition: transform 0.3s ease;
            z-index: 1050;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }
        #sidebar::-webkit-scrollbar { width: 6px; }
        #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        #sidebar::-webkit-scrollbar-track { background: transparent; }

        /* Desktop: sidebar collapse slides out to the left */
        #sidebar.collapsed { transform: translateX(calc(-1 * var(--sidebar-width))); }

        .sidebar-header {
            padding: 20px; text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-family: 'Outfit', sans-serif;
        }
        .sidebar-header h3 { font-size: 1.5rem; font-weight: 700; margin: 0; letter-spacing: 1px; }
        .sidebar-header h3 span { color: var(--primary); }

        .sidebar-menu { padding: 20px 0; list-style: none; margin: 0; }
        .sidebar-menu li { padding: 0 15px; margin-bottom: 5px; }
        .sidebar-menu a {
            display: flex; align-items: center; padding: 12px 15px;
            color: var(--text-light); text-decoration: none;
            border-radius: 8px; transition: all 0.3s ease; font-weight: 500;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: var(--sidebar-hover); color: #fff;
        }
        .sidebar-menu a i { font-size: 1.2rem; margin-right: 15px; color: var(--primary); }
        .sidebar-submenu { list-style: none; padding-left: 20px; margin-top: 5px; }
        .sidebar-submenu a { padding: 8px 15px; font-size: 0.9rem; }

        /* ── Mobile Sidebar Overlay Backdrop ─────────── */
        #sidebarOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 1040;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }
        #sidebarOverlay.show { display: block; }

        /* ── Main Content ─────────────────────────────── */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        #main-content.expanded { margin-left: 0; }

        /* ── Top Navbar ───────────────────────────────── */
        .top-navbar {
            background-color: #fff;
            padding: 14px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .btn-toggle {
            background: none; border: none;
            font-size: 1.5rem; color: var(--sidebar-bg); cursor: pointer;
            padding: 4px 8px; border-radius: 6px;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .btn-toggle:hover { background: rgba(0,0,0,0.06); }

        .user-dropdown {
            display: flex; align-items: center; gap: 8px; cursor: pointer;
            color: var(--text-main); font-weight: 500; text-decoration: none;
        }

        /* ── Cards & Widgets ──────────────────────────── */
        .card {
            border: none; border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: transform 0.3s ease;
        }
        .card:hover { transform: translateY(-4px); }

        .widget-icon {
            width: 50px; height: 50px; border-radius: 12px;
            background-color: rgba(212,175,55,0.1); color: var(--primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; flex-shrink: 0;
        }

        .btn-gold { background-color: var(--primary); color: #fff; border: none; }
        .btn-gold:hover { background-color: var(--primary-hover); color: #fff; }

        /* ── Tables ───────────────────────────────────── */
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        /* ── Page content padding ─────────────────────── */
        .admin-page-content { padding: clamp(14px, 3vw, 24px); }

        /* ── Responsive: Tablet (≤ 991px) ─────────────── */
        @media (max-width: 991.98px) {
            .user-name { display: none !important; }
        }

        /* ── Responsive: Mobile (≤ 768px) ─────────────── */
        @media (max-width: 768px) {
            /* Sidebar off-screen by default on mobile */
            #sidebar { transform: translateX(calc(-1 * var(--sidebar-width))); }
            #sidebar.mobile-open {
                transform: translateX(0);
                box-shadow: 6px 0 30px rgba(0,0,0,0.35);
            }

            /* Main content always full width on mobile */
            #main-content { margin-left: 0 !important; }
            #main-content.expanded { margin-left: 0 !important; }

            /* Navbar */
            .top-navbar { padding: 12px 16px; }

            /* Page content */
            .admin-page-content { padding: 14px; }

            /* Disable card hover lift on touch */
            .card:hover { transform: none; }

            /* Widget icon smaller */
            .widget-icon { width: 42px; height: 42px; font-size: 1.2rem; }

            /* Tables on mobile */
            .table { font-size: 0.85rem; }
            .table th, .table td { white-space: nowrap; }

            /* Form buttons */
            .form-actions .btn { width: 100%; margin-bottom: 8px; }

            /* Page title rows (title + add button) */
            .page-title-row {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 10px;
            }
            .page-title-row .btn { width: 100%; }
        }

        /* ── Responsive: Small Mobile (≤ 480px) ────────── */
        @media (max-width: 480px) {
            .sidebar-header h3 { font-size: 1.2rem; }
            .top-navbar { padding: 10px 12px; }
            .admin-page-content { padding: 10px; }
            .card-body { padding: 14px !important; }
            h1, .h1 { font-size: 1.4rem !important; }
            h2, .h2 { font-size: 1.2rem !important; }
            .table { font-size: 0.78rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarOverlay"></div>

    @include('backend.partials.sidebar')

    <div id="main-content">
        @include('backend.partials.navbar')

        <div class="admin-page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function () {
        const sidebar     = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const overlay     = document.getElementById('sidebarOverlay');
        const toggleBtn   = document.getElementById('sidebarToggle');

        function isMobile() { return window.innerWidth <= 768; }

        function openMobileSidebar() {
            sidebar.classList.add('mobile-open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileSidebar() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        function toggleDesktopSidebar() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                if (isMobile()) {
                    sidebar.classList.contains('mobile-open') ? closeMobileSidebar() : openMobileSidebar();
                } else {
                    toggleDesktopSidebar();
                }
            });
        }

        // Close when overlay backdrop is clicked
        if (overlay) overlay.addEventListener('click', closeMobileSidebar);

        // Close sidebar on nav link click (mobile)
        document.querySelectorAll('#sidebar .sidebar-menu a[href]:not([href="#"])').forEach(function (link) {
            link.addEventListener('click', function () {
                if (isMobile()) closeMobileSidebar();
            });
        });

        // Reset state on window resize
        window.addEventListener('resize', function () {
            if (!isMobile()) {
                closeMobileSidebar();
                document.body.style.overflow = '';
            }
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
