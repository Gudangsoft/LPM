@extends('layouts.admin')

@section('title', 'Pengaturan Akun')

@push('styles')
<style>
    .profile-banner {
        overflow: hidden;
        border: 0;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
    }
    .profile-banner__cover {
        height: 108px;
        background: linear-gradient(135deg, var(--primary-color, #1e40af) 0%, var(--secondary-color, #3b82f6) 100%);
    }
    .profile-banner__body {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 20px;
        padding: 0 24px 22px;
        margin-top: -48px;
    }
    .profile-banner__avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }
    .profile-banner__avatar {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        background: #e2e8f0;
        box-shadow: 0 2px 10px rgba(0,0,0,.15);
        display: block;
    }
    .profile-banner__avatar-edit {
        position: absolute;
        right: -2px;
        bottom: -2px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--primary-color, #1e40af);
        color: #fff;
        border: 2px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .8rem;
        cursor: pointer;
        transition: background-color .15s ease;
    }
    .profile-banner__avatar-edit:hover { background: #1e3a8a; }
    .profile-banner__meta {
        padding-top: 52px;
        flex: 1 1 260px;
        min-width: 0;
    }
    .profile-banner__meta h4 { margin-bottom: 2px; }

    .profile-nav .list-group-item {
        border: 0;
        border-radius: 10px !important;
        margin-bottom: 4px;
        font-weight: 500;
        color: #334155;
        padding: .6rem .9rem;
    }
    .profile-nav .list-group-item.active,
    .profile-nav .list-group-item:hover {
        background: #eff6ff;
        color: var(--primary-color, #1e40af);
    }
    .profile-nav .list-group-item.text-danger.active,
    .profile-nav .list-group-item.text-danger:hover {
        background: #fef2f2;
        color: #dc2626 !important;
    }

    .session-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .session-row:last-child { border-bottom: 0; }
    .session-row__icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title">Pengaturan Akun</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Pengaturan Akun</li>
            </ol>
        </nav>
    </div>

    @if(session('status') === 'other-sessions-closed')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>Berhasil keluar dari semua perangkat lain.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @elseif(session('status') === 'session-revoked')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>Sesi tersebut berhasil dikeluarkan.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Identity banner --}}
    <div class="card profile-banner mb-4">
        <div class="profile-banner__cover"></div>
        <div class="profile-banner__body">
            <div class="profile-banner__avatar-wrap">
                @if($user->avatar)
                <img id="avatarPreview" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="profile-banner__avatar">
                @else
                <img id="avatarPreview" src="data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'%3E%3Crect width='96' height='96' fill='%23e2e8f0'/%3E%3C/svg%3E" alt="{{ $user->name }}" class="profile-banner__avatar">
                @endif
                <label for="avatarInput" class="profile-banner__avatar-edit" title="Ganti foto profil">
                    <i class="bi bi-camera-fill"></i>
                </label>
                {{-- Tied to #profileForm via the form attribute so it submits with it despite living in this banner. --}}
                <input type="file" id="avatarInput" name="avatar" form="profileForm" accept="image/png,image/jpeg,image/jpg,image/gif,image/webp" class="d-none">
            </div>
            <div class="profile-banner__meta">
                <h4>{{ $user->name }}</h4>
                <div class="text-muted">{{ $user->email }}</div>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @forelse($user->roles as $role)
                        <span class="badge bg-primary-subtle text-primary-emphasis border">{{ $role->name }}</span>
                    @empty
                        <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                    @endforelse
                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                        {{ $user->is_active ? __('admin.active') : __('admin.inactive') }}
                    </span>
                </div>
                <div class="small text-muted mt-2">
                    <i class="bi bi-calendar3 me-1"></i>Bergabung {{ $user->created_at->format('d M Y') }}
                    @if($user->auditor)
                        &nbsp;&middot;&nbsp;<i class="bi bi-patch-check me-1"></i>Auditor{{ $user->auditor->bidang_keahlian ? ' — ' . $user->auditor->bidang_keahlian : '' }}
                    @endif
                    @if($user->prodiDikepalai->isNotEmpty())
                        &nbsp;&middot;&nbsp;<i class="bi bi-mortarboard me-1"></i>Kaprodi {{ $user->prodiDikepalai->pluck('nama')->implode(', ') }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Section navigation --}}
        <div class="col-lg-3">
            <div class="list-group profile-nav sticky-lg-top" style="top: 86px;">
                <a class="list-group-item list-group-item-action active" href="#profil"><i class="bi bi-person me-2"></i>Informasi Profil</a>
                <a class="list-group-item list-group-item-action" href="#keamanan"><i class="bi bi-key me-2"></i>Keamanan</a>
                <a class="list-group-item list-group-item-action" href="#sesi"><i class="bi bi-laptop me-2"></i>Sesi Aktif</a>
                <a class="list-group-item list-group-item-action text-danger" href="#bahaya"><i class="bi bi-exclamation-triangle me-2"></i>Zona Berbahaya</a>
            </div>
        </div>

        <div class="col-lg-9">
            {{-- Informasi Profil --}}
            <div id="profil" class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>{{ __('admin.profile_information') ?? 'Informasi Profil' }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Perbarui nama, email, dan foto profil akun Anda. Klik foto di atas untuk menggantinya.</p>

                    <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
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
                                <p class="text-success small">Link verifikasi baru telah dikirim ke alamat email Anda.</p>
                                @endif
                            </div>
                            @endif
                        </div>

                        @error('avatar')<div class="text-danger small mb-3">{{ $message }}</div>@enderror

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

                    @if($user->avatar)
                    <form action="{{ route('profile.update') }}" method="POST" class="mt-2" onsubmit="return confirm('Hapus foto profil?')">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="remove_avatar" value="1">
                        <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                            <i class="bi bi-trash me-1"></i>Hapus foto profil
                        </button>
                    </form>
                    @endif

                    <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>

            {{-- Keamanan --}}
            <div id="keamanan" class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-key me-2"></i>Keamanan</h5>
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

            {{-- Sesi Aktif --}}
            <div id="sesi" class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-laptop me-2"></i>Sesi Aktif</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Perangkat yang sedang atau baru saja masuk ke akun Anda.</p>

                    @forelse($sessions as $s)
                    <div class="session-row">
                        <div class="session-row__icon"><i class="bi {{ $s->icon }}"></i></div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">
                                {{ $s->browser }} &middot; {{ $s->os }}
                                @if($s->is_current)<span class="badge bg-success ms-1">Perangkat ini</span>@endif
                            </div>
                            <div class="small text-muted">{{ $s->ip_address }} &middot; Aktif {{ $s->last_active->diffForHumans() }}</div>
                        </div>
                        @unless($s->is_current)
                        <form action="{{ route('profile.sessions.revoke', $s->id) }}" method="POST" onsubmit="return confirm('Keluarkan sesi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Keluar</button>
                        </form>
                        @endunless
                    </div>
                    @empty
                    <p class="text-muted mb-0">Riwayat sesi tidak tersedia untuk konfigurasi server ini.</p>
                    @endforelse

                    @if($sessions->count() > 1)
                    <hr>
                    <p class="small text-muted mb-2">Masukkan password Anda untuk keluar dari semua perangkat lain selain yang ini.</p>
                    <form action="{{ route('profile.sessions.logout-others') }}" method="POST" class="row g-2 align-items-start">
                        @csrf
                        <div class="col-sm-8">
                            <input type="password" name="password" class="form-control form-control-sm @error('password', 'logoutOtherSessions') is-invalid @enderror" placeholder="Password Anda">
                            @error('password', 'logoutOtherSessions')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-box-arrow-right me-1"></i>Keluar Perangkat Lain
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Zona Berbahaya --}}
            <div id="bahaya" class="card border-danger">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Zona Berbahaya</h5>
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

    {{-- Delete Account Modal --}}
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

@push('scripts')
<script>
(function () {
    // Live avatar preview - the file input lives in the banner but submits with #profileForm.
    var input = document.getElementById('avatarInput');
    var preview = document.getElementById('avatarPreview');
    if (input && preview) {
        input.addEventListener('change', function () {
            var file = this.files[0];
            if (file) preview.src = URL.createObjectURL(file);
        });
    }

    // Lightweight scrollspy for the settings section nav.
    var navLinks = Array.prototype.slice.call(document.querySelectorAll('.profile-nav .list-group-item'));
    var sections = navLinks
        .map(function (a) { return document.querySelector(a.getAttribute('href')); })
        .filter(Boolean);

    if ('IntersectionObserver' in window && sections.length) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                navLinks.forEach(function (a) { a.classList.remove('active'); });
                var match = navLinks.find(function (a) { return a.getAttribute('href') === '#' + entry.target.id; });
                if (match) match.classList.add('active');
            });
        }, { rootMargin: '-20% 0px -70% 0px' });

        sections.forEach(function (s) { observer.observe(s); });
    }
})();
</script>
@endpush
