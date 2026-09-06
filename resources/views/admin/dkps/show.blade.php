@extends('layouts.admin')

@section('title', 'Isi DKPS - ' . ($submission->prodi->nama ?? ''))

@php
    $toc = [
        'Kerjasama & Mahasiswa' => [
            ['slug' => 'kerjasama', 'label' => 'Tabel 1 - Kerjasama Tridharma'],
            ['slug' => 'kualitas-input', 'label' => 'Tabel 2 - Kualitas Input Mahasiswa'],
            ['slug' => 'prestasi-mahasiswa', 'label' => 'Tabel 3 - Prestasi Mahasiswa'],
            ['slug' => 'karya-inovatif', 'label' => 'Tabel 4 - Karya Inovatif Mahasiswa'],
            ['slug' => 'kepuasan-mahasiswa', 'label' => 'Tabel 5 - Kepuasan Mahasiswa'],
        ],
        'Dosen & SDM' => [
            ['slug' => 'dosen-tetap', 'label' => 'Tabel 6 - Dosen Tetap Perguruan Tinggi'],
            ['slug' => 'beban-kerja-dtps', 'label' => 'Tabel 7 - Beban Kerja DTPS'],
            ['slug' => 'rekognisi-dtps', 'label' => 'Tabel 8 - Rekognisi DTPS'],
            ['slug' => 'pengembangan-kompetensi-dosen', 'label' => 'Tabel 9 - Pengembangan Kompetensi DTPS'],
            ['slug' => 'tenaga-kependidikan', 'label' => 'Tabel 10 - Tenaga Kependidikan'],
            ['slug' => 'pengembangan-kompetensi-tendik', 'label' => 'Tabel 11 - Pengembangan Kompetensi Tendik'],
        ],
        'Sarana & Kurikulum' => [
            ['slug' => 'penggunaan-dana', 'label' => 'Tabel 12 - Penggunaan Dana'],
            ['slug' => 'sarana-lab', 'label' => 'Tabel 13 - Sarana Laboratorium'],
            ['slug' => 'prasarana', 'label' => 'Tabel 14 - Prasarana Pendidikan'],
            ['slug' => 'tik', 'label' => 'Tabel 15 - Teknologi Informasi & Komunikasi'],
            ['slug' => 'kurikulum', 'label' => 'Tabel 16 - Kurikulum'],
            ['slug' => 'integrasi-penelitian-pkm', 'label' => 'Tabel 17 - Integrasi Penelitian & PkM'],
            ['slug' => 'pembimbingan-magang', 'label' => 'Tabel 18 - Pembimbingan Magang Kependidikan'],
            ['slug' => 'kegiatan-luar-kelas', 'label' => 'Tabel 19 - Kegiatan Akademik di Luar Kelas'],
            ['slug' => 'pembimbingan-ta', 'label' => 'Tabel 20 - Pembimbingan Tugas Akhir/Skripsi'],
        ],
        'Lulusan' => [
            ['slug' => 'ipk-lulusan', 'label' => 'Tabel 21 - IPK Lulusan'],
            ['slug' => 'masa-studi', 'label' => 'Tabel 22 - Masa Studi Lulusan'],
            ['slug' => 'lulusan-bekerja', 'label' => 'Tabel 23 - Lulusan Bekerja & Studi Lanjut'],
            ['slug' => 'waktu-tunggu', 'label' => 'Tabel 24 - Waktu Tunggu Kerja Pertama'],
            ['slug' => 'kesesuaian-bidang', 'label' => 'Tabel 25 - Kesesuaian Bidang Kerja'],
            ['slug' => 'kepuasan-pengguna', 'label' => 'Tabel 26 - Kepuasan Pengguna Lulusan'],
        ],
        'Penelitian & PkM' => [
            ['slug' => 'penelitian-ringkasan', 'label' => 'Tabel 27 - Penelitian DTPS'],
            ['slug' => 'penelitian-mahasiswa', 'label' => 'Tabel 28 - Penelitian melibatkan Mahasiswa'],
            ['slug' => 'publikasi-dtps', 'label' => 'Tabel 29 - Publikasi Ilmiah DTPS'],
            ['slug' => 'publikasi-detail', 'label' => 'Tabel 30 - Publikasi Sinta/Scopus'],
            ['slug' => 'sitasi-dtps', 'label' => 'Tabel 31 - Karya Ilmiah DTPS yang Disitasi'],
            ['slug' => 'pkm-ringkasan', 'label' => 'Tabel 32 - PkM DTPS'],
            ['slug' => 'pkm-mahasiswa', 'label' => 'Tabel 33 - PkM melibatkan Mahasiswa'],
        ],
    ];
@endphp

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="page-title">DKPS - {{ $submission->prodi->nama ?? '-' }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dkps.index') }}">DKPS</a></li>
                    <li class="breadcrumb-item active">{{ $submission->prodi->nama ?? '-' }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dkps.export', $submission) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="{{ route('admin.dkps.edit', $submission) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit Info
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>Program Studi</strong><br>{{ $submission->prodi->full_name ?? '-' }}</div>
                <div class="col-md-3"><strong>Tahun Akademik (TS)</strong><br>{{ $submission->tahun_ajaran }}</div>
                <div class="col-md-3"><strong>Nama Pengusul</strong><br>{{ $submission->nama_pengusul ?? '-' }}</div>
                <div class="col-md-3"><strong>{{ __('admin.status') }}</strong><br><span class="badge bg-{{ $submission->status_color }}">{{ ucfirst($submission->status) }}</span></div>
            </div>
            @if($submission->akreditasi)
            <hr>
            <div class="small text-muted">
                Terhubung ke akreditasi: {{ $submission->akreditasi->lembaga }} - Peringkat {{ $submission->akreditasi->peringkat }},
                SK {{ $submission->akreditasi->nomor_sk }}
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card" style="top: 1rem; position: sticky;">
                <div class="card-header">Daftar Isi</div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($toc as $kategori => $items)
                        <div class="list-group-item bg-light">
                            <strong class="small text-uppercase text-muted">{{ $kategori }}</strong>
                        </div>
                        @foreach($items as $item)
                        <a href="#{{ $item['slug'] }}" class="list-group-item list-group-item-action small">{{ $item['label'] }}</a>
                        @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            @foreach($toc as $kategori => $items)
            <h5 class="text-uppercase text-muted mt-2 mb-3">{{ $kategori }}</h5>
            @foreach($items as $item)
            <div class="card mb-4" id="{{ $item['slug'] }}">
                <div class="card-header">{{ $item['label'] }}</div>
                <div class="card-body">
                    @includeIf('admin.dkps.sections.' . str_replace('-', '_', $item['slug']), ['submission' => $submission])
                    @unless(\Illuminate\Support\Facades\View::exists('admin.dkps.sections.' . str_replace('-', '_', $item['slug'])))
                    <p class="text-muted mb-0"><i class="bi bi-hourglass-split me-1"></i>Bagian ini akan segera tersedia.</p>
                    @endunless
                </div>
            </div>
            @endforeach
            @endforeach
        </div>
    </div>
@endsection
