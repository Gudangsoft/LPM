@extends('layouts.frontend')

@section('title', __('menu.home'))

@section('content')
    <!-- Hero Slider - Fullscreen Cinematic -->
    @if($sliders->count() > 0)
    <section class="hero-section">
        <!-- Decorative Shapes -->
        <div class="hero-shape hero-shape-1"></div>
        <div class="hero-shape hero-shape-2"></div>
        <div class="hero-shape hero-shape-3"></div>

        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
            <div class="carousel-indicators">
                @foreach($sliders as $index => $slider)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>

            <div class="carousel-inner">
                @foreach($sliders as $index => $slider)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                    <div class="hero-slide">
                        <!-- Background Image -->
                        <div class="hero-slide-bg">
                            <img src="{{ Storage::url($slider->gambar) }}" alt="{{ $slider->judul ?? 'Slider' }}">
                        </div>
                        <!-- Overlay -->
                        <div class="hero-slide-overlay"></div>
                        
                        <!-- Content -->
                        <div class="hero-slide-content">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <div class="hero-content-wrapper">
                                            <div class="hero-tag">
                                                <i class="bi bi-patch-check-fill"></i>
                                                {{ $slider->button_text ?: __('home.quality_assurance') }}
                                            </div>
                                            @if($slider->judul)
                                            <h1 class="hero-title">{{ $slider->judul }}</h1>
                                            @endif
                                            @if($slider->deskripsi)
                                            <p class="hero-description">{{ $slider->deskripsi }}</p>
                                            @endif
                                            <div class="hero-buttons">
                                                @if($slider->link)
                                                <a href="{{ $slider->link }}" class="hero-btn-primary">
                                                    {{ __('buttons.learn_more') }}
                                                    <i class="bi bi-arrow-right-circle-fill"></i>
                                                </a>
                                                @endif
                                                <a href="{{ route('kontak.index') }}" class="hero-btn-secondary">
                                                    <i class="bi bi-envelope-fill"></i>
                                                    {{ __('buttons.contact_us') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Navigation -->
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>

            <!-- Progress Bar -->
            <div class="hero-progress">
                <div class="hero-progress-bar"></div>
            </div>
        </div>

        <!-- Floating Stats -->
        <div class="hero-stats">
            <div class="hero-stat-card">
                <div class="hero-stat-number">{{ $stats['prodi'] ?? '25' }}+</div>
                <div class="hero-stat-label">{{ __('home.accredited_programs') }}</div>
            </div>
            <div class="hero-stat-card">
                <div class="hero-stat-number">{{ $stats['auditor'] ?? '50' }}+</div>
                <div class="hero-stat-label">{{ __('home.certified_auditors') }}</div>
            </div>
            <div class="hero-stat-card">
                <div class="hero-stat-number">{{ $stats['tahun'] ?? '10' }}+</div>
                <div class="hero-stat-label">{{ __('home.years_experience') }}</div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <span>Scroll</span>
            <i class="bi bi-chevron-double-down"></i>
        </div>
    </section>
    @endif

    <!-- Sambutan Ketua -->
    @if($sambutanKetua)
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 mb-4 mb-lg-0" data-aos="fade-right">
                    <img src="{{ asset('images/ketua-lpm.jpg') }}" alt="Ketua LPM" class="img-fluid rounded-4 shadow" onerror="this.src='https://via.placeholder.com/400x500?text=Ketua+LPM'">
                </div>
                <div class="col-lg-8" data-aos="fade-left">
                    <div class="section-title">
                        <h2>{{ __('home.welcome_message') }}</h2>
                    </div>
                    <div class="content">
                        {!! Str::limit(strip_tags($sambutanKetua->getLocalizedKonten()), 500) !!}
                    </div>
                    <a href="{{ route('halaman.show', 'sambutan-ketua') }}" class="btn btn-outline-primary mt-3">{{ __('buttons.read_more') }}</a>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Berita Terbaru -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="section-title mb-0" data-aos="fade-right">
                    <h2>{{ __('home.latest_news') }}</h2>
                </div>
                <a href="{{ route('berita.index') }}" class="btn btn-outline-primary" data-aos="fade-left">{{ __('buttons.view_all') }}</a>
            </div>
            <div class="row">
                @forelse($berita as $item)
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card card-berita">
                        @if($item->thumbnail)
                        <img src="{{ Storage::url($item->thumbnail) }}" class="card-img-top" alt="{{ $item->judul }}">
                        @else
                        <img src="https://via.placeholder.com/400x250?text=Berita" class="card-img-top" alt="{{ $item->judul }}">
                        @endif
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="badge bg-primary">{{ $item->kategori->nama ?? 'Umum' }}</span>
                                <small class="text-muted ms-2">{{ $item->published_at?->format('d M Y') }}</small>
                            </div>
                            <h5 class="card-title">
                                <a href="{{ route('berita.show', $item->slug) }}">{{ Str::limit($item->judul, 60) }}</a>
                            </h5>
                            <p class="card-text text-muted">{{ Str::limit(strip_tags($item->ringkasan ?: $item->konten), 100) }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">{{ __('messages.no_news') }}</div>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Pengumuman & Agenda -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <!-- Pengumuman -->
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                    <div class="section-title">
                        <h2>{{ __('home.announcements') }}</h2>
                    </div>
                    <div class="sidebar-widget">
                        @forelse($pengumuman as $item)
                        <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white text-center rounded p-2" style="width: 60px;">
                                    <div class="fs-5 fw-bold">{{ $item->created_at->format('d') }}</div>
                                    <small>{{ $item->created_at->format('M') }}</small>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">
                                    <a href="{{ route('pengumuman.show', $item->slug) }}" class="text-dark text-decoration-none">
                                        {{ Str::limit($item->judul, 50) }}
                                    </a>
                                </h6>
                                @if($item->is_important)
                                <span class="badge bg-danger">{{ __('labels.important') }}</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-muted mb-0">{{ __('messages.no_announcements') }}</p>
                        @endforelse
                        <a href="{{ route('pengumuman.index') }}" class="btn btn-sm btn-outline-primary mt-2">{{ __('buttons.view_all') }}</a>
                    </div>
                </div>

                <!-- Agenda -->
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="section-title">
                        <h2>{{ __('home.agenda') }}</h2>
                    </div>
                    <div class="sidebar-widget">
                        @forelse($agenda as $item)
                        <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-shrink-0">
                                <div class="bg-warning text-dark text-center rounded p-2" style="width: 60px;">
                                    <div class="fs-5 fw-bold">{{ $item->tanggal_mulai->format('d') }}</div>
                                    <small>{{ $item->tanggal_mulai->format('M') }}</small>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">
                                    <a href="{{ route('agenda.show', $item->slug) }}" class="text-dark text-decoration-none">
                                        {{ Str::limit($item->judul, 50) }}
                                    </a>
                                </h6>
                                @if($item->lokasi)
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $item->lokasi }}</small>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-muted mb-0">{{ __('messages.no_agenda') }}</p>
                        @endforelse
                        <a href="{{ route('agenda.index') }}" class="btn btn-sm btn-outline-primary mt-2">{{ __('buttons.view_all') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeri -->
    @if($galeri->count() > 0)
    <section class="py-5">
        <div class="container">
            <div class="section-title text-center" data-aos="fade-up">
                <h2>{{ __('home.gallery') }}</h2>
            </div>
            <div class="row">
                @foreach($galeri as $item)
                <div class="col-lg-3 col-md-4 col-6 mb-4" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
                    <a href="{{ Storage::url($item->gambar) }}" class="d-block" data-fancybox="gallery" data-caption="{{ $item->judul }}">
                        <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->judul }}" class="img-fluid rounded-3 shadow-sm" style="height: 180px; width: 100%; object-fit: cover;">
                    </a>
                </div>
                @endforeach
            </div>
            <div class="text-center" data-aos="fade-up">
                <a href="{{ route('galeri.index') }}" class="btn btn-outline-primary">{{ __('buttons.view_all_gallery') }}</a>
            </div>
        </div>
    </section>
    @endif
@endsection

@push('scripts')
<script>
    // Hero Slider Progress Bar
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('heroCarousel');
        if (carousel) {
            const progressBar = carousel.querySelector('.hero-progress-bar');
            
            // Reset progress animation on slide change
            carousel.addEventListener('slide.bs.carousel', function() {
                if (progressBar) {
                    progressBar.style.animation = 'none';
                    progressBar.offsetHeight; // Trigger reflow
                    progressBar.style.animation = 'progress 6s linear infinite';
                }
            });
        }
    });
</script>
@endpush