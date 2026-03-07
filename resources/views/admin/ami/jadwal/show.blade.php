@extends('layouts.admin')

@section('title', 'Detail Jadwal AMI')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Jadwal AMI: {{ $jadwal->prodi->nama }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ami.jadwal.index') }}">Jadwal AMI</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.ami.jadwal.edit', $jadwal) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Jadwal</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Prodi</td>
                            <td>
                                <span class="badge bg-info">{{ $jadwal->prodi->jenjang }}</span>
                                {{ $jadwal->prodi->nama }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kaprodi</td>
                            <td>{{ $jadwal->prodi->kaprodi->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Periode</td>
                            <td>{{ $jadwal->periodeAmi->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal</td>
                            <td>{{ $jadwal->tanggal_audit->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu</td>
                            <td>{{ $jadwal->time_range }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tempat</td>
                            <td>{{ $jadwal->tempat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge bg-{{ $jadwal->status_color }}">{{ ucfirst($jadwal->status) }}</span></td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <form action="{{ route('admin.ami.jadwal.update-status', $jadwal) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <select name="status" class="form-select form-select-sm">
                            <option value="terjadwal" {{ $jadwal->status == 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                            <option value="berlangsung" {{ $jadwal->status == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                            <option value="selesai" {{ $jadwal->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditunda" {{ $jadwal->status == 'ditunda' ? 'selected' : '' }}>Ditunda</option>
                            <option value="batal" {{ $jadwal->status == 'batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </form>
                </div>
            </div>

            <!-- Tim Auditor -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tim Auditor</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($jadwal->penugasan as $p)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-{{ $p->peran_color }} me-2">{{ ucfirst($p->peran) }}</span>
                                {{ $p->auditor->user->name ?? '-' }}
                            </div>
                            <form action="{{ route('admin.ami.jadwal.remove-auditor', [$jadwal, $p]) }}" method="POST" onsubmit="return confirm('Hapus auditor ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-x"></i>
                                </button>
                            </form>
                        </li>
                        @empty
                        <li class="list-group-item text-muted text-center">Belum ada auditor ditugaskan</li>
                        @endforelse
                    </ul>
                </div>
                @if($availableAuditors->count() > 0)
                <div class="card-footer">
                    <form action="{{ route('admin.ami.jadwal.add-auditor', $jadwal) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <select name="auditor_id" class="form-select form-select-sm" required>
                            <option value="">Pilih Auditor</option>
                            @foreach($availableAuditors as $a)
                            <option value="{{ $a->id }}">{{ $a->user->name }}</option>
                            @endforeach
                        </select>
                        <select name="peran" class="form-select form-select-sm" style="max-width: 100px;" required>
                            <option value="anggota">Anggota</option>
                            <option value="ketua">Ketua</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <div class="col-md-8">
            <!-- Daftar Temuan -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Temuan Audit ({{ $jadwal->temuan->count() }})</h5>
                    <a href="{{ route('admin.ami.temuan.create') }}?jadwal_id={{ $jadwal->id }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus"></i> Tambah Temuan
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Standar</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Tindak Lanjut</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwal->temuan as $temuan)
                                <tr>
                                    <td>{{ Str::limit($temuan->standar, 20) }}</td>
                                    <td><span class="badge bg-{{ $temuan->kategori_color }}">{{ ucfirst($temuan->kategori) }}</span></td>
                                    <td>{{ Str::limit($temuan->deskripsi, 50) }}</td>
                                    <td><span class="badge bg-{{ $temuan->status_color }}">{{ ucfirst(str_replace('_', ' ', $temuan->status)) }}</span></td>
                                    <td>{{ $temuan->tindakLanjut->count() }}</td>
                                    <td>
                                        <a href="{{ route('admin.ami.temuan.show', $temuan) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada temuan
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
