@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <h4 class="fw-bold mb-1">Selamat Datang Kembali</h4>
    <p class="text-muted small mb-4">Masuk dengan akun Anda untuk mengakses Sistem Informasi LPM</p>

    @if (!empty($siteSettings['demo_login_enabled']) && !empty($siteSettings['demo_login_email']) && !empty($siteSettings['demo_login_password']))
        <div class="demo-login-box mb-4">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-stars text-warning fs-5"></i>
                <div class="flex-grow-1">
                    <p class="mb-1 fw-semibold small">{{ $siteSettings['demo_login_note'] ?? 'Ingin mencoba tanpa akun?' }}</p>
                    <p class="mb-2 text-muted small">
                        {{ $siteSettings['demo_login_email'] }} / {{ $siteSettings['demo_login_password'] }}
                    </p>
                    <button type="button" id="demoLoginBtn" class="btn btn-sm btn-outline-primary"
                        data-email="{{ $siteSettings['demo_login_email'] }}"
                        data-password="{{ $siteSettings['demo_login_password'] }}">
                        <i class="bi bi-lightning-charge me-1"></i>Masuk sebagai Demo
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('admin.email') }}</label>
            <div class="input-group has-validation">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('admin.password') }}</label>
            <div class="input-group has-validation">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="captcha" class="form-label">Verifikasi Keamanan</label>
            <div class="captcha-box d-flex align-items-center justify-content-between mb-2" data-num1="{{ $captchaNum1 }}" data-num2="{{ $captchaNum2 }}">
                <div class="captcha-question">
                    <i class="bi bi-shield-check me-1 text-primary"></i>
                    <span>{{ $captchaNum1 }} + {{ $captchaNum2 }} = ?</span>
                </div>
                <a href="{{ route('login') }}" class="text-decoration-none small" title="Ganti soal captcha">
                    <i class="bi bi-arrow-clockwise"></i> Ganti
                </a>
            </div>
            <input type="number" inputmode="numeric" class="form-control @error('captcha') is-invalid @enderror" id="captcha" name="captcha" placeholder="Masukkan hasil penjumlahan" required>
            @error('captcha')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-decoration-none small">Lupa password?</a>
            @endif
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('menu.login') }}
            </button>
        </div>

        @if (Route::has('register'))
        <p class="text-center text-muted mb-0">
            Belum punya akun? <a href="{{ route('register') }}" class="text-decoration-none">{{ __('menu.register') }}</a>
        </p>
        @endif
    </form>

    <hr class="my-4">

    <div class="text-center">
        <a href="{{ route('home') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i>{{ __('messages.back_to_home') }}
        </a>
    </div>

    <style>
        .captcha-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
        }
        .captcha-question span {
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #1e293b;
            font-size: 1.05rem;
        }
        .demo-login-box {
            background: #fffbeb;
            border: 1px dashed #fbbf24;
            border-radius: 8px;
            padding: 14px 16px;
        }
        .demo-login-box p {
            margin-bottom: 0;
        }
    </style>

    <script>
        document.getElementById('togglePassword')?.addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });

        document.getElementById('demoLoginBtn')?.addEventListener('click', function () {
            document.getElementById('email').value = this.dataset.email;
            document.getElementById('password').value = this.dataset.password;

            const captchaBox = document.querySelector('.captcha-box');
            if (captchaBox) {
                const num1 = parseInt(captchaBox.dataset.num1, 10);
                const num2 = parseInt(captchaBox.dataset.num2, 10);
                document.getElementById('captcha').value = num1 + num2;
            }

            document.querySelector('form').submit();
        });
    </script>
@endsection
