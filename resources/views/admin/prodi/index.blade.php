@extends('layouts.admin')

@section('title', 'Program Studi')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Program Studi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Program Studi</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.prodi.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Prodi
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('admin.prodi.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" style="max-width: 300px;" placeholder="Cari nama atau kode prodi..." value="{{ request('search') }}">
                <select name="jenjang" class="form-select" style="max-width: 150px;">
                    <option value="">Semua Jenjang</option>
                    @foreach(['D3', 'D4', 'S1', 'S2', 'S3'] as $j)
                    <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
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
                            <th>Kode</th>
                            <th>Nama Program Studi</th>
                            <th>Jenjang</th>
                            <th>Fakultas</th>
                            <th>Kaprodi</th>
                            <th>Akreditasi</th>
                            <th>Status</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prodi as $index => $item)
                        <tr>
                            <td>{{ $prodi->firstItem() + $index }}</td>
                            <td><code>{{ $item->kode }}</code></td>
                            <td>
                                <a href="{{ route('admin.prodi.show', $item) }}" class="text-decoration-none fw-semibold">
                                    {{ $item->nama }}
                                </a>
                            </td>
                            <td><span class="badge bg-info">{{ $item->jenjang }}</span></td>
                            <td>{{ $item->fakultas ?? '-' }}</td>
                            <td>{{ $item->kaprodi->name ?? '-' }}</td>
                            <td>
                                @if($item->latestAkreditasi)
                                <span class="badge bg-{{ $item->latestAkreditasi->status_color }}">
                                    {{ $item->latestAkreditasi->peringkat }}
                                </span>
                                @else
                                <span class="badge bg-secondary">Belum</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_active)
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.prodi.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.prodi.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus prodi ini?')">
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
                                Belum ada data program studi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($prodi->hasPages())
        <div class="card-footer">
            {{ $prodi->links() }}
        </div>
        @endif
    </div>
@endsection
