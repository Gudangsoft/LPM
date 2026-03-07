@extends('layouts.admin')

@section('title', 'Periode AMI')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Periode AMI</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">AMI</li>
                    <li class="breadcrumb-item active">Periode</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.periode.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Periode
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('admin.ami.periode.index') }}" method="GET" class="d-flex gap-2">
                <select name="status" class="form-select" style="max-width: 180px;">
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
                            <th>Nama Periode</th>
                            <th>Tahun Akademik</th>
                            <th>Semester</th>
                            <th>Tanggal</th>
                            <th>Jadwal AMI</th>
                            <th>Status</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodes as $index => $item)
                        <tr>
                            <td>{{ $periodes->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('admin.ami.periode.show', $item) }}" class="text-decoration-none fw-semibold">
                                    {{ $item->nama }}
                                </a>
                            </td>
                            <td>{{ $item->tahun_akademik }}</td>
                            <td>{{ $item->semester }}</td>
                            <td>
                                <small>
                                    {{ $item->tanggal_mulai->format('d M Y') }} - {{ $item->tanggal_selesai->format('d M Y') }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $item->jadwal_ami_count }} jadwal</span>
                            </td>
                            <td><span class="badge bg-{{ $item->status_color }}">{{ ucfirst($item->status) }}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.ami.periode.show', $item) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.ami.periode.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.ami.periode.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus periode ini?')">
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
                                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                                Belum ada periode AMI
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($periodes->hasPages())
        <div class="card-footer">
            {{ $periodes->links() }}
        </div>
        @endif
    </div>
@endsection
