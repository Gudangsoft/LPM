@extends('layouts.frontend')

@section('title', __('menu.announcements'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ __('menu.announcements') }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('menu.announcements') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    @forelse($pengumuman as $item)
                    <div class="bg-white rounded-4 shadow-sm p-4 mb-4" data-aos="fade-up">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="mb-1">
                                    <a href="{{ route('pengumuman.show', $item->slug) }}" class="text-decoration-none text-dark">
                                        {{ $item->judul }}
                                    </a>
                                </h4>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>{{ $item->created_at->format('d F Y') }}
                                </small>
                            </div>
                            @if($item->is_important)
                            <span class="badge bg-danger">{{ __('labels.important') }}</span>
                            @endif
                        </div>
                        <p class="text-muted mb-3">{{ Str::limit(strip_tags($item->konten), 200) }}</p>
                        <a href="{{ route('pengumuman.show', $item->slug) }}" class="btn btn-sm btn-outline-primary">{{ __('buttons.read_more') }}</a>
                    </div>
                    @empty
                    <div class="alert alert-info">{{ __('messages.no_announcements') }}</div>
                    @endforelse

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $pengumuman->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection