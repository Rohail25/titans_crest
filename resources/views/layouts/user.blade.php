<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Titans Crest - Investment Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-dark: #0f172a;
            --primary-blue: #1e40af;
            --accent-gold: #fbbf24;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border-light: #e2e8f0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--text-dark);
        }

        /* Sidebar Navigation */
        .sidebar {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #1a2b4a 100%);
            color: white;
            min-height: 100vh;
            padding: 2rem 0;
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            padding: 0 2rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
        }

        .sidebar-brand h3 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .sidebar-brand p {
            font-size: 0.875rem;
            color: var(--accent-gold);
            margin: 0;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.5rem 2rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: white;
            border-left-color: var(--accent-gold);
        }

        .sidebar-menu a.active {
            background-color: rgba(251, 191, 36, 0.1);
            color: var(--accent-gold);
            border-left-color: var(--accent-gold);
        }

        .sidebar-menu i {
            margin-right: 1rem;
            width: 20px;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-profile {
            display: flex;
            align-items: center;
            color: white;
            margin-bottom: 1.5rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-weight: 700;
            margin-right: 1rem;
        }

        .user-info p {
            margin: 0;
            font-size: 0.875rem;
        }

        .user-info-name {
            font-weight: 600;
        }

        .user-info-email {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Top Navigation */
        .topnav {
            background: white;
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 0;
            z-index: 100;
            margin-left: 280px;
        }

        .topnav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topnav-left h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }

        .topnav-right {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
            color: var(--text-light);
            font-size: 1.25rem;
            transition: color 0.3s ease;
        }

        .notification-icon:hover {
            color: var(--primary-blue);
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.725rem;
            font-weight: 700;
        }

        .profile-dropdown {
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
        }

        .profile-dropdown img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            min-height: calc(100vh - 80px);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: var(--card-bg);
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-light);
            border-radius: 12px 12px 0 0;
            padding: 1.5rem;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: var(--text-dark);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Stat Cards */
        .stat-card {
            padding: 1.5rem;
            border-radius: 12px;
            background: white;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-gold));
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-gold));
            color: white;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .stat-card-label {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .stat-card-change {
            font-size: 0.875rem;
            color: var(--success);
        }

        .stat-card-change.negative {
            color: var(--danger);
        }

        /* Primary Card (Highlighted) */
        .stat-card.primary {
            background: linear-gradient(135deg, var(--primary-blue), #1e40af);
            color: white;
        }

        .stat-card.primary .stat-card-label {
            color: rgba(255, 255, 255, 0.8);
        }

        .stat-card.primary .stat-card-value {
            color: white;
        }

        .stat-card.primary .stat-card-change {
            color: var(--accent-gold);
        }

        /* Progress Bars */
        .progress-bars {
            margin-top: 1rem;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            background: var(--border-light);
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-gold));
            transition: width 0.3s ease;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: var(--light-bg);
            border: none;
        }

        .table thead th {
            color: var(--text-light);
            font-weight: 600;
            border: none;
            padding: 1rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1rem;
            border-color: var(--border-light);
            vertical-align: middle;
        }

        .table tbody tr {
            transition: background 0.3s ease;
        }

        .table tbody tr:hover {
            background: var(--light-bg);
        }

        /* Badges */
        .badge {
            padding: 0.5rem 0.875rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        /* Forms */
        .form-control, .form-select {
            border: 1px solid var(--border-light);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        .form-label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), #1e40af);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(30, 64, 175, 0.3);
            color: white;
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
        }

        .btn-outline-primary:hover {
            background: var(--primary-blue);
            color: white;
        }

        .btn-success {
            background: var(--success);
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: var(--danger);
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 8px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border-left: 4px solid var(--warning);
        }

        /* Footer */
        .footer {
            background: white;
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--border-light);
            margin-left: 280px;
            text-align: center;
            color: var(--text-light);
            font-size: 0.875rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem 0;
                min-height: auto;
            }

            .sidebar-menu {
                display: flex;
                flex-wrap: wrap;
            }

            .sidebar-menu a {
                padding: 0.75rem 1rem;
                flex: 1;
                min-width: 150px;
                border-left: none;
                border-bottom: 2px solid transparent;
            }

            .sidebar-menu a.active {
                border-left: none;
                border-bottom-color: var(--accent-gold);
            }

            .topnav, .main-content, .footer {
                margin-left: 0;
            }

            .sidebar-footer {
                position: relative;
                border-top: none;
                padding: 1rem;
                margin-top: 1rem;
            }

            .main-content {
                padding: 1rem;
            }
        }

        /* Utility Classes */
        .text-muted {
            color: var(--text-light) !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar pt-0">
            <div class="sidebar-brand mb-0 pb-0" >
                {{-- <h3><i class="fas fa-crown"></i> Titans Crest</h3>
                <p>Investment Dashboard</p> --}}
                <a href="/"><img src="images/logo.svg" alt="Titans Crest" width="150" height="150"></a>
            </div>

            <ul class="sidebar-menu">
                <li><a href="{{ route('user.dashboard') }}" class="@if(request()->routeIs('user.dashboard')) active @endif">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a></li>
                <li><a href="{{ route('user.wallet') }}" class="@if(request()->routeIs('user.wallet')) active @endif">
                    <i class="fas fa-wallet"></i> Wallet
                </a></li>
                <li><a href="{{ route('user.deposit.index') }}" class="@if(request()->routeIs('user.deposit.*')) active @endif">
                    <i class="fas fa-arrow-down"></i> Deposits
                </a></li>
                <li><a href="{{ route('user.withdrawal.index') }}" class="@if(request()->routeIs('user.withdrawal.*')) active @endif">
                    <i class="fas fa-arrow-up"></i> Withdrawals
                </a></li>
                <li><a href="{{ route('user.referral') }}" class="@if(request()->routeIs('user.referral')) active @endif">
                    <i class="fas fa-users"></i> Referrals
                </a></li>
                <li><a href="{{ route('user.team') }}" class="@if(request()->routeIs('user.team')) active @endif">
                    <i class="fas fa-sitemap"></i> Team
                </a></li>
                <li><a href="{{ route('user.analytics') }}" class="@if(request()->routeIs('user.analytics')) active @endif">
                    <i class="fas fa-chart-bar"></i> Analytics
                </a></li>
            </ul>

            <div class="sidebar-footer" >
                <div class="user-profile">
                    <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="user-info">
                        <p class="user-info-name">{{ auth()->user()->name }}</p>
                        <p class="user-info-email">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Top Navigation -->
            <nav class="topnav">
                <div class="topnav-content">
                    <div class="topnav-left">
                        <h1>@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="topnav-right">
                        <div class="notification-icon">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">2</span>
                        </div>
                        <div class="dropdown">
                            <div class="profile-dropdown dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=0D8ABC&color=fff" alt="Profile">
                                <span>{{ auth()->user()->name }}</span>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('user.profile.show') }}">Profile Settings</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.profile.edit') }}">Security</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Flash Messages -->
            @if($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show m-4" role="alert">
                    <i class="fas fa-check-circle"></i> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            <div class="main-content">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="footer">
                <p>&copy; {{ date('Y') }} Titans Crest. All rights reserved. | Secure Investment Platform</p>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
