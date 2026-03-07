@extends('layouts.frontend')

@section('title', __('menu.gallery'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ __('menu.gallery') }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('menu.gallery') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <!-- Filter -->
            @if($kategoris->count() > 0)
            <div class="mb-4" data-aos="fade-up">
                <a href="{{ route('galeri.index') }}" class="btn btn-{{ !request('kategori') ? 'primary' : 'outline-primary' }} btn-sm me-2 mb-2">{{ __('labels.all') }}</a>
                @foreach($kategoris as $kategori)
                <a href="{{ route('galeri.index', ['kategori' => $kategori]) }}" class="btn btn-{{ request('kategori') == $kategori ? 'primary' : 'outline-primary' }} btn-sm me-2 mb-2">{{ $kategori }}</a>
                @endforeach
            </div>
            @endif

            <div class="row">
                @forelse($galeri as $item)
                <div class="col-lg-3 col-md-4 col-6 mb-4" data-aos="zoom-in">
                    <a href="{{ Storage::url($item->gambar) }}" class="d-block gallery-item" data-fancybox="gallery" data-caption="{{ $item->judul }}">
                        <div class="position-relative overflow-hidden rounded-3">
                            <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->judul }}" class="img-fluid" style="height: 200px; width: 100%; object-fit: cover; transition: transform 0.3s;">
                            <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-2">
                                <small class="d-block text-truncate">{{ $item->judul }}</small>
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info">{{ __('messages.no_gallery') }}</div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $galeri->links() }}
            </div>
        </div>
    </section>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<style>
    .gallery-item:hover img {
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    Fancybox.bind("[data-fancybox]", {});
</script>
@endpush