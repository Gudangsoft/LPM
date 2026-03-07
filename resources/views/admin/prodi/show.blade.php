@extends('layouts.admin')

@section('title', 'Detail Program Studi')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ $prodi->nama }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.prodi.index') }}">Program Studi</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.prodi.edit', $prodi) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Prodi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Kode</td>
                            <td><code>{{ $prodi->kode }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenjang</td>
                            <td><span class="badge bg-info">{{ $prodi->jenjang }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fakultas</td>
                            <td>{{ $prodi->fakultas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kaprodi</td>
                            <td>{{ $prodi->kaprodi->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($prodi->is_active)
                                <span class="badge bg-success">Aktif</span>
                                @else
                                <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Riwayat Akreditasi</h5>
                    <a href="{{ route('admin.akreditasi.create') }}?prodi_id={{ $prodi->id }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Tambah
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Lembaga</th>
                                    <th>Peringkat</th>
                                    <th>No. SK</th>
                                    <th>Kadaluarsa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prodi->akreditasi as $akr)
                                <tr>
                                    <td>{{ $akr->lembaga }}</td>
                                    <td><span class="badge bg-primary">{{ $akr->peringkat }}</span></td>
                                    <td>{{ $akr->nomor_sk }}</td>
                                    <td>{{ $akr->tanggal_kadaluarsa->format('d M Y') }}</td>
                                    <td><span class="badge bg-{{ $akr->status_color }}">{{ ucfirst($akr->status) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">Belum ada data akreditasi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Riwayat Audit (AMI)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Periode</th>
                                    <th>Tanggal Audit</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prodi->jadwalAmi as $jadwal)
                                <tr>
                                    <td>{{ $jadwal->periodeAmi->nama ?? '-' }}</td>
                                    <td>{{ $jadwal->tanggal_audit->format('d M Y') }}</td>
                                    <td>{{ $jadwal->time_range }}</td>
                                    <td><span class="badge bg-{{ $jadwal->status_color }}">{{ ucfirst($jadwal->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.ami.jadwal.show', $jadwal) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat audit</td>
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
