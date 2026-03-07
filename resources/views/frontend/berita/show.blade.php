@extends('layouts.frontend')

@section('title', $berita->judul)
@section('meta_description', $berita->ringkasan ?: Str::limit(strip_tags($berita->konten), 160))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ $berita->judul }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('berita.index') }}">{{ __('menu.news') }}</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($berita->judul, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <article class="bg-white rounded-4 shadow-sm p-4" data-aos="fade-up">
                        <!-- Meta Info -->
                        <div class="d-flex flex-wrap gap-3 mb-4 text-muted">
                            <span><i class="bi bi-calendar me-1"></i> {{ $berita->published_at?->format('d F Y') }}</span>
                            <span><i class="bi bi-person me-1"></i> {{ $berita->user->name ?? 'Admin' }}</span>
                            <span><i class="bi bi-folder me-1"></i> {{ $berita->kategori->nama ?? 'Umum' }}</span>
                            <span><i class="bi bi-eye me-1"></i> {{ $berita->views }} {{ __('labels.views') }}</span>
                        </div>

                        <!-- Featured Image -->
                        @if($berita->thumbnail)
                        <div class="mb-4">
                            <img src="{{ Storage::url($berita->thumbnail) }}" alt="{{ $berita->judul }}" class="img-fluid rounded-3 w-100">
                        </div>
                        @endif

                        <!-- Content -->
                        <div class="content">
                            {!! $berita->konten !!}
                        </div>

                        <!-- Share Buttons -->
                        <hr class="my-4">
                        <div class="d-flex align-items-center">
                            <span class="me-3 fw-semibold">{{ __('labels.share') }}:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($berita->judul) }}" target="_blank" class="btn btn-sm btn-outline-info me-2">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($berita->judul . ' ' . url()->current()) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </article>

                    <!-- Related News -->
                    @if($relatedBerita->count() > 0)
                    <div class="mt-5" data-aos="fade-up">
                        <h4 class="mb-4">{{ __('labels.related_news') }}</h4>
                        <div class="row">
                            @foreach($relatedBerita as $item)
                            <div class="col-md-6 mb-4">
                                <div class="card card-berita h-100">
                                    @if($item->thumbnail)
                                    <img src="{{ Storage::url($item->thumbnail) }}" class="card-img-top" alt="{{ $item->judul }}" style="height: 150px; object-fit: cover;">
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="{{ route('berita.show', $item->slug) }}">{{ Str::limit($item->judul, 50) }}</a>
                                        </h6>
                                        <small class="text-muted">{{ $item->published_at?->format('d M Y') }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar-widget" data-aos="fade-left">
                        <h5>{{ __('labels.other_news') }}</h5>
                        @php
                            $otherNews = App\Models\Berita::published()->where('id', '!=', $berita->id)->latest()->take(5)->get();
                        @endphp
                        @foreach($otherNews as $item)
                        <div class="d-flex mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                            @if($item->thumbnail)
                            <img src="{{ Storage::url($item->thumbnail) }}" alt="{{ $item->judul }}" class="rounded" style="width: 80px; height: 60px; object-fit: cover;">
                            @endif
                            <div class="ms-3">
                                <h6 class="mb-1 fs-6">
                                    <a href="{{ route('berita.show', $item->slug) }}" class="text-decoration-none text-dark">
                                        {{ Str::limit($item->judul, 40) }}
                                    </a>
                                </h6>
                                <small class="text-muted">{{ $item->published_at?->format('d M Y') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection