@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <h4 class="text-center mb-4">{{ __('menu.login') }}</h4>

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('admin.email') }}</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('admin.password') }}</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
            @error('password')
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
@endsection