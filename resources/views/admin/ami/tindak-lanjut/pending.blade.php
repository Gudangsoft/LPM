@extends('layouts.admin')

@section('title', 'Pending Review Tindak Lanjut')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Pending Review</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ami.tindak-lanjut.index') }}">Tindak Lanjut</a></li>
                    <li class="breadcrumb-item active">Pending Review</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.tindak-lanjut.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-1"></i>
        Berikut adalah daftar tindak lanjut yang menunggu persetujuan. Silakan review dan berikan keputusan.
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Temuan</th>
                            <th>Tindakan</th>
                            <th>Penanggung Jawab</th>
                            <th>Diajukan</th>
                            <th style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tindakLanjuts as $index => $item)
                        <tr>
                            <td>{{ $tindakLanjuts->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-{{ $item->temuanAmi->kategori_color ?? 'secondary' }} me-1">{{ ucfirst($item->temuanAmi->kategori ?? '-') }}</span>
                                {{ Str::limit($item->temuanAmi->standar ?? '-', 25) }}
                                <br>
                                <small class="text-muted">{{ $item->temuanAmi->jadwalAmi->prodi->nama ?? '-' }}</small>
                            </td>
                            <td>
                                <strong>{{ Str::limit($item->tindakan, 40) }}</strong><br>
                                <small class="text-muted">{{ Str::limit($item->deskripsi, 60) }}</small>
                            </td>
                            <td>{{ $item->penanggungJawab->name ?? '-' }}</td>
                            <td>{{ $item->updated_at->format('d M Y') }}</td>
                            <td>
                                <form action="{{ route('admin.ami.tindak-lanjut.review', $item) }}" method="POST" class="d-inline-flex gap-1">
                                    @csrf
                                    <a href="{{ route('admin.ami.tindak-lanjut.show', $item) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="submit" name="status" value="disetujui" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                    <button type="submit" name="status" value="ditolak" class="btn btn-sm btn-danger">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                                <h5 class="text-muted">Tidak ada tindak lanjut yang pending</h5>
                                <p class="text-muted">Semua tindak lanjut sudah direview</p>
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
