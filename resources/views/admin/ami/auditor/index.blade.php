@extends('layouts.admin')

@section('title', 'Auditor')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Auditor Internal</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">AMI</li>
                    <li class="breadcrumb-item active">Auditor</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.auditor.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Auditor
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('admin.ami.auditor.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" style="max-width: 300px;" placeholder="Cari nama auditor..." value="{{ request('search') }}">
                <select name="status" class="form-select" style="max-width: 150px;">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>No. Sertifikat</th>
                            <th>Bidang Keahlian</th>
                            <th>Masa Berlaku</th>
                            <th>Status</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditors as $index => $item)
                        <tr>
                            <td>{{ $auditors->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('admin.ami.auditor.show', $item) }}" class="text-decoration-none fw-semibold">
                                    {{ $item->user->name ?? '-' }}
                                </a>
                                <br><small class="text-muted">{{ $item->user->email ?? '' }}</small>
                            </td>
                            <td>{{ $item->nip ?? '-' }}</td>
                            <td>{{ $item->no_sertifikat ?? '-' }}</td>
                            <td>{{ $item->bidang_keahlian ?? '-' }}</td>
                            <td>
                                @if($item->masa_berlaku)
                                {{ $item->masa_berlaku->format('d M Y') }}
                                @if(!$item->isCertificateValid())
                                <span class="badge bg-danger">Expired</span>
                                @endif
                                @else
                                -
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $item->status_color }}">{{ ucfirst($item->status) }}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.ami.auditor.show', $item) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.ami.auditor.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.ami.auditor.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus auditor ini?')">
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
                                <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                                Belum ada data auditor
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($auditors->hasPages())
        <div class="card-footer">
            {{ $auditors->links() }}
        </div>
        @endif
    </div>
@endsection
