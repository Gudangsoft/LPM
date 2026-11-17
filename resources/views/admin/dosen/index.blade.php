@extends('layouts.admin')

@section('title', 'Data Dosen')

@section('content')
    @php $canManage = auth()->user()->hasPermission('dosen.manage'); @endphp

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
        </div>
    </div>

    <p class="text-muted mb-3">
        Data induk dosen per program studi. Data ini dapat diambil langsung ke DKPS Tabel 6
        dan muncul sebagai pilihan Dosen Pengampu di Evaluasi Pembelajaran dan Review RPS.
    </p>

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
                            <th>Kode / NIDN</th>
                            <th>Nama & Kontak</th>
                            <th>Alamat & Email</th>
                            <th>Jns. Kel</th>
                            <th>Homebase</th>
                            <th>Lulusan</th>
                            <th>Golongan / Jabatan</th>
                            <th>Status</th>
                            @if($canManage)<th width="110">{{ __('admin.actions') }}</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dosen as $d)
                        <tr>
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
                            @if($canManage)
                            <td>
                                <a href="{{ route('admin.dosen.edit', $d) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.dosen.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="{{ $canManage ? 9 : 8 }}" class="text-center text-muted py-4">Belum ada data dosen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($dosen->hasPages())
        <div class="card-footer">{{ $dosen->links() }}</div>
        @endif
    </div>

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
                        <li>Wajib ada kolom <strong>Nama</strong> dan <strong>Homebase</strong> (kode atau nama prodi).</li>
                        <li>Dosen yang sudah terdaftar (dicocokkan lewat NIDN, lalu Kode Dosen) akan <strong>diperbarui</strong>, sisanya <strong>ditambahkan</strong>.</li>
                    </ol>
                    <label for="importDosenFile" class="form-label">File Excel (.xlsx, .xls, .csv; maks. 5 MB)</label>
                    <input type="file" class="form-control" id="importDosenFile" name="file" accept=".xlsx,.xls,.csv" required>
                    @error('file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
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
