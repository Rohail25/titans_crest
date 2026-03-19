<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Titans Crest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a1a2e;
            --secondary: #16213e;
            --accent: #d4af37;
            --text-light: #eee;
            --text-dark: #333;
            --danger: #ef4444;
            --warning: #f97316;
            --success: #22c55e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #0f0f1e;
            color: var(--text-light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .admin-container {
            display: flex;
            flex: 1;
            min-height: calc(100vh - 60px);
        }

        /* Sidebar */
        .admin-sidebar {
            width: 260px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 10px 0 30px  0;
            position: fixed;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            border-right: 2px solid var(--accent);
           
        }

        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 3px;
        }

        .admin-sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            margin: 0 10px 5px 10px;
            border-radius: 4px;
        }

        .admin-sidebar a:hover {
            background-color: rgba(212, 175, 55, 0.1);
            border-left-color: var(--accent);
            padding-left: 25px;
        }

        .admin-sidebar a.active {
            background-color: rgba(212, 175, 55, 0.2);
            border-left-color: var(--accent);
            color: var(--accent);
        }

        .admin-sidebar i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }

        .sidebar-title {
            padding: 15px 20px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--accent);
            letter-spacing: 1px;
            
            margin-left: 10px;
        }

        .sidebar-title:first-child {
            margin-top: 0;
        }

        /* Main Content */
        .admin-main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .admin-topbar {
            background: var(--primary);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--accent);
            position: fixed;
            width: calc(100% - 260px);
            top: 0;
            left: 260px;
            z-index: 999;
        }

        .admin-topbar .brand {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-topbar .admin-badge {
            background: var(--accent);
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .admin-topbar .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-topbar .user-menu a {
            color: var(--text-light);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .admin-topbar .user-menu a:hover {
            background-color: rgba(212, 175, 55, 0.1);
            color: var(--accent);
        }

        .sidebar-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border: 1px solid rgba(212, 175, 55, 0.5);
            background: transparent;
            color: var(--text-light);
            border-radius: 6px;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .sidebar-toggle:hover {
            background: rgba(212, 175, 55, 0.1);
            color: var(--accent);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 999;
        }

        /* Content Area */
        .admin-content {
            margin-top: 60px;
            padding: 30px;
            flex: 1;
            overflow-y: auto;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-title i {
            font-size: 2.5rem;
        }

        /* Cards */
        .card {
            background: var(--secondary);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 8px;
            color: var(--text-light);
            margin-bottom: 20px;
        }

        .card-header {
            background: rgba(212, 175, 55, 0.1);
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            padding: 15px 20px;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            color: var(--accent);
            font-weight: 600;
        }

        /* KPI Cards */
        .kpi-card {
            background: linear-gradient(135deg, var(--secondary) 0%, rgba(212, 175, 55, 0.1) 100%);
            border: 1px solid var(--accent);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.2);
        }

        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent);
            margin: 10px 0;
        }

        .kpi-label {
            font-size: 0.9rem;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .kpi-icon {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 10px;
        }

        /* Tables */
        .table {
            color: inherit;
        }

        .table thead th {
            background: rgba(212, 175, 55, 0.1);
            border-bottom: 2px solid var(--accent);
            color: var(--accent);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .table tbody td {
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            padding: 12px;
        }

        .table tbody tr:hover {
            background: rgba(212, 175, 55, 0.05);
        }

        .badge {
            font-weight: 600;
            padding: 6px 10px;
            font-size: 0.8rem;
        }

        .badge-success {
            background: var(--success);
            color: white;
        }

        .badge-warning {
            background: var(--warning);
            color: white;
        }

        .badge-danger {
            background: var(--danger);
            color: white;
        }

        /* Buttons */
        .btn {
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--accent);
            color: var(--primary);
            border: none;
        }

        .btn-primary:hover {
            background-color: #e0a428;
            color: var(--primary);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .btn-danger {
            background-color: var(--danger);
            border: none;
        }

        .btn-danger:hover {
            background-color: #d63828;
        }

        /* Pagination */
        .pagination {
            justify-content: flex-start;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .pagination .page-item {
            margin: 0 2px;
        }

        .pagination .page-link {
            color: var(--text-light);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 0.85rem;
        }

        .pagination .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
            color: var(--primary);
        }

        .pagination .page-link:hover {
            background: rgba(212, 175, 55, 0.15);
            color: var(--text-light);
        }

        .pagination .page-link:focus {
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.3);
        }

        /* Forms */
        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            color: var(--text-light);
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--accent);
            color: var(--text-light);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }

        .form-label {
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: 8px;
        }

        /* Alerts */
        .alert {
            border-radius: 4px;
            border: 1px solid;
            margin-bottom: 20px;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-color: var(--success);
            color: var(--success);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: var(--danger);
            color: var(--danger);
        }

        .alert-warning {
            background: rgba(249, 115, 22, 0.1);
            border-color: var(--warning);
            color: var(--warning);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 80px;
                padding: 20px 0;
            }

            .admin-sidebar a {
                padding: 12px;
                margin: 0 5px 5px 5px;
            }

            .admin-sidebar a span {
                display: none;
            }

            .admin-topbar {
                width: calc(100% - 80px);
                left: 80px;
            }

            .admin-main {
                margin-left: 80px;
            }

            .admin-content {
                padding: 20px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .sidebar-title {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                width: 280px;
                max-width: 85%;
                z-index: 1001;
            }

            .admin-sidebar.active {
                transform: translateX(0);
            }

            .admin-sidebar a span {
                display: inline;
            }

            .sidebar-title {
                display: block;
            }

            .sidebar-toggle {
                display: inline-flex;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .admin-topbar {
                width: 100%;
                left: 0;
                padding: 12px 15px;
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-topbar .user-menu a span,
            .admin-topbar .user-menu .btn span {
                display: none;
            }

            .admin-content {
                padding: 15px;
            }

            .page-title {
                font-size: 1.2rem;
            }

            .kpi-card {
                margin-bottom: 15px;
            }
        }
    </style>
    @yield('styles')
</head>

<body>
    <!-- Top Bar -->
    <div class="admin-topbar">
        <div class="brand">
            <button id="adminSidebarToggle" class="sidebar-toggle" type="button" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            {{-- <i class="fas fa-crown"></i> --}}
            {{-- <span>Titans Crest Admin</span>
            <span class="admin-badge">Admin</span> --}}
        </div>
        <div class="user-menu">
            {{-- <span><i class="fas fa-bell"></i></span> --}}
            <a href="{{ route('admin.profile.show') }}">
                <i class="fas fa-user-circle"></i>
                <span>{{ auth()->user()->name }}</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-sm"
                    style="background: transparent; border: none; color: var(--text-light);">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <div id="adminSidebarOverlay" class="sidebar-overlay"></div>
    
    <div class="admin-container">
        <!-- Sidebar -->
        
        <div id="adminSidebar" class="admin-sidebar">
            <img src="{{ asset('images/logo.svg') }}" alt="Titans Crest" width="100" height="100" style="margin-left:40px;">

            <div class="sidebar-title">Dashboard</div>
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Overview</span>
            </a>

            <div class="sidebar-title">Operations</div>
            <a href="{{ route('admin.withdrawals.index') }}"
                class="{{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                <i class="fas fa-coins"></i>
                <span>Withdrawals</span>
            </a>
            <a href="{{ route('admin.users.index') }}"
                class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="{{ route('admin.fund-management.index') }}"
                class="{{ request()->routeIs('admin.fund-management.*') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i>
                <span>Fund Mgmt</span>
            </a>

            <div class="sidebar-title">System</div>
            <a href="{{ route('admin.reports.index') }}"
                class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
            <a href="{{ route('admin.settings.index') }}"
                class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
            <a href="{{ route('admin.referral-commissions.index') }}"
                class="{{ request()->routeIs('admin.referral-commissions.*') ? 'active' : '' }}">
                <i class="fas fa-sitemap"></i>
                <span>Referral Commissions</span>
            </a>
            <a href="{{ route('admin.profit-sharing.index') }}"
                class="{{ request()->routeIs('admin.profit-sharing.*') ? 'active' : '' }}">
                <i class="fas fa-share-alt"></i>
                <span>Profit Sharing</span>
            </a>

            <div class="sidebar-title">Logs</div>
            <a href="{{ route('admin.email-logs.index') }}"
                class="{{ request()->routeIs('admin.email-logs.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i>
                <span>Email Logs</span>
            </a>
            <a href="{{ route('admin.audit-logs.index') }}"
                class="{{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Audit Logs</span>
            </a>

            {{-- <div class="sidebar-title">User Panel</div>
            <a href="{{ route('user.dashboard') }}">
                <i class="fas fa-arrow-right"></i>
                <span>User Side</span>
            </a> --}}
        </div>

        <!-- Main Content -->
        <div class="admin-main">
            <div class="admin-content">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Errors:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Optional: Smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            const sidebarToggle = document.getElementById('adminSidebarToggle');
            const sidebarOverlay = document.getElementById('adminSidebarOverlay');

            const openSidebar = function() {
                if (!sidebar || !sidebarOverlay) {
                    return;
                }
                sidebar.classList.add('active');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            };

            const closeSidebar = function() {
                if (!sidebar || !sidebarOverlay) {
                    return;
                }
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            };

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    const isOpen = sidebar && sidebar.classList.contains('active');
                    if (isOpen) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            if (sidebar) {
                sidebar.querySelectorAll('a').forEach(function(link) {
                    link.addEventListener('click', closeSidebar);
                });
            }

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth > 576) {
                    closeSidebar();
                }
            });

            const activeLink = document.querySelector('.admin-sidebar a.active');
            if (activeLink) {
                activeLink.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        });
    </script>
    @yield('scripts')
</body>

</html>
