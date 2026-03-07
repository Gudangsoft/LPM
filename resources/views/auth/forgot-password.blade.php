@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
    <h4 class="text-center mb-4">Lupa Password</h4>

    <p class="text-muted text-center mb-4">
        Masukkan alamat email Anda dan kami akan mengirimkan link untuk reset password.
    </p>

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">{{ __('admin.email') }}</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-envelope me-1"></i>Kirim Link Reset
            </button>
        </div>

        <p class="text-center text-muted mb-0">
            <a href="{{ route('login') }}" class="text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke halaman login
            </a>
        </p>
    </form>
@endsection