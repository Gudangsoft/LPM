@extends('layouts.frontend')

@section('title', __('menu.news'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ __('menu.news') }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('menu.news') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Search Form -->
                    <div class="mb-4" data-aos="fade-up">
                        <form action="{{ route('berita.index') }}" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="{{ __('labels.search_news') }}" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>

                    @if(request('search'))
                    <div class="mb-4">
                        <p>{{ __('messages.search_results_for') }}: <strong>{{ request('search') }}</strong></p>
                    </div>
                    @endif

                    <div class="row">
                        @forelse($berita as $item)
                        <div class="col-md-6 mb-4" data-aos="fade-up">
                            <div class="card card-berita h-100">
                                @if($item->thumbnail)
                                <img src="{{ Storage::url($item->thumbnail) }}" class="card-img-top" alt="{{ $item->judul }}">
                                @else
                                <img src="https://via.placeholder.com/400x250?text=Berita" class="card-img-top" alt="{{ $item->judul }}">
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <span class="badge bg-primary">{{ $item->kategori->nama ?? 'Umum' }}</span>
                                        <small class="text-muted ms-2">{{ $item->published_at?->format('d M Y') }}</small>
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
                            <div class="alert alert-info">{{ __('messages.no_news') }}</div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $berita->links() }}
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Categories -->
                    <div class="sidebar-widget" data-aos="fade-left">
                        <h5>{{ __('labels.categories') }}</h5>
                        <ul class="list-unstyled">
                            @foreach($kategoris as $kategori)
                            <li class="d-flex justify-content-between align-items-center mb-2">
                                <a href="{{ route('berita.index', ['kategori' => $kategori->slug]) }}" class="text-decoration-none text-dark">
                                    <i class="bi bi-folder me-2"></i>{{ $kategori->nama }}
                                </a>
                                <span class="badge bg-secondary">{{ $kategori->berita_count }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection