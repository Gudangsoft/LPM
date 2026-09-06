@extends('layouts.admin')

@section('title', __('admin.news'))

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">{{ __('admin.news') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">{{ __('admin.news') }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.berita.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_news') }}
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <form action="{{ route('admin.berita.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="{{ __('admin.search') }}..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-outline-primary">{{ __('admin.search') }}</button>
                @if(request('search'))
                <a href="{{ route('admin.berita.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </form>
            <span class="text-muted small">{{ $berita->total() }} {{ __('admin.news') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('admin.title') }}</th>
                            <th>{{ __('admin.category') }}</th>
                            <th>{{ __('admin.author') }}</th>
                            <th>{{ __('admin.views') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.date') }}</th>
                            <th style="width: 120px;">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berita as $index => $item)
                        <tr>
                            <td>{{ $berita->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->thumbnail)
                                    <img src="{{ Storage::url($item->thumbnail) }}" alt="" class="rounded me-2" style="width: 50px; height: 35px; object-fit: cover;">
                                    @else
                                    <div class="rounded me-2 bg-light d-flex align-items-center justify-content-center text-muted flex-shrink-0" style="width: 50px; height: 35px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ Str::limit($item->judul, 40) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary">{{ $item->kategori->nama ?? '-' }}</span></td>
                            <td>{{ $item->user->name ?? '-' }}</td>
                            <td>{{ $item->views }}</td>
                            <td>
                                @if($item->is_published)
                                <span class="badge bg-success">{{ __('admin.published') }}</span>
                                @else
                                <span class="badge bg-warning">{{ __('admin.draft') }}</span>
                                @endif
                            </td>
                            <td>{{ $item->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    @if($item->is_published)
                                    <a href="{{ route('berita.show', $item->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="{{ __('admin.view') }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('admin.berita.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="{{ __('admin.edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.berita.destroy', $item) }}" method="POST" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('admin.delete') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                {{ __('admin.no_data') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($berita->hasPages())
        <div class="card-footer">
            {{ $berita->links() }}
        </div>
        @endif
    </div>
@endsection
