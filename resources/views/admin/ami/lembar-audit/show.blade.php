@extends('layouts.admin')

@section('title', 'Lembar Kerja Audit — ' . optional($jadwal->prodi)->nama)

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Lembar Kerja Audit</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ami.lembar-audit.index') }}">Lembar Kerja Audit</a></li>
                    <li class="breadcrumb-item active">{{ optional($jadwal->prodi)->nama }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.temuan.create', ['jadwal_id' => $jadwal->id]) }}" class="btn btn-outline-danger btn-sm align-self-center">
            <i class="bi bi-exclamation-diamond me-1"></i>Buat Temuan
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body row g-2 small">
            <div class="col-md-3"><strong>Unit:</strong> {{ optional($jadwal->prodi)->nama }}</div>
            <div class="col-md-3"><strong>Periode:</strong> {{ optional($jadwal->periodeAmi)->nama }}</div>
            <div class="col-md-3"><strong>Tanggal:</strong> {{ optional($jadwal->tanggal_audit)->format('d M Y') }}</div>
            <div class="col-md-3"><strong>Evaluasi Diri:</strong>
                <span class="badge bg-{{ ($jadwal->evaluasiDiri?->status ?? '') === 'submitted' ? 'success' : 'secondary' }}">
                    {{ ($jadwal->evaluasiDiri?->status ?? '') === 'submitted' ? 'Dikirim' : 'Belum dikirim' }}
                </span>
            </div>
        </div>
    </div>

    @if(!$editable)
        <div class="alert alert-info"><i class="bi bi-lock me-1"></i>Mode lihat saja — Anda tidak ditugaskan pada jadwal ini.</div>
    @endif

    @if($standar->isEmpty())
        <div class="alert alert-warning">Belum ada butir instrumen aktif.</div>
    @else
    <form id="laSave" action="{{ route('admin.ami.lembar-audit.save', $jadwal) }}" method="POST">@csrf @method('PUT')</form>

    @foreach($standar as $s)
    <div class="card mb-3">
        <div class="card-header fw-semibold">{{ $s->kode ? $s->kode.' — ' : '' }}{{ $s->nama }}</div>
        <div class="card-body">
            @foreach($s->butir as $b)
            @php $row = $rows[$b->id] ?? null; @endphp
            <div class="border rounded p-3 mb-3">
                <div class="fw-semibold mb-1">{{ $b->kode ? $b->kode.'. ' : '' }}{{ $b->pertanyaan }}</div>
                @if($b->indikator)<div class="text-muted small mb-2">Indikator: {{ $b->indikator }}</div>@endif

                <div class="bg-light rounded p-2 mb-2 small">
                    <div><strong>Evaluasi Diri Auditee</strong></div>
                    <div>Nilai mandiri: <strong>{{ $row->nilai_mandiri ?? '—' }}</strong></div>
                    <div>{{ $row->deskripsi_capaian ?: '(tidak diisi)' }}</div>
                </div>

                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Nilai Auditor</label>
                        <input form="laSave" type="number" step="0.01" min="0" name="butir[{{ $row->id }}][nilai_auditor]"
                               value="{{ old("butir.{$row->id}.nilai_auditor", $row->nilai_auditor) }}"
                               class="form-control form-control-sm" {{ $editable ? '' : 'disabled' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Status Verifikasi</label>
                        <select form="laSave" name="butir[{{ $row->id }}][status_verifikasi]" class="form-select form-select-sm" {{ $editable ? '' : 'disabled' }}>
                            @foreach($statusOptions as $val => $lbl)
                            <option value="{{ $val }}" {{ old("butir.{$row->id}.status_verifikasi", $row->status_verifikasi) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small mb-1">Catatan Auditor</label>
                        <textarea form="laSave" name="butir[{{ $row->id }}][catatan_auditor]" rows="2"
                                  class="form-control form-control-sm" {{ $editable ? '' : 'disabled' }}>{{ old("butir.{$row->id}.catatan_auditor", $row->catatan_auditor) }}</textarea>
                    </div>
                </div>

                <div class="mt-2">
                    <div class="small fw-semibold mb-1">Bukti &amp; Validasi</div>
                    @forelse($row->bukti as $bk)
                    <div class="border-top py-2 small">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border">v{{ $bk->versi }}</span>
                            @if($bk->url)<a href="{{ $bk->url }}" target="_blank" rel="noopener">{{ $bk->judul }}</a>@else{{ $bk->judul }}@endif
                            <span class="badge bg-{{ $bk->status_color }}">{{ $bk->status_validasi }}</span>
                        </div>
                        @if($editable)
                        <form action="{{ route('admin.ami.lembar-audit.bukti.validasi', $bk) }}" method="POST" class="row g-1 mt-1">
                            @csrf
                            <div class="col-md-3">
                                <select name="status_validasi" class="form-select form-select-sm">
                                    <option value="belum" {{ $bk->status_validasi === 'belum' ? 'selected' : '' }}>Belum</option>
                                    <option value="valid" {{ $bk->status_validasi === 'valid' ? 'selected' : '' }}>Valid</option>
                                    <option value="tidak_valid" {{ $bk->status_validasi === 'tidak_valid' ? 'selected' : '' }}>Tidak Valid</option>
                                </select>
                            </div>
                            <div class="col-md-7"><input name="catatan_validasi" value="{{ $bk->catatan_validasi }}" class="form-control form-control-sm" placeholder="Catatan validasi"></div>
                            <div class="col-md-2"><button class="btn btn-sm btn-outline-primary w-100">Simpan</button></div>
                        </form>
                        @endif
                    </div>
                    @empty
                    <div class="text-muted small">Tidak ada bukti dari auditee.</div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($editable)
    <div class="mb-4">
        <button type="submit" form="laSave" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Penilaian</button>
    </div>
    @endif
    @endif
@endsection
