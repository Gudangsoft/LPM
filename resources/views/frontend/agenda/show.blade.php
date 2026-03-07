@extends('layouts.frontend')

@section('title', $agenda->judul)

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ $agenda->judul }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('agenda.index') }}">{{ __('menu.agenda') }}</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($agenda->judul, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="bg-white rounded-4 shadow-sm p-4 p-lg-5" data-aos="fade-up">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar fs-4 text-primary me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">{{ __('labels.date') }}</small>
                                        <strong>{{ $agenda->formatted_date }}</strong>
                                    </div>
                                </div>
                            </div>
                            @if($agenda->waktu_mulai)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock fs-4 text-primary me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">{{ __('labels.time') }}</small>
                                        <strong>{{ $agenda->waktu_mulai }} @if($agenda->waktu_selesai) - {{ $agenda->waktu_selesai }} @endif</strong>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        @if($agenda->lokasi)
                        <div class="mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt fs-4 text-primary me-3"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('labels.location') }}</small>
                                    <strong>{{ $agenda->lokasi }}</strong>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($agenda->deskripsi)
                        <hr>
                        <div class="content mt-4">
                            {!! nl2br(e($agenda->deskripsi)) !!}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection