@extends('layouts.frontend')

@section('title', __('menu.agenda'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ __('menu.agenda') }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('menu.agenda') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    @forelse($upcoming as $item)
                    <div class="bg-white rounded-4 shadow-sm p-4 mb-4" data-aos="fade-up">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center mb-3 mb-md-0">
                                <div class="bg-primary text-white rounded-3 p-3">
                                    <div class="fs-2 fw-bold">{{ $item->tanggal_mulai->format('d') }}</div>
                                    <div>{{ $item->tanggal_mulai->format('M Y') }}</div>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <h4 class="mb-2">
                                    <a href="{{ route('agenda.show', $item->slug) }}" class="text-decoration-none text-dark">
                                        {{ $item->judul }}
                                    </a>
                                </h4>
                                @if($item->lokasi)
                                <p class="text-muted mb-2">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $item->lokasi }}
                                </p>
                                @endif
                                @if($item->waktu_mulai)
                                <p class="text-muted mb-2">
                                    <i class="bi bi-clock me-1"></i>{{ $item->waktu_mulai }} 
                                    @if($item->waktu_selesai) - {{ $item->waktu_selesai }} @endif
                                </p>
                                @endif
                                @if($item->deskripsi)
                                <p class="mb-0">{{ Str::limit(strip_tags($item->deskripsi), 150) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-info">{{ __('messages.no_agenda') }}</div>
                    @endforelse

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $upcoming->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection