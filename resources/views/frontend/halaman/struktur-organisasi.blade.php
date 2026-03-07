@extends('layouts.frontend')

@section('title', __('menu.structure'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ __('menu.structure') }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('menu.structure') }}</li>
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

            <div class="row justify-content-center">
                @foreach($struktur as $item)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-4">
                            @if($item->foto)
                            <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                            <div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                <i class="bi bi-person fs-1 text-white"></i>
                            </div>
                            @endif
                            <h5 class="card-title mb-1">{{ $item->nama }}</h5>
                            <p class="text-primary fw-semibold mb-2">{{ $item->jabatan }}</p>
                            @if($item->bio)
                            <p class="card-text text-muted small">{{ Str::limit($item->bio, 80) }}</p>
                            @endif
                            @if($item->email)
                            <a href="mailto:{{ $item->email }}" class="text-decoration-none text-muted small d-block">
                                <i class="bi bi-envelope me-1"></i>{{ $item->email }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection