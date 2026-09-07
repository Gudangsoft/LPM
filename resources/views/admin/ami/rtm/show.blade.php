@extends('layouts.admin')

@section('title', 'RTM — ' . $rtm->judul)

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">{{ $rtm->judul }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ami.rtm.index') }}">RTM</a></li>
                    <li class="breadcrumb-item active">{{ \Illuminate\Support\Str::limit($rtm->judul, 40) }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 align-self-center">
            <span class="badge bg-{{ $rtm->status_color }} fs-6">{{ $rtm->status }}</span>
            <a href="{{ route('admin.ami.rtm.edit', $rtm) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body row g-2 small">
            <div class="col-md-3"><strong>Periode:</strong> {{ optional($rtm->periodeAmi)->nama ?? '-' }}</div>
            <div class="col-md-3"><strong>Tanggal:</strong> {{ optional($rtm->tanggal)->format('d M Y') ?? '-' }}</div>
            <div class="col-md-2"><strong>Tempat:</strong> {{ $rtm->tempat ?? '-' }}</div>
            <div class="col-md-2"><strong>Pemimpin:</strong> {{ $rtm->pemimpin ?? '-' }}</div>
            <div class="col-md-2"><strong>Notulen:</strong> {{ $rtm->notulen ?? '-' }}</div>
        </div>
    </div>

    {{-- Ringkasan / bahan --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Bahan &amp; Ringkasan</span>
            <form action="{{ route('admin.ami.rtm.generate-ringkasan', $rtm) }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-magic me-1"></i>Generate dari data AMI</button>
            </form>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ami.rtm.update', $rtm) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="judul" value="{{ $rtm->judul }}">
                <input type="hidden" name="periode_ami_id" value="{{ $rtm->periode_ami_id }}">
                <input type="hidden" name="status" value="{{ $rtm->status }}">
                <textarea name="ringkasan" rows="8" class="form-control font-monospace" style="font-size:.85rem">{{ $rtm->ringkasan }}</textarea>
                <button class="btn btn-sm btn-primary mt-2">Simpan Ringkasan</button>
            </form>
        </div>
    </div>

    {{-- Agenda --}}
    <div class="card mb-3">
        <div class="card-header">Agenda Rapat</div>
        <div class="card-body">
            @forelse($rtm->agenda as $a)
            <div class="border rounded p-2 mb-2">
                <form action="{{ route('admin.ami.rtm.agenda.update', $a) }}" method="POST" class="row g-1">
                    @csrf @method('PUT')
                    <div class="col-md-4"><input name="topik" value="{{ $a->topik }}" class="form-control form-control-sm" required></div>
                    <div class="col-md-6"><input name="pembahasan" value="{{ $a->pembahasan }}" class="form-control form-control-sm" placeholder="Pembahasan"></div>
                    <div class="col-md-2 d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1">Simpan</button>
                    </div>
                </form>
                <form action="{{ route('admin.ami.rtm.agenda.delete', $a) }}" method="POST" class="mt-1" onsubmit="return confirm('Hapus agenda?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-link text-danger p-0">hapus</button>
                </form>
            </div>
            @empty
            <div class="text-muted small mb-2">Belum ada agenda.</div>
            @endforelse

            <form action="{{ route('admin.ami.rtm.agenda.add', $rtm) }}" method="POST" class="row g-1 border-top pt-2">
                @csrf
                <div class="col-md-4"><input name="topik" class="form-control form-control-sm" placeholder="Topik agenda baru" required></div>
                <div class="col-md-6"><input name="pembahasan" class="form-control form-control-sm" placeholder="Pembahasan"></div>
                <div class="col-md-2"><button class="btn btn-sm btn-primary w-100"><i class="bi bi-plus-lg"></i> Tambah</button></div>
            </form>
        </div>
    </div>

    {{-- Keputusan & Rekomendasi --}}
    <div class="card mb-4">
        <div class="card-header">Keputusan &amp; Rekomendasi</div>
        <div class="card-body">
            @forelse($rtm->keputusan as $k)
            <div class="border rounded p-3 mb-2">
                <form action="{{ route('admin.ami.rtm.keputusan.update', $k) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-2">
                        <div class="col-md-6"><label class="form-label small mb-1">Keputusan</label><textarea name="keputusan" rows="2" class="form-control form-control-sm" required>{{ $k->keputusan }}</textarea></div>
                        <div class="col-md-6"><label class="form-label small mb-1">Rekomendasi</label><textarea name="rekomendasi" rows="2" class="form-control form-control-sm">{{ $k->rekomendasi }}</textarea></div>
                        <div class="col-md-3"><label class="form-label small mb-1">PIC</label><input name="pic" value="{{ $k->pic }}" class="form-control form-control-sm"></div>
                        <div class="col-md-3"><label class="form-label small mb-1">Target</label><input type="date" name="target_tanggal" value="{{ optional($k->target_tanggal)->format('Y-m-d') }}" class="form-control form-control-sm"></div>
                        <div class="col-md-3"><label class="form-label small mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                @foreach(['belum'=>'Belum','proses'=>'Proses','selesai'=>'Selesai'] as $v=>$l)
                                <option value="{{ $v }}" {{ $k->status === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end"><button class="btn btn-sm btn-outline-primary w-100">Simpan</button></div>
                        <div class="col-12"><label class="form-label small mb-1">Tindak Lanjut</label><input name="tindak_lanjut" value="{{ $k->tindak_lanjut }}" class="form-control form-control-sm"></div>
                    </div>
                </form>
                <form action="{{ route('admin.ami.rtm.keputusan.delete', $k) }}" method="POST" class="mt-1" onsubmit="return confirm('Hapus keputusan?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-link text-danger p-0">hapus</button>
                </form>
            </div>
            @empty
            <div class="text-muted small mb-2">Belum ada keputusan.</div>
            @endforelse

            <form action="{{ route('admin.ami.rtm.keputusan.add', $rtm) }}" method="POST" class="border-top pt-2">
                @csrf
                <div class="row g-2">
                    <div class="col-md-6"><input name="keputusan" class="form-control form-control-sm" placeholder="Keputusan baru" required></div>
                    <div class="col-md-4"><input name="rekomendasi" class="form-control form-control-sm" placeholder="Rekomendasi"></div>
                    <div class="col-md-2"><button class="btn btn-sm btn-primary w-100"><i class="bi bi-plus-lg"></i> Tambah</button></div>
                </div>
            </form>
        </div>
    </div>
@endsection
