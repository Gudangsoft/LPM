@extends('layouts.admin')

@section('title', 'Evaluasi Diri — ' . optional($jadwal->prodi)->nama)

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Evaluasi Diri</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ami.evaluasi-diri.index') }}">Evaluasi Diri</a></li>
                    <li class="breadcrumb-item active">{{ optional($jadwal->prodi)->nama }}</li>
                </ol>
            </nav>
        </div>
        <span class="badge bg-{{ $header->status_color }} align-self-center fs-6">
            {{ $header->isSubmitted() ? 'Sudah Dikirim' : 'Draft' }}
        </span>
    </div>

    <div class="card mb-3">
        <div class="card-body row g-2 small">
            <div class="col-md-4"><strong>Unit:</strong> {{ optional($jadwal->prodi)->nama }}</div>
            <div class="col-md-4"><strong>Periode:</strong> {{ optional($jadwal->periodeAmi)->nama }}</div>
            <div class="col-md-4"><strong>Tanggal Audit:</strong> {{ optional($jadwal->tanggal_audit)->format('d M Y') }}</div>
        </div>
    </div>

    @if(!$editable)
        <div class="alert alert-info"><i class="bi bi-lock me-1"></i>Mode lihat saja (sudah dikirim atau bukan unit Anda).</div>
    @endif

    @if($standar->isEmpty())
        <div class="alert alert-warning">Belum ada butir instrumen aktif. Isi <a href="{{ route('admin.ami.instrumen.index') }}">Instrumen Audit</a> terlebih dahulu.</div>
    @else
    {{-- The scoring form lives here (empty); inputs bind to it via form="edSave" so evidence forms are never nested. --}}
    <form id="edSave" action="{{ route('admin.ami.evaluasi-diri.save', $jadwal) }}" method="POST">@csrf @method('PUT')</form>

    @foreach($standar as $s)
    <div class="card mb-3">
        <div class="card-header fw-semibold">{{ $s->kode ? $s->kode.' — ' : '' }}{{ $s->nama }}</div>
        <div class="card-body">
            @foreach($s->butir as $b)
            @php $row = $rows[$b->id] ?? null; @endphp
            <div class="border rounded p-3 mb-3">
                <div class="fw-semibold mb-1">{{ $b->kode ? $b->kode.'. ' : '' }}{{ $b->pertanyaan }}</div>
                @if($b->indikator)<div class="text-muted small mb-2">Indikator: {{ $b->indikator }}</div>@endif
                <div class="small text-muted mb-2">
                    @if($b->target)Target: <strong>{{ $b->target }}</strong> &nbsp;|&nbsp; @endif
                    Bobot: {{ rtrim(rtrim(number_format($b->bobot, 2), '0'), '.') }}
                    @if($b->jenis_bukti) &nbsp;|&nbsp; Jenis bukti: {{ $b->jenis_bukti }} @endif
                </div>

                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Nilai Mandiri</label>
                        <input form="edSave" type="number" step="0.01" min="0" name="butir[{{ $row->id }}][nilai_mandiri]"
                               value="{{ old("butir.{$row->id}.nilai_mandiri", $row->nilai_mandiri) }}"
                               class="form-control form-control-sm" {{ $editable ? '' : 'disabled' }}>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label small mb-1">Deskripsi Capaian</label>
                        <textarea form="edSave" name="butir[{{ $row->id }}][deskripsi_capaian]" rows="2"
                                  class="form-control form-control-sm" {{ $editable ? '' : 'disabled' }}>{{ old("butir.{$row->id}.deskripsi_capaian", $row->deskripsi_capaian) }}</textarea>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="small fw-semibold mb-1">Bukti Pendukung</div>
                    @forelse($row->bukti as $bk)
                    <div class="d-flex align-items-center gap-2 border-top py-1 small">
                        <span class="badge bg-light text-dark border">v{{ $bk->versi }}</span>
                        @if($bk->url)<a href="{{ $bk->url }}" target="_blank" rel="noopener">{{ $bk->judul }}</a>@else{{ $bk->judul }}@endif
                        <span class="badge bg-{{ $bk->status_color }}">{{ $bk->status_validasi }}</span>
                        @if($bk->catatan_validasi)<span class="text-muted">— {{ $bk->catatan_validasi }}</span>@endif
                        @if($editable)
                        <form action="{{ route('admin.ami.evaluasi-diri.bukti.delete', $bk) }}" method="POST" class="ms-auto" onsubmit="return confirm('Hapus bukti ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger p-0">hapus</button>
                        </form>
                        @endif
                    </div>
                    @empty
                    <div class="text-muted small">Belum ada bukti.</div>
                    @endforelse

                    @if($editable)
                    <form action="{{ route('admin.ami.evaluasi-diri.bukti.add', $row) }}" method="POST" enctype="multipart/form-data" class="border rounded bg-light p-2 mt-2">
                        @csrf
                        <div class="row g-1">
                            <div class="col-md-4"><input name="judul" class="form-control form-control-sm" placeholder="Judul bukti" required></div>
                            <div class="col-md-4"><input name="tautan" class="form-control form-control-sm" placeholder="https://... (opsional)"></div>
                            <div class="col-md-3"><input type="file" name="file" class="form-control form-control-sm"></div>
                            <div class="col-md-1"><button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-plus-lg"></i></button></div>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div class="card mb-3">
        <div class="card-body">
            <label class="form-label">Catatan Umum</label>
            <textarea form="edSave" name="catatan" rows="2" class="form-control" {{ $editable ? '' : 'disabled' }}>{{ old('catatan', $header->catatan) }}</textarea>
        </div>
    </div>

    @if($editable)
    <div class="d-flex gap-2 mb-4">
        <button type="submit" form="edSave" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Draft</button>
        <form action="{{ route('admin.ami.evaluasi-diri.submit', $jadwal) }}" method="POST"
              onsubmit="return confirm('Kirim evaluasi diri? Setelah dikirim tidak dapat diubah.')">
            @csrf
            <button type="submit" class="btn btn-success"><i class="bi bi-send me-1"></i>Kirim Evaluasi Diri</button>
        </form>
    </div>
    @endif
    @endif
@endsection
