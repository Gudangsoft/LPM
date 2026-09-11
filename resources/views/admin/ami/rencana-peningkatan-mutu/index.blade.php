@extends('layouts.admin')

@section('title', 'Rencana Peningkatan Mutu')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Rencana Peningkatan Mutu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">SPMI</li>
                <li class="breadcrumb-item active">Rencana Peningkatan Mutu</li>
            </ol>
        </nav>
    </div>

    <p class="text-muted mb-3">
        Rekap seluruh keputusan dan rekomendasi peningkatan mutu dari setiap Rapat Tinjauan
        Manajemen (RTM) dalam satu daftar, lengkap dengan penanggung jawab (PIC), target waktu,
        dan status tindak lanjutnya. Untuk menambah/menghapus keputusan baru, buka RTM terkait
        di menu <a href="{{ route('admin.ami.rtm.index') }}">Rapat Tinjauan Manajemen</a>.
    </p>

    <div class="row g-3 mb-3">
        @foreach([['Total Rencana', $stats['total'], 'primary'], ['Selesai', $stats['selesai'], 'success'], ['Proses', $stats['proses'], 'info'], ['Belum Dimulai', $stats['belum'], 'secondary']] as [$l, $v, $c])
        <div class="col-6 col-md-3">
            <div class="card border-{{ $c }}"><div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-{{ $c }}">{{ $v }}</div><small class="text-muted">{{ $l }}</small>
            </div></div>
        </div>
        @endforeach
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Periode AMI</label>
                    <select name="periode_ami_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($periodeOptions as $p)
                        <option value="{{ $p->id }}" {{ request('periode_ami_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->tahun_akademik }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum</option>
                        <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">PIC</label>
                    <input type="text" name="pic" value="{{ request('pic') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Cari Keputusan</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-funnel"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Keputusan / Rekomendasi</th>
                            <th width="150">RTM</th>
                            <th width="140">PIC</th>
                            <th width="120">Target</th>
                            <th width="150">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rencana as $r)
                        <tr>
                            <td>
                                {{ $r->keputusan }}
                                @if($r->rekomendasi)<div class="small text-muted">{{ Str::limit($r->rekomendasi, 100) }}</div>@endif
                            </td>
                            <td>
                                <a href="{{ route('admin.ami.rtm.show', $r->rtm) }}"><small>{{ $r->rtm->judul }}</small></a>
                                <div class="small text-muted">{{ optional($r->rtm->periodeAmi)->tahun_akademik }}</div>
                            </td>
                            <td><small>{{ $r->pic ?? '-' }}</small></td>
                            <td><small>{{ optional($r->target_tanggal)->format('d M Y') ?? '-' }}</small></td>
                            <td>
                                <form action="{{ route('admin.ami.rtm.keputusan.update', $r) }}" method="POST" class="d-flex gap-1">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="keputusan" value="{{ $r->keputusan }}">
                                    <input type="hidden" name="rekomendasi" value="{{ $r->rekomendasi }}">
                                    <input type="hidden" name="pic" value="{{ $r->pic }}">
                                    <input type="hidden" name="target_tanggal" value="{{ optional($r->target_tanggal)->format('Y-m-d') }}">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="belum" {{ $r->status === 'belum' ? 'selected' : '' }}>Belum</option>
                                        <option value="proses" {{ $r->status === 'proses' ? 'selected' : '' }}>Proses</option>
                                        <option value="selesai" {{ $r->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada keputusan RTM.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($rencana->hasPages())<div class="card-footer">{{ $rencana->links() }}</div>@endif
    </div>
@endsection
