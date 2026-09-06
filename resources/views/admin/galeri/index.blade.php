@extends('layouts.admin')

@section('title', __('admin.gallery'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.gallery') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.gallery') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.gallery_list') }}</span>
            <a href="{{ route('admin.galeri.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_new') }}
            </a>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('admin.search') }}..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i>{{ __('admin.search') }}
                    </button>
                </div>
            </form>

            <div class="row">
                @forelse($galeris as $galeri)
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            <img src="{{ Storage::url($galeri->gambar) }}" class="card-img-top" alt="{{ $galeri->judul }}" style="height: 180px; object-fit: cover;">
                            <span class="badge {{ $galeri->is_active ? 'bg-success' : 'bg-secondary' }} position-absolute top-0 end-0 m-2">
                                {{ $galeri->is_active ? __('admin.active') : __('admin.inactive') }}
                            </span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title text-truncate">{{ $galeri->judul }}</h6>
                            @if($galeri->kategori)
                            <span class="badge bg-light text-dark border mb-1">{{ $galeri->kategori }}</span>
                            @endif
                            <p class="card-text small text-muted">{{ $galeri->created_at->format('d M Y') }}</p>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.galeri.edit', $galeri) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.galeri.destroy', $galeri) }}" method="POST" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-images fs-1 d-block mb-2"></i>
                        {{ __('admin.no_data') }}
                    </div>
                </div>
                @endforelse
            </div>
            {{ $galeris->links() }}
        </div>
    </div>
@endsection
