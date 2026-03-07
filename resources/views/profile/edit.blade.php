@extends('layouts.admin')

@section('title', __('admin.profile'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Profile Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>{{ __('admin.profile_information') ?? 'Informasi Profil' }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Perbarui informasi profil dan alamat email akun Anda.</p>

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('admin.name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('admin.email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2">
                                <p class="text-muted small">
                                    Alamat email Anda belum diverifikasi.
                                    <button form="send-verification" class="btn btn-link p-0 text-decoration-underline">
                                        Klik di sini untuk mengirim ulang email verifikasi.
                                    </button>
                                </p>

                                @if (session('status') === 'verification-link-sent')
                                <p class="text-success small">
                                    Link verifikasi baru telah dikirim ke alamat email Anda.
                                </p>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            @if (session('status') === 'profile-updated')
                            <span class="text-success align-self-center me-2">
                                <i class="bi bi-check-circle me-1"></i>Tersimpan
                            </span>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}
                            </button>
                        </div>
                    </form>

                    <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>

            <!-- Update Password -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-key me-2"></i>Ubah Password
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                                   id="current_password" name="current_password" autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                                   id="password" name="password" autocomplete="new-password">
                            @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            @if (session('status') === 'password-updated')
                            <span class="text-success align-self-center me-2">
                                <i class="bi bi-check-circle me-1"></i>Tersimpan
                            </span>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('admin.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Hapus Akun
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. 
                        Sebelum menghapus akun, harap unduh data atau informasi yang ingin Anda simpan.
                    </p>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="bi bi-trash me-1"></i>Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Apakah Anda yakin ingin menghapus akun?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. 
                        Masukkan password Anda untuk mengkonfirmasi bahwa Anda ingin menghapus akun secara permanen.
                    </p>

                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                               id="delete_password" name="password" placeholder="Password">
                        @error('password', 'userDeletion')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection