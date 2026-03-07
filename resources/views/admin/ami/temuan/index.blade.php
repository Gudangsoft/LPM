@extends('layouts.admin')

@section('title', 'Temuan AMI')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Temuan Audit Mutu Internal</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">AMI</li>
                    <li class="breadcrumb-item active">Temuan</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.temuan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Temuan
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h3 class="text-danger">{{ $stats['mayor'] ?? 0 }}</h3>
                    <small class="text-muted">Mayor</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $stats['minor'] ?? 0 }}</h3>
                    <small class="text-muted">Minor</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $stats['observasi'] ?? 0 }}</h3>
                    <small class="text-muted">Observasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $stats['rekomendasi'] ?? 0 }}</h3>
                    <small class="text-muted">Rekomendasi</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('admin.ami.temuan.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <select name="periode_id" class="form-select" style="max-width: 180px;">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $p)
                    <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
                <select name="prodi_id" class="form-select" style="max-width: 200px;">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $p)
                    <option value="{{ $p->id }}" {{ request('prodi_id') == $p->id ? 'selected' : '' }}>{{ $p->jenjang }} - {{ $p->nama }}</option>
                    @endforeach
                </select>
                <select name="kategori" class="form-select" style="max-width: 130px;">
                    <option value="">Kategori</option>
                    <option value="mayor" {{ request('kategori') == 'mayor' ? 'selected' : '' }}>Mayor</option>
                    <option value="minor" {{ request('kategori') == 'minor' ? 'selected' : '' }}>Minor</option>
                    <option value="observasi" {{ request('kategori') == 'observasi' ? 'selected' : '' }}>Observasi</option>
                    <option value="rekomendasi" {{ request('kategori') == 'rekomendasi' ? 'selected' : '' }}>Rekomendasi</option>
                </select>
                <select name="status" class="form-select" style="max-width: 150px;">
                    <option value="">Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
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
                            <th>Prodi</th>
                            <th>Standar</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Tindak Lanjut</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($temuans as $index => $item)
                        <tr>
                            <td>{{ $temuans->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-info me-1">{{ $item->jadwalAmi->prodi->jenjang ?? '-' }}</span>
                                {{ $item->jadwalAmi->prodi->nama ?? '-' }}
                            </td>
                            <td>{{ Str::limit($item->standar, 25) }}</td>
                            <td><span class="badge bg-{{ $item->kategori_color }}">{{ ucfirst($item->kategori) }}</span></td>
                            <td>{{ Str::limit($item->deskripsi, 40) }}</td>
                            <td><span class="badge bg-{{ $item->status_color }}">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span></td>
                            <td>
                                @if($item->tindakLanjut->count() > 0)
                                <span class="badge bg-secondary">{{ $item->tindakLanjut->count() }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.ami.temuan.show', $item) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.ami.temuan.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.ami.temuan.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus temuan ini?')">
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
                                <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                                Belum ada temuan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($temuans->hasPages())
        <div class="card-footer">
            {{ $temuans->links() }}
        </div>
        @endif
    </div>
@endsection
