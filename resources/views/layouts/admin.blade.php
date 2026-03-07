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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --sidebar-gradient: linear-gradient(180deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            --accent-color: #8b5cf6;
            --accent-glow: rgba(139, 92, 246, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 50%, #f8fafc 100%);
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
            background: rgba(139, 92, 246, 0.3);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(139, 92, 246, 0.5);
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
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid rgba(139, 92, 246, 0.1);
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.15) 0%, transparent 100%);
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
            filter: drop-shadow(0 4px 12px rgba(139, 92, 246, 0.4));
        }

        .sidebar-brand-icon {
            width: 42px;
            height: 42px;
            background: var(--primary-gradient);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 14px;
            font-size: 1.25rem;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
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
            padding: 20px 0;
            position: relative;
        }

        .nav-section {
            padding: 20px 24px 10px;
            font-size: 0.7rem;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            letter-spacing: 0.15em;
            font-weight: 600;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 2px 12px;
            border-radius: 12px;
            position: relative;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(139, 92, 246, 0.15);
            color: white;
            transform: translateX(4px);
        }

        .sidebar-nav .nav-link.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.4);
        }

        .sidebar-nav .nav-link.active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: white;
            border-radius: 0 4px 4px 0;
        }

        .sidebar-nav .nav-link i {
            width: 22px;
            margin-right: 14px;
            font-size: 1.15rem;
            opacity: 0.9;
        }

        .sidebar-nav .nav-link .badge {
            font-size: 0.65rem;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            height: var(--header-height);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .toggle-sidebar {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid rgba(139, 92, 246, 0.2);
            font-size: 1.25rem;
            color: #64748b;
            cursor: pointer;
            padding: 10px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .toggle-sidebar:hover {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-right .btn-outline-primary {
            border-color: rgba(139, 92, 246, 0.3);
            color: #7c3aed;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .header-right .btn-outline-primary:hover {
            background: var(--primary-gradient);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1e293b;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid rgba(139, 92, 246, 0.1);
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
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid rgba(139, 92, 246, 0.3);
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
            background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        }

        /* Content */
        .content {
            padding: 30px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .breadcrumb {
            margin-bottom: 0;
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: #7c3aed;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: #64748b;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.06);
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 35px rgba(139, 92, 246, 0.12);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, #fafbff 0%, #f8fafc 100%);
            border-bottom: 1px solid rgba(139, 92, 246, 0.08);
            padding: 20px 24px;
            font-weight: 700;
            color: #0f172a;
        }

        .card-body {
            padding: 24px;
        }

        .card-footer {
            background: linear-gradient(135deg, #fafbff 0%, #f8fafc 100%);
            border-top: 1px solid rgba(139, 92, 246, 0.08);
            padding: 16px 24px;
        }

        /* Stats Cards */
        .stat-card {
            padding: 24px;
            border-radius: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            transition: all 0.4s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px) scale(1.02);
        }

        .stat-card:hover::before {
            transform: translate(10%, 10%);
        }

        .stat-card.bg-blue { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
        .stat-card.bg-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-card.bg-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .stat-card.bg-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-card.bg-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .stat-card.bg-cyan { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
        .stat-card.bg-pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
        .stat-card.bg-indigo { background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%); }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #6366f1 100%);
            border-radius: 24px;
            padding: 36px 40px;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            min-height: 200px;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -100%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            animation: float 15s ease-in-out infinite;
        }

        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(30px, 30px) rotate(10deg); }
        }

        .welcome-banner .date-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .welcome-banner .welcome-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }

        .welcome-banner .welcome-subtitle {
            font-size: 1.25rem;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 14px;
        }

        .welcome-banner .welcome-period {
            font-size: 1rem;
            opacity: 0.85;
        }

        .welcome-banner .welcome-period strong {
            color: #fcd34d;
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
            background: linear-gradient(135deg, #fafbff 0%, #f8fafc 100%);
        }

        .table td {
            padding: 16px;
            vertical-align: middle;
            color: #334155;
        }

        .table-hover tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background: linear-gradient(135deg, #f0f4ff 0%, #faf5ff 100%);
        }

        /* Forms */
        .form-label {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 12px 18px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
        }

        /* Buttons */
        .btn {
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        }

        .btn-sm {
            padding: 8px 14px;
            font-size: 0.875rem;
            border-radius: 10px;
        }

        .btn-outline-primary {
            border: 2px solid rgba(139, 92, 246, 0.3);
            color: #7c3aed;
        }

        .btn-outline-primary:hover {
            background: var(--primary-gradient);
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
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 16px;
            padding: 18px 24px;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        .alert-info {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #3730a3;
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
            @endphp
            @foreach($menus as $menu)
                @if($menu->tipe == 'section')
                    {{-- Section Header --}}
                    <div class="nav-section">{{ $menu->nama }}</div>
                @elseif($menu->tipe == 'divider')
                    {{-- Divider --}}
                    <hr class="my-2 border-secondary">
                @elseif($menu->tipe == 'link')
                    {{-- Menu Item --}}
                    <a href="{{ $menu->getUrl() }}" class="nav-link {{ $menu->isActive() ? 'active' : '' }}">
                        @if($menu->icon)
                        <i class="bi {{ $menu->icon }}"></i>
                        @endif
                        {{ $menu->nama }}
                        @php $badgeCount = $menu->getBadgeCount(); @endphp
                        @if($badgeCount > 0)
                            <span class="badge {{ $menu->badge_class ?? 'bg-danger' }} ms-auto">{{ $badgeCount }}</span>
                        @endif
                    </a>
                    
                    {{-- Children --}}
                    @if($menu->children->count() > 0)
                        @foreach($menu->children as $child)
                        <a href="{{ $child->getUrl() }}" class="nav-link ps-5 {{ $child->isActive() ? 'active' : '' }}">
                            @if($child->icon)
                            <i class="bi {{ $child->icon }}"></i>
                            @endif
                            {{ $child->nama }}
                            @php $childBadge = $child->getBadgeCount(); @endphp
                            @if($childBadge > 0)
                                <span class="badge {{ $child->badge_class ?? 'bg-danger' }} ms-auto">{{ $childBadge }}</span>
                            @endif
                        </a>
                        @endforeach
                    @endif
                @endif
            @endforeach
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            <div class="header-right">
                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('admin.view_site') }}
                </a>
                
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

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle Sidebar
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        });

        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('show');
            this.classList.remove('show');
        });
    </script>

    @stack('scripts')
</body>
</html>