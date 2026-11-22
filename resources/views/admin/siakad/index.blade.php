@extends('layouts.admin')

@section('title', 'Tarik Data SIAKAD')

@php
    $spin = "const b = this.querySelector('button[type=submit]'); b.disabled = true; b.innerHTML = '<span class=&quot;spinner-border spinner-border-sm me-1&quot;></span>Menarik data...';";
@endphp

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tarik Data SIAKAD</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Tarik Data SIAKAD</li>
            </ol>
        </nav>
    </div>

    <p class="text-muted mb-3">
        Ambil data dari API SIAKAD ke SPMI. Data yang kosong di SIAKAD tidak menghapus isian di SPMI.
        Urutan yang disarankan: <strong>Program Studi &rarr; Dosen &rarr; Kurikulum &rarr; Mahasiswa</strong>.
    </p>

    @if(! $configured)
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-1"></i>Koneksi SIAKAD belum diatur. Isi <code>SIAKAD_API_USERNAME</code> dan
        <code>SIAKAD_API_PASSWORD</code> di file <code>.env</code> server, lalu jalankan <code>php artisan config:clear</code>.
    </div>
    @endif

    @if(session('import_warnings'))
    <div class="alert alert-warning">
        <div class="fw-semibold mb-1"><i class="bi bi-info-circle me-1"></i>Isian yang tidak dikenali (data tetap ditarik, isian ini dikosongkan):</div>
        <ul class="mb-0 small" style="max-height: 200px; overflow-y: auto;">
            @foreach(session('import_warnings') as $warn)<li>{{ $warn }}</li>@endforeach
        </ul>
    </div>
    @endif

    @if(session('import_errors'))
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i>{{ session('import_errors_title', 'Detail dari SIAKAD:') }}</div>
        <ul class="mb-0 small" style="max-height: 260px; overflow-y: auto;">
            @foreach(session('import_errors') as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="row g-3">
        {{-- Program Studi --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="bi bi-building me-1"></i>Fakultas &amp; Program Studi</h5>
                    <div class="small text-muted mb-2">SIAKAD: <code>get_fakultas</code>, <code>get_prodi</code> &rarr; menu Program Studi</div>
                    <ul class="small ps-3">
                        <li>Prodi baru ditambahkan; yang sudah ada diperbarui nama, jenjang, dan fakultasnya.</li>
                        <li>Kode SPMI dan Kaprodi tidak diubah.</li>
                    </ul>
                    <div class="small mb-3">Di SPMI: {{ $jumlah['prodi'] }} prodi ({{ $jumlah['prodi_siakad'] }} terhubung SIAKAD) &middot; Terakhir: {{ $last['prodi'] }}</div>
                    <form action="{{ route('admin.siakad.sync.prodi') }}" method="POST" class="mt-auto" onsubmit="{{ $spin }}">
                        @csrf
                        <button type="submit" class="btn btn-dark"><i class="bi bi-cloud-download me-1"></i>Tarik Program Studi</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Dosen --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="bi bi-person-badge me-1"></i>Dosen</h5>
                    <div class="small text-muted mb-2">SIAKAD: <code>get_dosen</code> &rarr; menu Data Dosen</div>
                    <ul class="small ps-3">
                        <li>Program Studi ikut ditarik lebih dulu; prodi dosen diambil dari plot HomeBase di SIAKAD.</li>
                        <li>Dosen yang sudah ada (cocok NIDN/Kode Dosen) diperbarui.</li>
                    </ul>
                    <div class="small mb-2">Di SPMI: {{ $jumlah['dosen'] }} dosen &middot; Terakhir: {{ $last['dosen'] }}</div>
                    <form action="{{ route('admin.siakad.sync.dosen') }}" method="POST" class="mt-auto" onsubmit="{{ $spin }}">
                        @csrf
                        <label for="siakadProdi" class="form-label small fw-semibold mb-1">Prodi untuk dosen baru yang HomeBase-nya kosong <span class="text-muted fw-normal">(opsional)</span></label>
                        <select name="prodi_id" id="siakadProdi" class="form-select form-select-sm mb-2">
                            <option value="">-- Lewati --</option>
                            @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->jenjang }} {{ $p->nama }}</option>
                            @endforeach
                        </select>
                        <div class="form-check small">
                            <input class="form-check-input" type="checkbox" name="isi_email" value="1" id="siakadIsiEmail" checked>
                            <label class="form-check-label" for="siakadIsiEmail">Isi email kosong dengan pola</label>
                        </div>
                        <div class="input-group input-group-sm mb-3">
                            <span class="input-group-text">KodeDosen@</span>
                            <input type="text" name="email_domain" class="form-control" value="unukase.ac.id" aria-label="Domain email">
                        </div>
                        <button type="submit" class="btn btn-dark"><i class="bi bi-cloud-download me-1"></i>Tarik Dosen</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kurikulum --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="bi bi-journal-bookmark me-1"></i>Kurikulum / Mata Kuliah</h5>
                    <div class="small text-muted mb-2">SIAKAD: <code>get_kurikulum</code> (KodeProgdi), <code>get_matakuliah</code> &rarr; DKPS Tabel Kurikulum</div>
                    <ul class="small ps-3">
                        <li>Mata kuliah prodi DKPS dipilih ditambahkan; yang sudah ada (cocok kode/nama MK) diperbarui semester dan SKS-nya.</li>
                        <li>Tautan RPS, asesmen CPL, dan centang kompetensi inti tidak diubah.</li>
                    </ul>
                    <div class="small mb-2">Terakhir: {{ $last['kurikulum'] }}</div>
                    @include('admin.siakad._dkps-form', ['route' => 'admin.siakad.sync.kurikulum', 'label' => 'Tarik Kurikulum', 'idPrefix' => 'kur'])
                </div>
            </div>
        </div>

        {{-- Mahasiswa --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="bi bi-people me-1"></i>Mahasiswa</h5>
                    <div class="small text-muted mb-2">SIAKAD: <code>get_mahasiswa</code> &rarr; DKPS Tabel Kualitas Input Mahasiswa</div>
                    <ul class="small ps-3">
                        <li>Jumlah mahasiswa baru (reguler/transfer) dihitung per angkatan untuk TS-4 s.d. TS.</li>
                        <li>Jumlah mahasiswa aktif diisi untuk baris TS (status saat ini).</li>
                        <li>Daya tampung, pendaftar, dan lulus seleksi tidak ada di SIAKAD, tetap diisi manual.</li>
                    </ul>
                    <div class="small mb-2">Terakhir: {{ $last['mahasiswa'] }}</div>
                    @include('admin.siakad._dkps-form', ['route' => 'admin.siakad.sync.mahasiswa', 'label' => 'Tarik Mahasiswa', 'idPrefix' => 'mhs'])
                </div>
            </div>
        </div>
    </div>

    {{-- Cek kolom API --}}
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-plug me-1"></i>Cek Koneksi &amp; Kolom API</h5>
            <p class="small text-muted">
                Menampilkan nama kolom dan 3 contoh baris (isi pribadi disamarkan) dari layanan SIAKAD. Pakai ini bila ada data yang tidak terbaca.
            </p>
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-sm-4">
                    <label for="cekAct" class="form-label small mb-1">Layanan</label>
                    <select name="cek" id="cekAct" class="form-select form-select-sm">
                        @foreach(\App\Http\Controllers\Admin\SiakadController::CEK_ACTS as $act)
                        <option value="{{ $act }}" @selected(($cek['act'] ?? request('cek')) === $act)>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <label for="cekProgdi" class="form-label small mb-1">KodeProgdi <span class="text-muted">(untuk get_kurikulum)</span></label>
                    <select name="progdi" id="cekProgdi" class="form-select form-select-sm">
                        <option value="">-</option>
                        @foreach($prodis->whereNotNull('kode_siakad') as $p)
                        <option value="{{ $p->kode_siakad }}" @selected(request('progdi') === $p->kode_siakad)>{{ $p->kode_siakad }} - {{ $p->jenjang }} {{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <button type="submit" class="btn btn-outline-dark btn-sm"><i class="bi bi-search me-1"></i>Cek</button>
                </div>
            </form>
            @if($cek)
            <pre class="bg-dark text-light p-3 rounded small mt-3 mb-0" style="max-height: 420px; overflow: auto; white-space: pre-wrap;">{{ $cek['output'] }}</pre>
            @endif
        </div>
    </div>
@endsection
