@extends('layouts.frontend')

@section('title', __('menu.accreditation'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ __('menu.accreditation') }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('menu.accreditation') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            @if($halaman)
            <div class="bg-white rounded-4 shadow-sm p-4 mb-5" data-aos="fade-up">
                <div class="content">
                    {!! $halaman->getLocalizedKonten() !!}
                </div>
            </div>
            @endif

            <div class="bg-white rounded-4 shadow-sm p-4" data-aos="fade-up">
                <h4 class="mb-4">Status Akreditasi Program Studi</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Program Studi</th>
                                <th>Jenjang</th>
                                <th>Lembaga</th>
                                <th>Peringkat</th>
                                <th>Berlaku Hingga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prodis as $prodi)
                            <tr>
                                <td class="fw-semibold">{{ $prodi->nama }}</td>
                                <td>{{ $prodi->jenjang }}</td>
                                @if($prodi->activeAkreditasi)
                                <td>{{ $prodi->activeAkreditasi->lembaga }}</td>
                                <td><span class="badge bg-primary">{{ $prodi->activeAkreditasi->peringkat }}</span></td>
                                <td>{{ $prodi->activeAkreditasi->tanggal_kadaluarsa->format('d M Y') }}</td>
                                <td><span class="badge bg-{{ $prodi->activeAkreditasi->status_color }}">{{ ucfirst(str_replace('_', ' ', $prodi->activeAkreditasi->status)) }}</span></td>
                                @else
                                <td colspan="3" class="text-muted">-</td>
                                <td><span class="badge bg-secondary">Belum Terakreditasi</span></td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data program studi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
