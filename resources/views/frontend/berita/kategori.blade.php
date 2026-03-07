@extends('layouts.frontend')

@section('title', __('labels.category') . ': ' . $kategori->nama)

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ $kategori->nama }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('berita.index') }}">{{ __('menu.news') }}</a></li>
                    <li class="breadcrumb-item active">{{ $kategori->nama }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row">
                @forelse($berita as $item)
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up">
                    <div class="card card-berita h-100">
                        @if($item->thumbnail)
                        <img src="{{ Storage::url($item->thumbnail) }}" class="card-img-top" alt="{{ $item->judul }}">
                        @else
                        <img src="https://via.placeholder.com/400x250?text=Berita" class="card-img-top" alt="{{ $item->judul }}">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <small class="text-muted">{{ $item->published_at?->format('d M Y') }}</small>
                            </div>
                            <h5 class="card-title">
                                <a href="{{ route('berita.show', $item->slug) }}">{{ Str::limit($item->judul, 60) }}</a>
                            </h5>
                            <p class="card-text text-muted flex-grow-1">{{ Str::limit(strip_tags($item->ringkasan ?: $item->konten), 120) }}</p>
                            <div class="mt-auto">
                                <a href="{{ route('berita.show', $item->slug) }}" class="btn btn-sm btn-outline-primary">{{ __('buttons.read_more') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">{{ __('messages.no_news_in_category') }}</div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $berita->links() }}
            </div>
        </div>
    </section>
@endsection