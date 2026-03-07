@extends('layouts.auth')

@section('title', __('menu.register'))

@section('content')
    <h4 class="text-center mb-4">{{ __('menu.register') }}</h4>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('admin.name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('admin.email') }}</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('admin.password') }}</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">Minimal 8 karakter</small>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('admin.confirm_password') }}</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i>{{ __('menu.register') }}
            </button>
        </div>

        <p class="text-center text-muted mb-0">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none">{{ __('menu.login') }}</a>
        </p>
    </form>

    <hr class="my-4">

    <div class="text-center">
        <a href="{{ route('home') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i>{{ __('messages.back_to_home') }}
        </a>
    </div>
@endsection