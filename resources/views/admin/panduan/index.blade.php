@extends('layouts.admin')

@section('title', 'Buku Panduan')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1 class="page-title">Buku Panduan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Buku Panduan</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.panduan.export-pdf') }}" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-pdf me-1"></i>Unduh PDF
            </a>
            <a href="{{ route('admin.panduan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Bab
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card" style="top: 1rem; position: sticky;">
                <div class="card-header">Daftar Isi</div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($chapters as $kategori => $bab)
                        <div class="list-group-item bg-light">
                            <strong class="small text-uppercase text-muted">{{ $kategori }}</strong>
                        </div>
                        @foreach($bab as $item)
                        <a href="#{{ $item->slug }}" class="list-group-item list-group-item-action small">
                            <i class="bi {{ $item->icon ?? 'bi-file-text' }} me-2"></i>{{ $item->judul }}
                        </a>
                        @endforeach
                        @empty
                        <div class="list-group-item text-muted small">Belum ada bab panduan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            @forelse($chapters as $kategori => $bab)
            <h5 class="text-uppercase text-muted mt-2 mb-3">{{ $kategori }}</h5>
            @foreach($bab as $item)
            <div class="card mb-4" id="{{ $item->slug }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi {{ $item->icon ?? 'bi-file-text' }} me-2"></i>{{ $item->judul }}</span>
                    <div>
                        <a href="{{ route('admin.panduan.edit', $item) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.panduan.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    {!! $item->konten !!}
                </div>
            </div>
            @endforeach
            @empty
            <div class="card">
                <div class="card-body text-center text-muted py-5">Belum ada bab panduan.</div>
            </div>
            @endforelse
        </div>
    </div>
@endsection
