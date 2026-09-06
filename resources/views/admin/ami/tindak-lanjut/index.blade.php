@extends('layouts.admin')

@section('title', 'Tindak Lanjut AMI')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Tindak Lanjut AMI</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">AMI</li>
                    <li class="breadcrumb-item active">Tindak Lanjut</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.ami.tindak-lanjut.pending') }}" class="btn btn-warning me-2">
                <i class="bi bi-hourglass-split me-1"></i>Pending Review ({{ $pendingCount ?? 0 }})
            </a>
            <a href="{{ route('admin.ami.tindak-lanjut.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Tindak Lanjut
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $stats['submitted'] ?? 0 }}</h3>
                    <small class="text-muted">Submitted</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $stats['reviewed'] ?? 0 }}</h3>
                    <small class="text-muted">Reviewed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $stats['approved'] ?? 0 }}</h3>
                    <small class="text-muted">Disetujui</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h3 class="text-danger">{{ $stats['rejected'] ?? 0 }}</h3>
                    <small class="text-muted">Ditolak</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('admin.ami.tindak-lanjut.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <select name="prodi_id" class="form-select" style="max-width: 200px;">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $p)
                    <option value="{{ $p->id }}" {{ request('prodi_id') == $p->id ? 'selected' : '' }}>{{ $p->jenjang }} - {{ $p->nama }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-select" style="max-width: 150px;">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" class="form-control" style="max-width: 200px;" placeholder="Cari deskripsi..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-filter"></i></button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Temuan</th>
                            <th>Deskripsi</th>
                            <th>Diajukan Oleh</th>
                            <th>Tanggal Submit</th>
                            <th>Status</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tindakLanjuts as $index => $item)
                        <tr>
                            <td>{{ $tindakLanjuts->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('admin.ami.temuan.show', $item->temuan_ami_id) }}" class="text-decoration-none">
                                    <span class="badge bg-{{ $item->temuanAmi->kategori_color ?? 'secondary' }} me-1">{{ ucfirst($item->temuanAmi->kategori ?? '-') }}</span>
                                    {{ Str::limit($item->temuanAmi->standar ?? '-', 20) }}
                                </a>
                                <div class="small text-muted">{{ $item->temuanAmi->jadwalAmi->prodi->nama ?? '-' }}</div>
                            </td>
                            <td>{{ Str::limit($item->deskripsi, 40) }}</td>
                            <td>{{ $item->user->name ?? '-' }}</td>
                            <td>{{ $item->tanggal_submit?->format('d M Y') ?? '-' }}</td>
                            <td><span class="badge bg-{{ $item->status_color }}">{{ ucfirst($item->status) }}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.ami.tindak-lanjut.show', $item) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(in_array($item->status, ['submitted', 'rejected']))
                                    <a href="{{ route('admin.ami.tindak-lanjut.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.ami.tindak-lanjut.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-clipboard-x fs-1 text-muted d-block mb-2"></i>
                                Belum ada tindak lanjut
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tindakLanjuts->hasPages())
        <div class="card-footer">
            {{ $tindakLanjuts->links() }}
        </div>
        @endif
    </div>
@endsection
