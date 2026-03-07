@extends('layouts.auth')

@section('title', 'Konfirmasi Password')

@section('content')
    <h4 class="text-center mb-4">Konfirmasi Password</h4>

    <div class="text-center mb-4">
        <i class="bi bi-shield-lock text-warning" style="font-size: 4rem;"></i>
    </div>

    <p class="text-muted text-center mb-4">
        Ini adalah area yang aman dari aplikasi. Silakan konfirmasi password Anda sebelum melanjutkan.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-4">
            <label for="password" class="form-label">{{ __('admin.password') }}</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Konfirmasi
            </button>
        </div>
    </form>
@endsection