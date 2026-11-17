@extends('layouts.admin')

@section('title', 'Data Akreditasi')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Data Akreditasi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Akreditasi</li>
                </ol>
            </nav>
        </div>
        @php $canCreateAkreditasi = auth()->user()->hasPermission('akreditasi.create'); @endphp
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.akreditasi.dashboard') }}" class="btn btn-outline-primary">
                <i class="bi bi-graph-up me-1"></i>Dashboard
            </a>
            <a href="{{ route('admin.akreditasi.export', request()->only('status', 'lembaga')) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            @if($canCreateAkreditasi)
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importAkreditasiModal">
                <i class="bi bi-upload me-1"></i>Import Excel
            </button>
            <a href="{{ route('admin.akreditasi.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Akreditasi
            </a>
            @endif
        </div>
    </div>

    @if(session('import_errors'))
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Kesalahan pada file import:</div>
        <ul class="mb-0 small" style="max-height: 220px; overflow-y: auto;">
            @foreach(session('import_errors') as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($canCreateAkreditasi)
    <div class="modal fade" id="importAkreditasiModal" tabindex="-1" aria-labelledby="importAkreditasiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.akreditasi.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importAkreditasiModalLabel">Import Data Akreditasi dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <ol class="small ps-3">
                        <li>Unduh <a href="{{ route('admin.akreditasi.template') }}">template import</a> (atau gunakan hasil Export Excel). Daftar Kode Prodi ada di sheet "Petunjuk".</li>
                        <li>Wajib: Kode Prodi, Lembaga, Peringkat, Nomor SK, Tanggal SK, Tanggal Kadaluarsa.</li>
                        <li>Prodi + Nomor SK yang sudah ada akan <strong>diperbarui</strong>, sisanya <strong>ditambahkan</strong>.</li>
                        <li>File SK (PDF/gambar) diunggah lewat tombol Edit; di Excel cukup isi <strong>Link SK</strong> bila ada.</li>
                    </ol>
                    <label for="importAkreditasiFile" class="form-label">File Excel (.xlsx, .xls, .csv; maks. 10 MB)</label>
                    <input type="file" class="form-control" id="importAkreditasiFile" name="file" accept=".xlsx,.xls,.csv,.ods" required>
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

    <div class="card">
        <div class="card-header">
            <form action="{{ route('admin.akreditasi.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <input type="text" name="search" class="form-control" style="max-width: 250px;" placeholder="Cari prodi..." value="{{ request('search') }}">
                <select name="status" class="form-select w-auto" style="min-width: 180px;">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
                <select name="lembaga" class="form-select w-auto" style="min-width: 150px;">
                    <option value="">Semua Lembaga</option>
                    @foreach($lembagaOptions as $l)
                    <option value="{{ $l }}" {{ request('lembaga') == $l ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Program Studi</th>
                            <th>Lembaga</th>
                            <th>Peringkat</th>
                            <th>No. SK</th>
                            <th>Tanggal SK</th>
                            <th>Kadaluarsa</th>
                            <th>Status</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($akreditasi as $index => $item)
                        <tr>
                            <td>{{ $akreditasi->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-info me-1">{{ $item->prodi->jenjang }}</span>
                                {{ $item->prodi->nama }}
                            </td>
                            <td>{{ $item->lembaga }}</td>
                            <td><span class="badge bg-primary fs-6">{{ $item->peringkat }}</span></td>
                            <td>
                                {{ $item->nomor_sk }}
                                @if($item->sk_url)
                                <a href="{{ $item->sk_url }}" target="_blank" rel="noopener" class="ms-1" title="Lihat SK">
                                    <i class="bi {{ $item->sk_icon }} {{ $item->sk_type === 'link' ? 'text-primary' : 'text-danger' }}"></i>
                                </a>
                                @endif
                            </td>
                            <td>{{ $item->tanggal_sk->format('d M Y') }}</td>
                            <td>
                                {{ $item->tanggal_kadaluarsa->format('d M Y') }}
                                @if($item->isExpired())
                                <span class="badge bg-danger ms-1">Expired</span>
                                @elseif($item->isExpiringSoon())
                                <span class="badge bg-warning ms-1">{{ $item->days_until_expiration }} hari</span>
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $item->status_color }}">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.akreditasi.show', $item) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.akreditasi.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.akreditasi.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                Belum ada data akreditasi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($akreditasi->hasPages())
        <div class="card-footer">
            {{ $akreditasi->links() }}
        </div>
        @endif
    </div>
@endsection
