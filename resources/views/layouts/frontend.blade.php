<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', $siteSettings['site_description'] ?? 'Lembaga Penjaminan Mutu - Sistem Informasi LPM Kampus')">

    <title>@yield('title', 'Beranda') - {{ $siteSettings['site_name'] ?? config('app.name', 'LPM') }}</title>

    <!-- Favicon -->
    @if(!empty($siteSettings['site_favicon']))
    <link rel="icon" type="image/png" href="{{ Storage::url($siteSettings['site_favicon']) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #f59e0b;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark-color);
        }

        /* Navbar (multi-level, reference: lpm.uin-suka.ac.id) */
        .navbar-lpm {
            background: #ffffff;
            padding: 0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
            border-bottom: 3px solid var(--primary-color);
        }

        .navbar-lpm .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1rem;
            line-height: 1.15;
            color: var(--primary-color) !important;
            padding: 10px 0;
            text-transform: uppercase;
            letter-spacing: -.01em;
        }

        .navbar-lpm .navbar-toggler {
            border-color: rgba(30, 64, 175, .25);
            color: var(--primary-color);
            padding: 6px 10px;
        }
        .navbar-lpm .navbar-toggler:focus { box-shadow: none; }

        .navbar-lpm .navbar-nav { align-items: stretch; }
        .navbar-lpm .nav-item { display: flex; }

        .navbar-lpm .nav-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 4px;
            color: var(--primary-color) !important;
            font-weight: 700;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .02em;
            padding: 22px 13px !important;
            transition: background-color .18s ease, color .18s ease;
        }

        .navbar-lpm .nav-link:hover,
        .navbar-lpm .nav-link.show {
            background: rgba(30, 64, 175, .08);
        }

        .navbar-lpm .nav-link.active {
            color: #fff !important;
            background: var(--primary-color);
        }

        .navbar-lpm .dropdown-toggle::after {
            margin-left: 3px;
            vertical-align: middle;
            border-top-color: currentColor;
        }

        /* Dropdown panels */
        .navbar-lpm .dropdown-menu {
            border: none;
            border-top: 3px solid var(--primary-color);
            border-radius: 0 0 8px 8px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, .14);
            margin-top: 0;
            padding: 4px 0;
            min-width: 248px;
            max-width: 320px;
        }

        .navbar-lpm .dropdown-item {
            padding: 9px 18px;
            font-size: .85rem;
            font-weight: 500;
            line-height: 1.35;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal;
        }
        .navbar-lpm .dropdown-menu > li:last-child > .dropdown-item { border-bottom: 0; }

        .navbar-lpm .dropdown-item:hover,
        .navbar-lpm .dropdown-item:focus {
            background: #eff2f7;
            color: var(--primary-color);
        }
        .navbar-lpm .dropdown-item.active {
            background: var(--primary-color);
            color: #fff;
        }

        .navbar-lpm .dropdown-divider { margin: 4px 0; border-color: #e2e8f0; }

        /* Flyout submenu */
        .navbar-lpm .dropdown-submenu { position: relative; }
        .navbar-lpm .dropdown-submenu > .dropdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .navbar-lpm .dropdown-submenu > .dropdown-item::after {
            content: "\F285"; /* bi-chevron-right */
            font-family: "bootstrap-icons";
            font-size: .7rem;
            line-height: 1;
        }

        @media (min-width: 992px) {
            /* Anchor panels directly under their toggle (Popper is disabled via
               data-bs-display="static", so we position them ourselves). */
            .navbar-lpm .nav-item.dropdown > .dropdown-menu {
                position: absolute;
                top: 100%;
                left: 0;
                right: auto;
            }
            /* Panels near the end of the bar align to the right so they don't clip */
            .navbar-lpm .navbar-nav > .nav-item.dropdown:nth-last-child(-n+3) > .dropdown-menu {
                left: auto;
                right: 0;
            }
            .navbar-lpm .nav-item.dropdown:hover > .dropdown-menu { display: block; }

            /* Flyout submenu opens beside its row */
            .navbar-lpm .dropdown-submenu > .dropdown-menu {
                position: absolute;
                top: -7px;
                right: 100%;
                left: auto;
                margin: 0;
                border-radius: 8px;
            }
            .navbar-lpm .dropdown-submenu:hover > .dropdown-menu { display: block; }
        }

        @media (max-width: 991.98px) {
            .navbar-lpm .navbar-collapse {
                max-height: 76vh;
                overflow-y: auto;
                margin-top: 8px;
            }
            .navbar-lpm .nav-item { display: block; }
            .navbar-lpm .nav-link { padding: 12px 6px !important; border-radius: 6px; }
            .navbar-lpm .dropdown-menu {
                border: none;
                box-shadow: none;
                background: #f8fafc;
                border-radius: 8px;
                margin: 0 0 6px 10px;
            }
            .navbar-lpm .dropdown-submenu > .dropdown-menu { display: none; }
            .navbar-lpm .dropdown-submenu.open > .dropdown-menu { display: block; }
            .navbar-lpm .dropdown-submenu > .dropdown-item::after { transition: transform .2s ease; }
            .navbar-lpm .dropdown-submenu.open > .dropdown-item::after { transform: rotate(90deg); }
        }

        /* Top Bar */
        .top-bar {
            background: var(--dark-color);
            color: white;
            padding: 8px 0;
            font-size: 0.85rem;
        }

        .top-bar a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s;
        }

        .top-bar a:hover {
            color: white;
        }

        /* Hero Section - Fullscreen Cinematic Slider */
        .hero-section {
            position: relative;
            height: 100vh;
            min-height: 600px;
            max-height: 900px;
            overflow: hidden;
        }

        .hero-section .carousel,
        .hero-section .carousel-inner,
        .hero-section .carousel-item {
            height: 100%;
        }

        .hero-slide {
            position: relative;
            height: 100%;
            display: flex;
            align-items: center;
        }

        .hero-slide-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .hero-slide-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1);
            transition: transform 8s ease-out;
        }

        .carousel-item.active .hero-slide-bg img {
            transform: scale(1.1);
        }

        .hero-slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(10, 36, 99, 0.92) 0%, rgba(30, 58, 138, 0.85) 50%, rgba(15, 23, 42, 0.9) 100%);
            z-index: 1;
        }

        .hero-slide-content {
            position: relative;
            z-index: 2;
            width: 100%;
            padding: 0 5%;
        }

        .hero-content-wrapper {
            max-width: 800px;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 10px 24px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(245, 158, 11, 0.4);
            animation: slideInLeft 0.8s ease-out;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            line-height: 1.1;
            margin-bottom: 25px;
            text-shadow: 2px 4px 20px rgba(0,0,0,0.3);
            animation: slideInUp 0.8s ease-out 0.2s both;
        }

        .hero-title span {
            background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-description {
            font-size: 1.25rem;
            color: rgba(255,255,255,0.9);
            line-height: 1.8;
            margin-bottom: 35px;
            max-width: 600px;
            animation: slideInUp 0.8s ease-out 0.4s both;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            animation: slideInUp 0.8s ease-out 0.6s both;
        }

        .hero-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 16px 36px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
            border: none;
        }

        .hero-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(59, 130, 246, 0.5);
            color: white;
        }

        .hero-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            color: white;
            padding: 16px 36px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .hero-btn-secondary:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            color: white;
            transform: translateY(-3px);
        }

        /* Floating Stats */
        .hero-stats {
            position: absolute;
            right: 5%;
            top: 50%;
            transform: translateY(-50%);
            z-index: 3;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .hero-stat-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 25px 30px;
            text-align: center;
            color: white;
            min-width: 180px;
            transition: all 0.3s ease;
        }

        .hero-stat-card:hover {
            transform: translateX(-10px);
            background: rgba(255,255,255,0.15);
        }

        .hero-stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-top: 5px;
        }

        /* Decorative Elements */
        .hero-shape {
            position: absolute;
            pointer-events: none;
            z-index: 2;
        }

        .hero-shape-1 {
            width: 400px;
            height: 400px;
            border: 2px solid rgba(255,255,255,0.08);
            border-radius: 50%;
            top: -150px;
            right: 20%;
            animation: float 6s ease-in-out infinite;
        }

        .hero-shape-2 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            bottom: 10%;
            left: 10%;
            animation: float 8s ease-in-out infinite reverse;
        }

        .hero-shape-3 {
            width: 120px;
            height: 120px;
            border: 2px dashed rgba(251, 191, 36, 0.3);
            border-radius: 50%;
            top: 30%;
            right: 35%;
            animation: spin 20s linear infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Navigation */
        .hero-section .carousel-control-prev,
        .hero-section .carousel-control-next {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 1;
            transition: all 0.3s ease;
        }

        .hero-section .carousel-control-prev { left: 30px; }
        .hero-section .carousel-control-next { right: 30px; }

        .hero-section .carousel-control-prev:hover,
        .hero-section .carousel-control-next:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-50%) scale(1.1);
        }

        /* Progress Indicators */
        .hero-section .carousel-indicators {
            bottom: 40px;
            left: 5%;
            justify-content: flex-start;
            margin: 0;
            gap: 12px;
        }

        .hero-section .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            border: 2px solid transparent;
            transition: all 0.3s ease;
            padding: 0;
        }

        .hero-section .carousel-indicators button.active {
            background: transparent;
            border-color: white;
            transform: scale(1.3);
        }

        /* Slide Progress Bar */
        .hero-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255,255,255,0.1);
            z-index: 10;
        }

        .hero-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            width: 0%;
            animation: progress 6s linear infinite;
        }

        @keyframes progress {
            from { width: 0%; }
            to { width: 100%; }
        }

        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            color: white;
            text-align: center;
            animation: bounce 2s ease-in-out infinite;
        }

        .scroll-indicator span {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            opacity: 0.7;
        }

        .scroll-indicator i {
            font-size: 1.5rem;
        }

        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(10px); }
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .hero-stats {
                display: none;
            }
        }

        @media (max-width: 991px) {
            .hero-section {
                height: auto;
                min-height: 500px;
            }

            .hero-slide {
                padding: 100px 0 80px;
            }

            .hero-title {
                font-size: 2.25rem;
            }

            .hero-description {
                font-size: 1rem;
            }

            .hero-buttons {
                justify-content: center;
            }

            .hero-content-wrapper {
                text-align: center;
            }

            .hero-section .carousel-control-prev,
            .hero-section .carousel-control-next {
                display: none;
            }

            .hero-section .carousel-indicators {
                left: 50%;
                transform: translateX(-50%);
                justify-content: center;
            }

            .scroll-indicator {
                display: none;
            }

            .hero-shape {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.75rem;
            }

            .hero-btn-primary,
            .hero-btn-secondary {
                padding: 14px 28px;
                font-size: 0.9rem;
            }
        }

        /* Section Title */
        .section-title {
            position: relative;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-weight: 700;
            color: var(--primary-color);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--accent-color);
            border-radius: 2px;
        }

        .section-title.text-center::after {
            left: 50%;
            transform: translateX(-50%);
        }

        /* Cards */
        .card-berita {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s;
            height: 100%;
        }

        .card-berita:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }

        .card-berita .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .card-berita .card-body {
            padding: 20px;
        }

        .card-berita .card-title {
            font-weight: 600;
            font-size: 1.1rem;
            line-height: 1.4;
        }

        .card-berita .card-title a {
            color: var(--dark-color);
            text-decoration: none;
        }

        .card-berita .card-title a:hover {
            color: var(--secondary-color);
        }

        /* Sidebar */
        .sidebar-widget {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .sidebar-widget h5 {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-color);
        }

        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 60px 0 30px;
        }

        .footer h5 {
            font-weight: 700;
            margin-bottom: 25px;
            color: var(--accent-color);
        }

        .footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: white;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            margin-top: 40px;
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 60px 0;
            color: white;
        }

        .page-header h1 {
            font-weight: 700;
            margin-bottom: 10px;
        }

        .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
        }

        .breadcrumb-item.active {
            color: white;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .navbar-lpm .nav-link {
                padding: 12px 0 !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('partials.impersonation-banner')

    <!-- Top Bar -->
    <div class="top-bar d-none d-lg-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span><i class="bi bi-envelope me-2"></i> {{ $siteSettings['contact_email'] ?? 'lpm@kampus.ac.id' }}</span>
                    <span class="ms-4"><i class="bi bi-telephone me-2"></i> {{ $siteSettings['contact_phone'] ?? '(021) 1234567' }}</span>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('language.switch', 'id') }}" class="me-2 {{ app()->getLocale() == 'id' ? 'fw-bold text-white' : '' }}">ID</a>
                    <span>|</span>
                    <a href="{{ route('language.switch', 'en') }}" class="ms-2 {{ app()->getLocale() == 'en' ? 'fw-bold text-white' : '' }}">EN</a>
                    @auth
                        <span class="ms-4">|</span>
                        <a href="{{ route('admin.dashboard') }}" class="ms-4"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    @else
                        <span class="ms-4">|</span>
                        <a href="{{ route('login') }}" class="ms-4"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-lpm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                @if(!empty($siteSettings['site_logo']))
                <img src="{{ Storage::url($siteSettings['site_logo']) }}" alt="Logo" height="40" class="d-inline-block me-2">
                @else
                <img src="{{ asset('images/logo.png') }}" alt="Logo" height="40" class="d-inline-block me-2" onerror="this.style.display='none'">
                @endif
                {{ $siteSettings['site_name'] ?? 'LPM KAMPUS' }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                @php
                    try {
                        $frontendMenu = \App\Models\Menu::getFrontendTree();
                    } catch (\Throwable $e) {
                        $frontendMenu = collect();
                    }
                @endphp
                <ul class="navbar-nav ms-auto">
                    @forelse($frontendMenu as $navItem)
                        @include('partials.frontend-menu-item', ['item' => $navItem, 'depth' => 0])
                    @empty
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('berita.*') ? 'active' : '' }}" href="{{ route('berita.index') }}">{{ __('menu.news') }}</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('dokumen.*') ? 'active' : '' }}" href="{{ route('dokumen.index') }}">{{ __('menu.documents') }}</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('kontak.*') ? 'active' : '' }}" href="{{ route('kontak.index') }}">{{ __('menu.contact') }}</a></li>
                    @endforelse
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5>{{ __('footer.about_lpm') }}</h5>
                    <p class="text-white-50">
                        {{ $siteSettings['site_description'] ?? __('footer.about_text') }}
                    </p>
                    <div class="social-links mt-3">
                        @if(!empty($siteSettings['social_facebook']))
                        <a href="{{ $siteSettings['social_facebook'] }}" target="_blank" class="me-3"><i class="bi bi-facebook fs-5"></i></a>
                        @endif
                        @if(!empty($siteSettings['social_instagram']))
                        <a href="{{ $siteSettings['social_instagram'] }}" target="_blank" class="me-3"><i class="bi bi-instagram fs-5"></i></a>
                        @endif
                        @if(!empty($siteSettings['social_twitter']))
                        <a href="{{ $siteSettings['social_twitter'] }}" target="_blank" class="me-3"><i class="bi bi-twitter-x fs-5"></i></a>
                        @endif
                        @if(!empty($siteSettings['social_youtube']))
                        <a href="{{ $siteSettings['social_youtube'] }}" target="_blank"><i class="bi bi-youtube fs-5"></i></a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5>{{ __('footer.quick_links') }}</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="{{ route('home') }}"><i class="bi bi-chevron-right me-2"></i>{{ __('menu.home') }}</a></li>
                        <li><a href="{{ route('profil') }}"><i class="bi bi-chevron-right me-2"></i>{{ __('menu.profile') }}</a></li>
                        <li><a href="{{ route('berita.index') }}"><i class="bi bi-chevron-right me-2"></i>{{ __('menu.news') }}</a></li>
                        <li><a href="{{ route('dokumen.index') }}"><i class="bi bi-chevron-right me-2"></i>{{ __('menu.documents') }}</a></li>
                        <li><a href="{{ route('kontak.index') }}"><i class="bi bi-chevron-right me-2"></i>{{ __('menu.contact') }}</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
                    <h5>{{ __('footer.services') }}</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="{{ route('sistem-penjaminan-mutu') }}"><i class="bi bi-chevron-right me-2"></i>{{ __('menu.quality_system') }}</a></li>
                        <li><a href="{{ route('audit-mutu-internal') }}"><i class="bi bi-chevron-right me-2"></i>{{ __('menu.internal_audit') }}</a></li>
                        <li><a href="{{ route('akreditasi') }}"><i class="bi bi-chevron-right me-2"></i>{{ __('menu.accreditation') }}</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5>{{ __('footer.contact') }}</h5>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> {{ $siteSettings['contact_address'] ?? 'Jl. Kampus No. 1, Kota' }}</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i> {{ $siteSettings['contact_phone'] ?? '(021) 1234567' }}</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> {{ $siteSettings['contact_email'] ?? 'lpm@kampus.ac.id' }}</li>
                        <li><i class="bi bi-clock me-2"></i> Senin - Jumat: 08:00 - 16:00</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom text-center text-white-50">
                <p class="mb-0">&copy; {{ date('Y') }} {{ $siteSettings['site_name'] ?? 'Lembaga Penjaminan Mutu' }}. {{ $siteSettings['footer_text'] ?? 'All rights reserved.' }}</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <script>
        AOS.init({
            duration: 800,
            once: true
        });

        // Multi-level navbar: toggle flyout submenus on mobile (Bootstrap 5 has no submenu support)
        document.querySelectorAll('.navbar-lpm .dropdown-submenu > .dropdown-item').forEach(function (item) {
            item.addEventListener('click', function (e) {
                if (window.matchMedia('(max-width: 991.98px)').matches) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.parentElement.classList.toggle('open');
                }
            });
        });

        // Reset any open submenus when the parent dropdown closes
        document.querySelectorAll('.navbar-lpm .nav-item.dropdown').forEach(function (dd) {
            dd.addEventListener('hidden.bs.dropdown', function () {
                dd.querySelectorAll('.dropdown-submenu.open').forEach(function (s) { s.classList.remove('open'); });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>