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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }
        .auth-card .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-card .logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }
        .auth-card .logo span {
            color: #667eea;
        }
        .form-label {
            font-weight: 500;
            color: #374151;
        }
        .form-control {
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="logo">
            @if(!empty($siteSettings['site_logo']))
            <img src="{{ Storage::url($siteSettings['site_logo']) }}" alt="Logo" style="height: 60px; width: auto;" class="mb-3">
            @endif
            <h1>{{ $siteSettings['site_name'] ?? 'Sistem Informasi LPM' }}</h1>
        </div>
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>