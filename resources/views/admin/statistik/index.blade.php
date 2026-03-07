@extends('layouts.admin')

@section('title', 'Data Statistik')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Data Statistik</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Statistik</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.statistik.chart') }}" class="btn btn-outline-primary">
                <i class="bi bi-graph-up me-1"></i>Lihat Grafik
            </a>
            <a href="{{ route('admin.statistik.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Data
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('admin.statistik.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <select name="kategori" class="form-select" style="max-width: 180px;">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriOptions as $key => $label)
                    <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="tahun" class="form-select" style="max-width: 120px;">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-filter"></i></button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Kategori</th>
                            <th>Tahun</th>
                            <th class="text-center">Usulan</th>
                            <th class="text-center">Didanai</th>
                            <th class="text-end">Dana Usulan</th>
                            <th class="text-end">Dana Disetujui</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statistiks as $index => $item)
                        <tr>
                            <td>{{ $statistiks->firstItem() + $index }}</td>
                            <td><span class="badge bg-primary">{{ $item->kategori_label }}</span></td>
                            <td><strong>{{ $item->tahun }}</strong></td>
                            <td class="text-center">
                                <span class="badge bg-info fs-6">{{ number_format($item->usulan) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">{{ number_format($item->didanai) }}</span>
                            </td>
                            <td class="text-end">Rp {{ number_format($item->dana_usulan, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->dana_disetujui, 0, ',', '.') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.statistik.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.statistik.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
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
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-bar-chart fs-1 text-muted d-block mb-2"></i>
                                Belum ada data statistik
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($statistiks->hasPages())
        <div class="card-footer">
            {{ $statistiks->links() }}
        </div>
        @endif
    </div>
@endsection
