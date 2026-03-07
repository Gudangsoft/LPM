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
        <div>
            <a href="{{ route('admin.akreditasi.dashboard') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-graph-up me-1"></i>Dashboard
            </a>
            <a href="{{ route('admin.akreditasi.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Akreditasi
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('admin.akreditasi.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <input type="text" name="search" class="form-control" style="max-width: 250px;" placeholder="Cari prodi..." value="{{ request('search') }}">
                <select name="status" class="form-select" style="max-width: 180px;">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
                <select name="lembaga" class="form-select" style="max-width: 150px;">
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
                                @if($item->file_sk)
                                <a href="{{ Storage::url($item->file_sk) }}" target="_blank" class="ms-1">
                                    <i class="bi bi-file-pdf text-danger"></i>
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
