<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'en' }}" dir="{{ $isRtl ?? false ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('admin.dashboard.title')) - {{ __('admin.app_name') }}</title>

    <!-- Bootstrap 5 CSS -->
    @if($isRtl ?? false)
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            {{ $isRtl ?? false ? 'right' : 'left' }}: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
            padding-top: 1rem;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            {{ $isRtl ?? false ? 'border-right' : 'border-left' }}: 3px solid transparent;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            {{ $isRtl ?? false ? 'border-right-color' : 'border-left-color' }}: #3498db;
        }

        .sidebar .nav-link i {
            width: 24px;
            {{ $isRtl ?? false ? 'margin-left' : 'margin-right' }}: 0.5rem;
        }

        .main-content {
            {{ $isRtl ?? false ? 'margin-right' : 'margin-left' }}: var(--sidebar-width);
            min-height: 100vh;
        }

        .top-navbar {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            padding: 0.75rem 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .user-info {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .menu-toggle-btn {
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            background: #fff;
            transition: all 0.3s;
        }

        .menu-toggle-btn:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
        }

        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border-radius: 0.5rem;
        }

        .stat-card {
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .brand-logo {
            padding: 1rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
            text-align: center;
        }

        .brand-logo img {
            max-width: 160px;
            height: auto;
            margin-bottom: 0.5rem;
            background: #fff;
            border-radius: 8px;
            padding: 8px;
        }

        .brand-logo h4 {
            color: #fff;
            margin: 0;
            font-weight: 600;
        }

        .brand-logo small {
            color: rgba(255, 255, 255, 0.6);
            display: block;
            font-size: 0.75rem;
        }

        #map {
            height: 500px;
            border-radius: 0.5rem;
        }

        .table th {
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .lang-dropdown .dropdown-item.active {
            background-color: #3498db;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-backdrop.show {
            display: block;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX({{ $isRtl ?? false ? '100%' : '-100%' }});
                transition: transform 0.3s ease-in-out;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                {{ $isRtl ?? false ? 'margin-right' : 'margin-left' }}: 0;
            }

            .page-title {
                font-size: 1rem;
            }

            .user-info {
                display: none;
            }

            .top-navbar {
                padding: 0.5rem 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .page-title {
                font-size: 0.95rem;
            }

            .lang-dropdown .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="brand-logo">
            <img src="{{ asset('images/logo.png') }}" alt="{{ __('admin.app_name') }}" class="img-fluid">
            <small>{{ __('admin.admin_panel') }}</small>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> {{ __('admin.nav.dashboard') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.establishments*') ? 'active' : '' }}" href="{{ route('admin.establishments.index') }}">
                    <i class="bi bi-building"></i> {{ __('admin.nav.establishments') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.map*') ? 'active' : '' }}" href="{{ route('admin.map.index') }}">
                    <i class="bi bi-map"></i> {{ __('admin.nav.map_view') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.agents*') ? 'active' : '' }}" href="{{ route('admin.agents.index') }}">
                    <i class="bi bi-people"></i> {{ __('admin.nav.agents') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                    <i class="bi bi-graph-up"></i> {{ __('admin.nav.reports') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.mobile-connection*') ? 'active' : '' }}" href="{{ route('admin.mobile-connection.index') }}">
                    <i class="bi bi-qr-code"></i> {{ __('admin.nav.mobile_connection') }}
                </a>
            </li>
        </ul>

        <div class="mt-auto p-3" style="position: absolute; bottom: 0; width: 100%;">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100">
                    <i class="bi bi-box-arrow-{{ $isRtl ?? false ? 'right' : 'left' }}"></i> {{ __('admin.nav.logout') }}
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 gap-md-3 flex-grow-1">
                    <button class="menu-toggle-btn d-md-none" id="sidebarToggle" type="button">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <h1 class="page-title">@yield('page-title', __('admin.dashboard.title'))</h1>
                </div>

                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <!-- Language Dropdown -->
                    <div class="dropdown lang-dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-globe"></i>
                            <span class="d-none d-sm-inline ms-1">
                                @switch($currentLocale ?? 'en')
                                    @case('en')
                                        EN
                                        @break
                                    @case('fr')
                                        FR
                                        @break
                                    @case('ar')
                                        AR
                                        @break
                                @endswitch
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item {{ ($currentLocale ?? 'en') === 'en' ? 'active' : '' }}" href="{{ route('locale.switch', 'en') }}">
                                    <i class="bi bi-check2 {{ ($currentLocale ?? 'en') === 'en' ? '' : 'invisible' }}"></i> English
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ ($currentLocale ?? 'en') === 'fr' ? 'active' : '' }}" href="{{ route('locale.switch', 'fr') }}">
                                    <i class="bi bi-check2 {{ ($currentLocale ?? 'en') === 'fr' ? '' : 'invisible' }}"></i> Français
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ ($currentLocale ?? 'en') === 'ar' ? 'active' : '' }}" href="{{ route('locale.switch', 'ar') }}">
                                    <i class="bi bi-check2 {{ ($currentLocale ?? 'en') === 'ar' ? '' : 'invisible' }}"></i> العربية
                                </a>
                            </li>
                        </ul>
                    </div>

                    <span class="user-info d-none d-md-flex align-items-center">
                        <i class="bi bi-person-circle me-2"></i>
                        <span>{{ auth()->user()->name }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');

            // Toggle sidebar
            function toggleSidebar() {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            }

            // Close sidebar when clicking backdrop
            backdrop.addEventListener('click', toggleSidebar);

            // Toggle sidebar when clicking the menu button
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            // Close sidebar when clicking a navigation link on mobile
            const navLinks = sidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        toggleSidebar();
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
