@extends('layouts.admin')

@section('title', 'Penugasan Saya')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Penugasan Saya</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Penugasan Saya</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Penugasan Audit</h5>
        </div>

        @if(is_null($penugasans))
        <div class="card-body">
            <div class="text-center text-muted py-4">
                <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                Akun Anda belum terdaftar sebagai auditor.
            </div>
        </div>
        @else
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Program Studi</th>
                            <th>Periode</th>
                            <th>Tanggal Audit</th>
                            <th>Peran</th>
                            <th>Status</th>
                            <th style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penugasans as $p)
                        <tr>
                            <td>{{ $p->jadwalAmi->prodi->nama ?? '-' }}</td>
                            <td>{{ $p->jadwalAmi->periodeAmi->nama ?? '-' }}</td>
                            <td>{{ $p->jadwalAmi->tanggal_audit?->format('d M Y') ?? '-' }}</td>
                            <td><span class="badge bg-{{ $p->peran_color }}">{{ ucfirst($p->peran) }}</span></td>
                            <td><span class="badge bg-{{ $p->status_color }}">{{ ucfirst($p->status) }}</span></td>
                            <td>
                                @if($p->status === 'ditugaskan')
                                <div class="d-flex gap-1">
                                    <form action="{{ route('admin.ami.penugasan.accept', $p) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-lg me-1"></i>Terima
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="collapse" data-bs-target="#reject-{{ $p->id }}">
                                        <i class="bi bi-x-lg me-1"></i>Tolak
                                    </button>
                                </div>
                                <div class="collapse mt-2" id="reject-{{ $p->id }}">
                                    <form action="{{ route('admin.ami.penugasan.reject', $p) }}" method="POST" class="d-flex gap-1">
                                        @csrf
                                        <input type="text" name="catatan" class="form-control form-control-sm" placeholder="Alasan penolakan (opsional)">
                                        <button type="submit" class="btn btn-sm btn-danger">Kirim</button>
                                    </form>
                                </div>
                                @else
                                <a href="{{ route('admin.ami.jadwal.show', $p->jadwal_ami_id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>Lihat Jadwal
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada penugasan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($penugasans->hasPages())
        <div class="card-footer">
            {{ $penugasans->links() }}
        </div>
        @endif
        @endif
    </div>
@endsection
