@extends('layouts.admin')

@section('title', 'Jadwal AMI')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Jadwal Audit Mutu Internal</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">AMI</li>
                    <li class="breadcrumb-item active">Jadwal</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.jadwal.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Jadwal
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('admin.ami.jadwal.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <select name="periode_id" class="form-select" style="max-width: 200px;">
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
                <select name="status" class="form-select" style="max-width: 150px;">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
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
                            <th>Prodi</th>
                            <th>Periode</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Tempat</th>
                            <th>Auditor</th>
                            <th>Status</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwals as $index => $item)
                        <tr>
                            <td>{{ $jadwals->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('admin.ami.jadwal.show', $item) }}" class="text-decoration-none">
                                    <span class="badge bg-info me-1">{{ $item->prodi->jenjang }}</span>
                                    <strong>{{ $item->prodi->nama }}</strong>
                                </a>
                            </td>
                            <td>{{ $item->periodeAmi->nama ?? '-' }}</td>
                            <td>{{ $item->tanggal_audit->format('d M Y') }}</td>
                            <td>{{ $item->time_range }}</td>
                            <td>{{ $item->tempat ?? '-' }}</td>
                            <td>
                                @foreach($item->penugasan->take(2) as $p)
                                <span class="badge bg-{{ $p->peran == 'ketua' ? 'primary' : 'secondary' }}" title="{{ $p->auditor->user->name ?? '-' }}">
                                    {{ Str::limit($p->auditor->user->name ?? '-', 10) }}
                                </span>
                                @endforeach
                                @if($item->penugasan->count() > 2)
                                <span class="badge bg-light text-dark">+{{ $item->penugasan->count() - 2 }}</span>
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $item->status_color }}">{{ ucfirst($item->status) }}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.ami.jadwal.show', $item) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.ami.jadwal.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.ami.jadwal.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
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
                                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                                Belum ada jadwal AMI
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($jadwals->hasPages())
        <div class="card-footer">
            {{ $jadwals->links() }}
        </div>
        @endif
    </div>
@endsection
