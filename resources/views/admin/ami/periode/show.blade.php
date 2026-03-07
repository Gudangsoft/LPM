@extends('layouts.admin')

@section('title', 'Detail Periode AMI')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ $periode->nama }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ami.periode.index') }}">Periode AMI</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            @if($periode->status == 'draft')
            <form action="{{ route('admin.ami.periode.activate', $periode) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-play-fill me-1"></i>Aktifkan
                </button>
            </form>
            @elseif($periode->status == 'aktif')
            <form action="{{ route('admin.ami.periode.complete', $periode) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Selesaikan
                </button>
            </form>
            @endif
            <a href="{{ route('admin.ami.periode.edit', $periode) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5>Status</h5>
                    <h3><span class="badge bg-light text-{{ $periode->status_color }}">{{ ucfirst($periode->status) }}</span></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="text-muted">Total Jadwal</h5>
                    <h3>{{ $periode->total_jadwal }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="text-muted">Selesai</h5>
                    <h3>{{ $periode->completed_jadwal }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="text-muted">Progress</h5>
                    <h3>{{ $periode->progress }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Periode</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Tahun Akademik</td>
                            <td>{{ $periode->tahun_akademik }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Semester</td>
                            <td>{{ $periode->semester }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Mulai</td>
                            <td>{{ $periode->tanggal_mulai->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Selesai</td>
                            <td>{{ $periode->tanggal_selesai->format('d M Y') }}</td>
                        </tr>
                    </table>
                    @if($periode->deskripsi)
                    <hr>
                    <p class="mb-0"><small class="text-muted">{{ $periode->deskripsi }}</small></p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Jadwal Audit</h5>
                    <a href="{{ route('admin.ami.jadwal.create') }}?periode_id={{ $periode->id }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Tambah Jadwal
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Prodi</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Auditor</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periode->jadwalAmi as $jadwal)
                                <tr>
                                    <td>
                                        <span class="badge bg-info me-1">{{ $jadwal->prodi->jenjang }}</span>
                                        {{ $jadwal->prodi->nama }}
                                    </td>
                                    <td>{{ $jadwal->tanggal_audit->format('d M Y') }}</td>
                                    <td>{{ $jadwal->time_range }}</td>
                                    <td>
                                        @foreach($jadwal->penugasan as $p)
                                        <span class="badge bg-{{ $p->peran == 'ketua' ? 'primary' : 'secondary' }}">
                                            {{ $p->auditor->user->name ?? '-' }}
                                        </span>
                                        @endforeach
                                    </td>
                                    <td><span class="badge bg-{{ $jadwal->status_color }}">{{ ucfirst($jadwal->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.ami.jadwal.show', $jadwal) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada jadwal audit
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
