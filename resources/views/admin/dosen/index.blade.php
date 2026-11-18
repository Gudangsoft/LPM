@extends('layouts.admin')

@section('title', 'Data Dosen')

@section('content')
    @php
        $canManage = auth()->user()->hasPermission('dosen.manage');
        $isAdmin = auth()->user()->isAdmin();
    @endphp

    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Data Dosen</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">SPMI</li>
                    <li class="breadcrumb-item active">Data Dosen</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.dosen.export', request()->only('prodi_id')) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            @if($canManage)
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importDosenModal">
                <i class="bi bi-upload me-1"></i>Import Excel
            </button>
            <a href="{{ route('admin.dosen.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Dosen
            </a>
            @endif
            @if($isAdmin)
            <form action="{{ route('admin.siakad.sync.dosen') }}" method="POST" class="d-inline"
                  onsubmit="if (!confirm('Tarik data Dosen dari SIAKAD? Dosen baru ditambahkan, yang sudah ada diperbarui. Data yang kosong di SIAKAD tidak menghapus data di SPMI. Sebaiknya tarik Program Studi lebih dulu.')) return false; this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '<span class=&quot;spinner-border spinner-border-sm me-1&quot;></span>Menarik data...'; return true;">
                @csrf
                <button type="submit" class="btn btn-outline-dark" title="Terakhir: {{ \Illuminate\Support\Facades\Cache::get('siakad:last_sync:dosen', 'belum pernah') }}">
                    <i class="bi bi-cloud-download me-1"></i>Tarik dari SIAKAD
                </button>
            </form>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#buatAkunModal">
                <i class="bi bi-person-plus me-1"></i>Buatkan Akun
                @if($tanpaAkun)<span class="badge bg-primary ms-1">{{ $tanpaAkun }}</span>@endif
            </button>
            @endif
        </div>
    </div>

    <p class="text-muted mb-3">
        Data induk dosen per program studi. Data ini dapat diambil langsung ke DKPS Tabel 6
        dan muncul sebagai pilihan Dosen Pengampu di Evaluasi Pembelajaran dan Review RPS.
    </p>

    @if(session('akun_skipped'))
    <div class="alert alert-warning">
        <div class="fw-semibold mb-1"><i class="bi bi-person-x me-1"></i>{{ count(session('akun_skipped')) }} dosen belum dibuatkan akun:</div>
        <ul class="mb-0 small" style="max-height: 200px; overflow-y: auto;">
            @foreach(session('akun_skipped') as $s)
            <li>{{ $s }}</li>
            @endforeach
        </ul>
        <div class="small mt-1">Lengkapi email dosen tersebut (Edit), lalu klik "Buatkan Akun" lagi.</div>
    </div>
    @endif

    @if(session('import_warnings'))
    <div class="alert alert-warning">
        <div class="fw-semibold mb-1"><i class="bi bi-info-circle me-1"></i>Isian yang tidak dikenali (data tetap diimpor, isian ini dikosongkan):</div>
        <ul class="mb-0 small" style="max-height: 200px; overflow-y: auto;">
            @foreach(session('import_warnings') as $warn)
            <li>{{ $warn }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('import_errors'))
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Kesalahan pada file import:</div>
        <ul class="mb-0 small">
            @foreach(session('import_errors') as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Prodi</label>
                    <select name="prodi_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ request('prodi_id') == $p->id ? 'selected' : '' }}>{{ $p->jenjang }} {{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Jabatan</label>
                    <select name="jabatan" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach(\App\Models\Dosen::JABATAN as $key => $label)
                        <option value="{{ $key }}" {{ request('jabatan') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Nama / kode / NIDN / keahlian...">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            @if($isAdmin)
                            <th width="32"><input type="checkbox" class="form-check-input" id="checkAllDosen" title="Pilih semua di halaman ini"></th>
                            @endif
                            <th>Kode / NIDN</th>
                            <th>Nama & Kontak</th>
                            <th>Alamat & Email</th>
                            <th>Jns. Kel</th>
                            <th>Homebase</th>
                            <th>Lulusan</th>
                            <th>Golongan / Jabatan</th>
                            <th>Status</th>
                            <th>Akun</th>
                            @if($canManage)<th class="text-nowrap" style="width: 1%;">{{ __('admin.actions') }}</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dosen as $d)
                        <tr>
                            @if($isAdmin)
                            <td>
                                @unless($d->user_id)
                                <input type="checkbox" class="form-check-input dosen-check" name="dosen_ids[]" value="{{ $d->id }}" form="buatAkunForm" aria-label="Pilih {{ $d->nama }}">
                                @endunless
                            </td>
                            @endif
                            <td>
                                <div class="fw-semibold">{{ $d->kode_dosen ?? '-' }}</div>
                                <small class="text-muted">{{ $d->nidn_nidk ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $d->nama }}</div>
                                @if($d->no_hp)<small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $d->no_hp }}</small>@endif
                            </td>
                            <td style="max-width: 220px;">
                                <small class="d-block text-truncate" title="{{ $d->alamat }}">{{ $d->alamat ?? '-' }}</small>
                                @if($d->email)<small class="text-muted text-break">{{ $d->email }}</small>@endif
                            </td>
                            <td>{{ $d->jenis_kelamin ?? '-' }}</td>
                            <td><small>{{ optional($d->prodi)->jenjang }} {{ optional($d->prodi)->nama }}</small></td>
                            <td>
                                <small class="fw-semibold">{{ $d->pendidikan_terakhir ?? '-' }}</small>
                                @if($d->lulusan)<div class="small text-muted">{{ $d->lulusan }}</div>@endif
                            </td>
                            <td>
                                <small class="fw-semibold">{{ $d->golongan ?? '-' }}</small>
                                <div class="small text-muted">{{ $d->jabatan_label }}</div>
                            </td>
                            <td><span class="badge bg-{{ $d->is_active ? 'success' : 'secondary' }}">{{ $d->is_active ? 'Aktif' : 'Tidak Aktif' }}</span></td>
                            <td>
                                @if($d->user)
                                    @if($isAdmin)
                                    <a href="{{ route('admin.users.edit', $d->user) }}" class="badge bg-primary text-decoration-none" title="Atur akun & role">
                                        <i class="bi bi-person-check me-1"></i>Ada
                                    </a>
                                    @else
                                    <span class="badge bg-primary"><i class="bi bi-person-check me-1"></i>Ada</span>
                                    @endif
                                    <div class="small text-muted">{{ $d->user->roles->pluck('name')->join(', ') ?: 'Role belum diatur' }}</div>
                                @else
                                    <span class="text-muted small">{{ $d->email ? 'Belum' : 'Belum (tanpa email)' }}</span>
                                @endif
                            </td>
                            @if($canManage)
                            <td class="text-nowrap">
                                <a href="{{ route('admin.dosen.edit', $d) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.dosen.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="{{ 9 + ($canManage ? 1 : 0) + ($isAdmin ? 1 : 0) }}" class="text-center text-muted py-4">Belum ada data dosen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($dosen->hasPages())
        <div class="card-footer">{{ $dosen->links() }}</div>
        @endif
    </div>

    @if($isAdmin)
    <div class="modal fade" id="buatAkunModal" tabindex="-1" aria-labelledby="buatAkunModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.dosen.buat-akun') }}" method="POST" id="buatAkunForm" class="modal-content">
                @csrf
                <input type="hidden" name="prodi_id" value="{{ request('prodi_id') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="buatAkunModalLabel">Buatkan Akun Login Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Dosen</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="scope" id="scopeSelected" value="selected" checked>
                        <label class="form-check-label" for="scopeSelected">Yang dicentang di tabel (<span id="selectedCount">0</span> dosen)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="scope" id="scopeAll" value="all">
                        <label class="form-check-label" for="scopeAll">
                            Semua dosen yang belum punya akun ({{ $tanpaAkun }} dosen{{ request('prodi_id') ? ', sesuai filter prodi' : '' }})
                        </label>
                    </div>

                    <label class="form-label fw-semibold">Password awal</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="password_mode" id="pwNidn" value="nidn" checked>
                        <label class="form-check-label" for="pwNidn">NIDN/NIDK masing-masing dosen (bila kosong: Kode Dosen)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="password_mode" id="pwCustom" value="custom">
                        <label class="form-check-label" for="pwCustom">Satu password untuk semua</label>
                    </div>
                    <input type="text" name="password" id="pwCustomInput" class="form-control mt-2 mb-3" placeholder="Minimal 8 karakter" minlength="8" autocomplete="off" disabled>

                    <label for="buatAkunRole" class="form-label fw-semibold">Role <span class="text-muted small fw-normal">(opsional)</span></label>
                    <select name="role_id" id="buatAkunRole" class="form-select">
                        <option value="">-- Atur nanti di menu Pengguna --</option>
                        @foreach($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>

                    <div class="alert alert-info small mt-3 mb-0">
                        Login memakai <strong>email</strong> dosen; dosen tanpa email dilewati. Bila email sudah terdaftar
                        sebagai pengguna, dosen ditautkan ke akun tersebut. Akun tanpa role bisa login tetapi belum
                        dapat membuka menu apa pun sampai role-nya diatur.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Buatkan Akun</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    (function () {
        const checks = () => document.querySelectorAll('.dosen-check');
        const count = () => { document.getElementById('selectedCount').textContent = [...checks()].filter(c => c.checked).length; };
        document.getElementById('checkAllDosen')?.addEventListener('change', e => { checks().forEach(c => c.checked = e.target.checked); count(); });
        document.addEventListener('change', e => { if (e.target.classList.contains('dosen-check')) count(); });
        const pwInput = document.getElementById('pwCustomInput');
        document.querySelectorAll('input[name="password_mode"]').forEach(r => r.addEventListener('change', () => {
            pwInput.disabled = !document.getElementById('pwCustom').checked;
            pwInput.required = !pwInput.disabled;
        }));
        document.getElementById('buatAkunModal').addEventListener('show.bs.modal', () => {
            count();
            if ([...checks()].every(c => !c.checked)) document.getElementById('scopeAll').checked = true;
        });
    })();
    </script>
    @endpush
    @endif

    @if($canManage)
    <div class="modal fade" id="importDosenModal" tabindex="-1" aria-labelledby="importDosenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.dosen.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importDosenModalLabel">Import Data Dosen dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <ol class="small ps-3">
                        <li>Unduh <a href="{{ route('admin.dosen.template') }}">template import</a> (atau gunakan hasil Export Excel).</li>
                        <li>Bisa juga langsung memakai file ekspor dari SIAKAD. Kolom dibaca berdasarkan <strong>judul kolom</strong>, urutan bebas, kolom lain diabaikan.</li>
                        <li>Wajib ada kolom <strong>Nama</strong>. Kolom <strong>Homebase</strong> (kode/nama prodi) boleh tidak ada bila Homebase default di bawah dipilih.</li>
                        <li>Dosen yang sudah terdaftar (dicocokkan lewat NIDN, lalu Kode Dosen) akan <strong>diperbarui</strong>, sisanya <strong>ditambahkan</strong>.</li>
                    </ol>
                    <label for="importDosenFile" class="form-label">File Excel (.xlsx, .xls, .csv, .ods; maks. 10 MB)</label>
                    <input type="file" class="form-control mb-3" id="importDosenFile" name="file" accept=".xlsx,.xls,.csv,.ods,.htm,.html" required>
                    @error('file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    <label for="importProdiDefault" class="form-label">Homebase default <span class="text-muted small">(opsional)</span></label>
                    <select name="prodi_default" id="importProdiDefault" class="form-select">
                        <option value="">-- Ikuti kolom Homebase di file --</option>
                        @foreach($prodis as $p)
                        <option value="{{ $p->id }}">{{ $p->jenjang }} {{ $p->nama }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Dipakai untuk baris yang kolom Homebase-nya kosong / tidak ada di file (misal ekspor SIAKAD per prodi).</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-upload me-1"></i>Import</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endsection
