@extends('layouts.frontend')

@section('title', $pengumuman->judul)

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ $pengumuman->judul }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pengumuman.index') }}">{{ __('menu.announcements') }}</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($pengumuman->judul, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="bg-white rounded-4 shadow-sm p-4 p-lg-5" data-aos="fade-up">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>{{ $pengumuman->created_at->format('d F Y') }}
                            </small>
                            @if($pengumuman->is_important)
                            <span class="badge bg-danger">{{ __('labels.important') }}</span>
                            @endif
                        </div>
                        
                        <div class="content">
                            {!! $pengumuman->konten !!}
                        </div>

                        @if($pengumuman->lampiran)
                        <hr class="my-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-paperclip fs-4 me-2"></i>
                            <a href="{{ Storage::url($pengumuman->lampiran) }}" target="_blank" class="text-decoration-none">
                                {{ __('labels.attachment') }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection