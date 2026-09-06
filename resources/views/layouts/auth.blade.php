<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ $siteSettings['site_name'] ?? config('app.name') }}</title>

    <!-- Favicon -->
    @if(!empty($siteSettings['site_favicon']))
    <link rel="icon" type="image/x-icon" href="{{ Storage::url($siteSettings['site_favicon']) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #3b82f6;
            --accent-color: #f59e0b;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light-color);
            margin: 0;
        }

        .auth-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Left branding panel */
        .auth-brand {
            flex: 0 0 44%;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 64px 56px;
            color: #fff;
            background: linear-gradient(160deg, #0a2463 0%, var(--primary-color) 55%, var(--secondary-color) 100%);
        }

        .auth-brand-shape {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-brand-shape-1 {
            width: 380px;
            height: 380px;
            border: 2px solid rgba(255,255,255,0.08);
            top: -120px;
            right: -100px;
        }

        .auth-brand-shape-2 {
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.18) 0%, transparent 70%);
            bottom: -60px;
            left: -60px;
        }

        .auth-brand-shape-3 {
            width: 110px;
            height: 110px;
            border: 2px dashed rgba(255,255,255,0.15);
            bottom: 18%;
            right: 12%;
        }

        .auth-brand-content {
            position: relative;
            z-index: 2;
            max-width: 420px;
        }

        .auth-brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        .auth-brand-logo img {
            height: 48px;
            width: auto;
            border-radius: 8px;
            background: #fff;
            padding: 4px;
        }

        .auth-brand-logo span {
            font-weight: 700;
            font-size: 1.15rem;
            line-height: 1.3;
        }

        .auth-brand h1 {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 18px;
        }

        .auth-brand h1 em {
            font-style: normal;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-brand > .auth-brand-content > p {
            color: rgba(255,255,255,0.82);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 40px;
        }

        .auth-feature {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 22px;
        }

        .auth-feature-icon {
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .auth-feature-text strong {
            display: block;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .auth-feature-text span {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.75);
        }

        /* Right form panel */
        .auth-panel {
            flex: 1 1 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            background: #fff;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
        }

        .auth-mobile-brand {
            display: none;
            text-align: center;
            margin-bottom: 28px;
        }

        .auth-mobile-brand img {
            height: 48px;
            margin-bottom: 12px;
        }

        .auth-mobile-brand h1 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .auth-card .form-label {
            font-weight: 500;
            color: #374151;
            font-size: 0.9rem;
        }

        .auth-card .form-control {
            padding: 12px 14px;
            border: 1px solid #e5e7eb;
        }

        .auth-card .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .auth-card .input-group-text {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: var(--primary-color);
        }

        .auth-card .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .auth-card .btn-primary:hover {
            box-shadow: 0 10px 25px -8px rgba(30, 64, 175, 0.55);
            transform: translateY(-1px);
        }

        .auth-card a {
            color: var(--primary-color);
        }

        @media (max-width: 991.98px) {
            .auth-brand { display: none; }
            .auth-mobile-brand { display: block; }
            .auth-panel { padding: 32px 20px; }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-brand">
            <span class="auth-brand-shape auth-brand-shape-1"></span>
            <span class="auth-brand-shape auth-brand-shape-2"></span>
            <span class="auth-brand-shape auth-brand-shape-3"></span>

            <div class="auth-brand-content">
                <div class="auth-brand-logo">
                    @if(!empty($siteSettings['site_logo']))
                    <img src="{{ Storage::url($siteSettings['site_logo']) }}" alt="Logo">
                    @endif
                    <span>{{ $siteSettings['site_name'] ?? 'Sistem Informasi LPM' }}</span>
                </div>

                <h1>Menjaga <em>Mutu</em>, Membangun Kepercayaan</h1>
                <p>{{ $siteSettings['site_description'] ?? 'Portal terpadu Lembaga Penjaminan Mutu untuk mendukung tata kelola, transparansi, dan peningkatan mutu perguruan tinggi secara berkelanjutan.' }}</p>

                <div class="auth-feature">
                    <div class="auth-feature-icon"><i class="bi bi-patch-check"></i></div>
                    <div class="auth-feature-text">
                        <strong>{{ __('menu.quality_system') }}</strong>
                        <span>Standar dan dokumen mutu terkelola dalam satu sistem</span>
                    </div>
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i class="bi bi-clipboard-data"></i></div>
                    <div class="auth-feature-text">
                        <strong>{{ __('menu.internal_audit') }}</strong>
                        <span>Proses audit yang transparan dan terdokumentasi</span>
                    </div>
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i class="bi bi-award"></i></div>
                    <div class="auth-feature-text">
                        <strong>{{ __('menu.accreditation') }}</strong>
                        <span>Mendukung kesiapan akreditasi program studi</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-panel">
            <div class="auth-card">
                <div class="auth-mobile-brand">
                    @if(!empty($siteSettings['site_logo']))
                    <img src="{{ Storage::url($siteSettings['site_logo']) }}" alt="Logo">
                    @endif
                    <h1>{{ $siteSettings['site_name'] ?? 'Sistem Informasi LPM' }}</h1>
                </div>

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
