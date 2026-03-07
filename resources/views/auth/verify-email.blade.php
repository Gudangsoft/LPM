@extends('layouts.auth')

@section('title', 'Verifikasi Email')

@section('content')
    <h4 class="text-center mb-4">Verifikasi Email</h4>

    <div class="text-center mb-4">
        <i class="bi bi-envelope-check text-primary" style="font-size: 4rem;"></i>
    </div>

    <p class="text-muted text-center mb-4">
        Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan? Jika Anda tidak menerima email tersebut, kami akan dengan senang hati mengirimkan email lainnya.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-4">
            Link verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-envelope me-1"></i>Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>
@endsection