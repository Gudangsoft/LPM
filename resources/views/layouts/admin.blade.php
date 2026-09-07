<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Admin {{ $siteSettings['site_name'] ?? config('app.name') }}</title>

    <!-- Favicon -->
    @if(!empty($siteSettings['site_favicon']))
    <link rel="icon" type="image/x-icon" href="{{ Storage::url($siteSettings['site_favicon']) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
            --primary-color: #1e40af;
            --primary-dark: #0a2463;
            --secondary-color: #3b82f6;
            --accent-color: #f59e0b;
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            --sidebar-gradient: linear-gradient(180deg, #0a2463 0%, #0f1f45 55%, #0b1226 100%);
            --accent-glow: rgba(30, 64, 175, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(30, 64, 175, 0.3);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(30, 64, 175, 0.5);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-gradient);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 160px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.12) 0%, transparent 100%);
            pointer-events: none;
        }

        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: relative;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            color: white;
            font-weight: 800;
            text-decoration: none;
            font-size: 1.1rem;
            letter-spacing: -0.02em;
        }

        .sidebar-brand img {
            height: 42px;
            width: auto;
            margin-right: 14px;
            border-radius: 6px;
            background: #fff;
            padding: 2px;
        }

        .sidebar-brand-icon {
            width: 42px;
            height: 42px;
            background: var(--primary-gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 14px;
            font-size: 1.25rem;
            box-shadow: 0 4px 15px rgba(10, 36, 99, 0.4);
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .sidebar-brand-text small {
            font-size: 0.65rem;
            font-weight: 500;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .sidebar-nav {
            padding: 12px 0 28px;
            position: relative;
        }

        .nav-section {
            margin: 14px 14px 4px;
            padding: 14px 12px 6px;
            font-size: 0.72rem;
            text-transform: uppercase;
            color: rgba(255,255,255,0.62);
            letter-spacing: 0.1em;
            font-weight: 700;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-nav > .nav-section:first-child {
            border-top: 0;
            margin-top: 2px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            transition: background-color 0.18s ease, color 0.18s ease;
            margin: 2px 14px;
            border-radius: 10px;
            position: relative;
            font-weight: 500;
            font-size: 0.88rem;
            white-space: nowrap;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.07);
            color: #fff;
        }

        .sidebar-nav .nav-link:hover i {
            opacity: 1;
        }

        .sidebar-nav .nav-link.active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 8px 20px -8px rgba(37, 99, 235, 0.65);
        }

        .sidebar-nav .nav-link.active i {
            opacity: 1;
        }

        .sidebar-nav .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.05rem;
            opacity: 0.85;
            transition: opacity 0.18s ease;
            flex-shrink: 0;
        }

        /* Nested / child items (levels 2-4) */
        .sidebar-nav .nav-link--l2,
        .sidebar-nav .nav-link--l3,
        .sidebar-nav .nav-link--l4 {
            font-size: 0.84rem;
            color: rgba(255,255,255,0.75);
        }
        .sidebar-nav .nav-link--l2 { padding-left: 42px; }
        .sidebar-nav .nav-link--l3 { padding-left: 58px; font-size: 0.82rem; }
        .sidebar-nav .nav-link--l4 { padding-left: 74px; font-size: 0.8rem; }

        .sidebar-nav .nav-link--l2 i,
        .sidebar-nav .nav-link--l3 i,
        .sidebar-nav .nav-link--l4 i {
            font-size: 0.9rem;
        }

        .sidebar-nav .nav-link--l2::before,
        .sidebar-nav .nav-link--l3::before,
        .sidebar-nav .nav-link--l4::before {
            content: '';
            position: absolute;
            top: 50%;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            transform: translateY(-50%);
            transition: background-color 0.18s ease;
        }
        .sidebar-nav .nav-link--l2::before { left: 24px; }
        .sidebar-nav .nav-link--l3::before { left: 40px; width: 5px; height: 5px; }
        .sidebar-nav .nav-link--l4::before { left: 56px; width: 5px; height: 5px; }

        .sidebar-nav .nav-link--l2:hover::before,
        .sidebar-nav .nav-link--l2.active::before,
        .sidebar-nav .nav-link--l3:hover::before,
        .sidebar-nav .nav-link--l3.active::before,
        .sidebar-nav .nav-link--l4:hover::before,
        .sidebar-nav .nav-link--l4.active::before {
            background: #fff;
        }

        .sidebar-nav .nav-link--muted {
            color: rgba(255,255,255,0.4);
            cursor: default;
        }
        .sidebar-nav .nav-link--muted:hover { background: transparent; }

        /* Collapsible groups */
        .sidebar-nav .nav-group-toggle { cursor: pointer; }
        .sidebar-nav .nav-group-caret {
            font-size: 0.72rem;
            opacity: 0.6;
            transition: transform 0.18s ease;
            margin-right: 0;
        }
        .sidebar-nav .nav-group.is-open > .nav-group-toggle > .nav-group-caret {
            transform: rotate(90deg);
        }
        .sidebar-nav .nav-group.is-open > .nav-group-toggle {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }
        .sidebar-nav .nav-group-body {
            display: none;
        }
        .sidebar-nav .nav-group.is-open > .nav-group-body {
            display: block;
        }

        .sidebar-nav .nav-link .badge {
            font-size: 0.65rem;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }

        .sidebar-nav hr {
            margin: 10px 22px;
            border: 0;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Desktop: collapsed sidebar (toggled by the header hamburger, remembered per browser) */
        @media (min-width: 992px) {
            body.sidebar-collapsed .sidebar {
                transform: translateX(-100%);
            }
            body.sidebar-collapsed .main-content {
                margin-left: 0;
            }
        }

        /* Header */
        .header {
            background: #ffffff;
            height: var(--header-height);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid #e2e8f0;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .toggle-sidebar {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 1.25rem;
            color: #64748b;
            cursor: pointer;
            padding: 10px 12px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .toggle-sidebar:hover {
            background: var(--primary-color);
            color: white;
            border-color: transparent;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-right .btn-outline-primary {
            border-color: rgba(30, 64, 175, 0.3);
            color: var(--primary-color);
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .header-right .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: transparent;
            color: white;
        }

        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1e293b;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 10px;
            transition: all 0.2s ease;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .user-dropdown .dropdown-toggle:hover {
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .user-dropdown .dropdown-toggle::after {
            display: none;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid rgba(30, 64, 175, 0.25);
        }

        .user-dropdown .dropdown-menu {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 8px;
            margin-top: 10px;
        }

        .user-dropdown .dropdown-item {
            border-radius: 10px;
            padding: 12px 16px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .user-dropdown .dropdown-item:hover {
            background: #f1f5f9;
        }

        /* Content */
        .content {
            padding: 30px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }

        .breadcrumb {
            margin-bottom: 0;
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: #64748b;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
            background: #ffffff;
            overflow: hidden;
        }

        .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 18px 24px;
            font-weight: 700;
            color: #0f172a;
        }

        .card-body {
            padding: 24px;
        }

        .card-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 16px 24px;
        }

        /* Stats Cards */
        .stat-card {
            padding: 24px;
            border-radius: 12px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.25s ease;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.15);
        }

        .stat-card.bg-blue { background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%); }
        .stat-card.bg-green { background: linear-gradient(135deg, #059669 0%, #047857 100%); }
        .stat-card.bg-purple { background: linear-gradient(135deg, #3730a3 0%, #312e81 100%); }
        .stat-card.bg-orange { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); }
        .stat-card.bg-red { background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%); }
        .stat-card.bg-cyan { background: linear-gradient(135deg, #0e7490 0%, #155e75 100%); }
        .stat-card.bg-pink { background: linear-gradient(135deg, #9d174d 0%, #831843 100%); }
        .stat-card.bg-indigo { background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%); }
        .stat-card.bg-slate { background: linear-gradient(135deg, #475569 0%, #334155 100%); }
        .stat-card.bg-teal { background: linear-gradient(135deg, #0f766e 0%, #115e59 100%); }
        .stat-card.bg-amber { background: linear-gradient(135deg, #b45309 0%, #92400e 100%); }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .stat-value {
            font-size: 1.9rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(120deg, #0a2463 0%, #1e40af 60%, #1d4ed8 100%);
            border-radius: 16px;
            padding: 32px 40px;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.35) 1px, transparent 1px);
            background-size: 22px 22px;
            opacity: 0.08;
            pointer-events: none;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            top: -40%;
            right: -8%;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.12);
            pointer-events: none;
        }

        .welcome-banner .date-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .welcome-banner .welcome-title {
            font-size: 1.65rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
            position: relative;
            z-index: 1;
        }

        .welcome-banner .welcome-subtitle {
            font-size: 1rem;
            font-weight: 400;
            opacity: 0.85;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .welcome-banner .welcome-period {
            font-size: 0.9rem;
            opacity: 0.85;
            position: relative;
            z-index: 1;
        }

        .welcome-banner .welcome-period strong {
            color: #fbbf24;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table th {
            font-weight: 700;
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-top: none;
            padding: 16px;
            background: #f8fafc;
        }

        .table td {
            padding: 16px;
            vertical-align: middle;
            color: #334155;
        }

        .table-hover tbody tr {
            transition: background 0.15s ease;
        }

        .table-hover tbody tr:hover {
            background: #f8fafc;
        }

        /* Forms */
        .form-label {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 11px 16px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 11px 22px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            box-shadow: 0 2px 8px rgba(30, 64, 175, 0.25);
        }

        .btn-primary:hover {
            box-shadow: 0 4px 14px rgba(30, 64, 175, 0.35);
            background: linear-gradient(135deg, #1e3a8a 0%, #172554 100%);
        }

        .btn-sm {
            padding: 7px 14px;
            font-size: 0.875rem;
            border-radius: 6px;
        }

        .btn-outline-primary {
            border: 1px solid rgba(30, 64, 175, 0.35);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: transparent;
            color: white;
        }

        .btn-outline-info {
            border: 2px solid rgba(6, 182, 212, 0.3);
            color: #0891b2;
        }

        .btn-outline-danger {
            border: 2px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
        }

        /* Badges */
        .badge {
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
        }

        /* Alerts */
        .alert {
            border: none;
            border-left: 4px solid transparent;
            border-radius: 8px;
            padding: 16px 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #ecfdf5;
            border-left-color: #059669;
            color: #065f46;
        }

        .alert-danger {
            background: #fef2f2;
            border-left-color: #dc2626;
            color: #991b1b;
        }

        .alert-warning {
            background: #fffbeb;
            border-left-color: #d97706;
            color: #92400e;
        }

        .alert-info {
            background: #eff6ff;
            border-left-color: var(--secondary-color);
            color: #1e3a8a;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(4px);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in {
            animation: slideIn 0.4s ease forwards;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>

    @stack('styles')
</head>
<body>
    <script>
        // Restore the collapsed sidebar state before paint to avoid a flash
        try {
            if (localStorage.getItem('admin.sidebarCollapsed') === '1') {
                document.body.classList.add('sidebar-collapsed');
            }
        } catch (e) {}
    </script>

    @include('partials.impersonation-banner')

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                @if(!empty($siteSettings['site_logo']))
                <img src="{{ Storage::url($siteSettings['site_logo']) }}" alt="Logo">
                @else
                <div class="sidebar-brand-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                @endif
                <div class="sidebar-brand-text">
                    <small>Admin Panel</small>
                    {{ $siteSettings['site_name'] ?? 'LPM' }}
                </div>
            </a>
        </div>

        <nav class="sidebar-nav">
            @php
                $menus = \App\Models\Menu::getMenuTree();
                $authUser = auth()->user();

                // Would this root menu (and its subtree) show anything for this user?
                $rootShows = function ($menu) use ($authUser) {
                    if ($menu->tipe !== 'link') {
                        return false;
                    }
                    return ! $authUser || $menu->isVisibleTo($authUser);
                };

                // Drop a section header if no visible link follows it before the next section.
                $visibleMenus = collect();
                foreach ($menus as $index => $menu) {
                    if ($menu->tipe === 'section') {
                        $hasVisibleFollowing = false;
                        for ($i = $index + 1; $i < $menus->count(); $i++) {
                            if ($menus[$i]->tipe === 'section') break;
                            if ($rootShows($menus[$i])) { $hasVisibleFollowing = true; break; }
                        }
                        if (! $hasVisibleFollowing) continue;
                    }
                    $visibleMenus->push($menu);
                }
            @endphp
            @foreach($visibleMenus as $menu)
                @if($menu->tipe == 'section')
                    <div class="nav-section">{{ $menu->nama }}</div>
                @elseif($menu->tipe == 'divider')
                    <hr class="my-2 border-secondary">
                @else
                    @include('partials.admin-sidebar-item', ['item' => $menu, 'depth' => 0, 'authUser' => $authUser])
                @endif
            @endforeach
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="toggle-sidebar" id="toggleSidebar" type="button" aria-label="{{ __('admin.toggle_sidebar') }}" title="{{ __('admin.toggle_sidebar') }}">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            <div class="header-right">
                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('admin.view_site') }}
                </a>

                <div class="dropdown notification-dropdown">
                    <a href="#" class="toggle-sidebar position-relative" data-bs-toggle="dropdown" title="{{ __('admin.notifications') }}">
                        <i class="bi bi-bell"></i>
                        @if($unreadNotificationsCount > 0)
                        <span class="badge bg-danger rounded-pill position-absolute" style="top: -4px; right: -4px; font-size: 0.65rem;">
                            {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
                        </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 340px; max-height: 420px; overflow-y: auto;">
                        <li class="px-3 py-2 fw-bold border-bottom">{{ __('admin.notifications') }}</li>
                        @forelse($recentNotifications as $notif)
                        <li>
                            <a href="{{ route('admin.notifications.read', $notif->id) }}" class="dropdown-item text-wrap {{ $notif->read_at ? '' : 'bg-light fw-semibold' }}">
                                <div class="small">{{ $notif->data['message'] ?? 'Notifikasi' }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $notif->created_at->diffForHumans() }}</div>
                            </a>
                        </li>
                        @empty
                        <li class="px-3 py-3 text-center text-muted small">{{ __('admin.no_notifications') }}</li>
                        @endforelse
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center small" href="{{ route('admin.notifications.index') }}">{{ __('admin.view_all_notifications') }}</a></li>
                    </ul>
                </div>

                @if(auth()->user()->isAdmin())
                <div class="dropdown settings-dropdown">
                    <a href="#" class="toggle-sidebar" data-bs-toggle="dropdown" title="{{ __('admin.settings') }}">
                        <i class="bi bi-gear"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('admin.pengaturan.index') }}"><i class="bi bi-sliders me-2"></i>{{ __('admin.general_settings') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.pengaturan.template') }}"><i class="bi bi-palette me-2"></i>{{ __('admin.template_settings') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>{{ __('admin.users') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.users.create') }}"><i class="bi bi-person-plus me-2"></i>{{ __('admin.add_user') }}</a></li>
                    </ul>
                </div>
                @endif

                <div class="dropdown user-dropdown">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                        @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" class="user-avatar">
                        @else
                        <div class="user-avatar bg-primary text-white d-flex align-items-center justify-content-center">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        @endif
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>{{ __('admin.profile') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>{{ __('admin.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- jQuery (required by Summernote and other admin widgets) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Sidebar
        // Desktop (>=992px): hide/show the sidebar and let the content reflow; state is remembered.
        // Mobile (<992px): slide the sidebar in as an overlay.
        (function () {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');
            var toggle = document.getElementById('toggleSidebar');
            var isDesktop = function () { return window.matchMedia('(min-width: 992px)').matches; };

            toggle.addEventListener('click', function () {
                if (isDesktop()) {
                    var collapsed = document.body.classList.toggle('sidebar-collapsed');
                    try { localStorage.setItem('admin.sidebarCollapsed', collapsed ? '1' : '0'); } catch (e) {}
                } else {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                }
            });

            overlay.addEventListener('click', function () {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });

            // Leaving mobile: drop the overlay state so it can't linger on desktop
            window.addEventListener('resize', function () {
                if (isDesktop()) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            });

            // Collapsible sidebar groups (remember open/closed per group)
            var KEY = 'admin.menuGroup.';
            document.querySelectorAll('.sidebar-nav .nav-group').forEach(function (group) {
                var id = group.getAttribute('data-menu-id');
                var hasActive = !!group.querySelector('.nav-link.active');
                var saved = null;
                try { saved = localStorage.getItem(KEY + id); } catch (e) {}
                if (hasActive || saved === '1') group.classList.add('is-open');
                else if (saved === '0') group.classList.remove('is-open');
            });
            sidebar.addEventListener('click', function (e) {
                var toggle = e.target.closest('.nav-group-toggle');
                if (!toggle) return;
                e.preventDefault();
                var group = toggle.closest('.nav-group');
                var open = group.classList.toggle('is-open');
                try { localStorage.setItem(KEY + group.getAttribute('data-menu-id'), open ? '1' : '0'); } catch (e2) {}
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>