@extends('layouts.admin')

@section('title', __('admin.announcements'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.announcements') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.announcements') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.announcement_list') }}</span>
            <a href="{{ route('admin.pengumuman.create') }}" class="btn btn-primary btn-sm">
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

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>{{ __('admin.title') }}</th>
                            <th>{{ __('admin.priority') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.date') }}</th>
                            <th width="150">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengumumans as $index => $pengumuman)
                        <tr>
                            <td>{{ $pengumumans->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ Str::limit($pengumuman->judul, 50) }}</strong>
                            </td>
                            <td>
                                @if($pengumuman->is_important)
                                <span class="badge bg-danger">{{ __('admin.important') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('admin.normal') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($pengumuman->is_active)
                                <span class="badge bg-success">{{ __('admin.published') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('admin.draft') }}</span>
                                @endif
                            </td>
                            <td>{{ $pengumuman->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('pengumuman.show', $pengumuman->slug) }}" class="btn btn-sm btn-info" target="_blank" title="{{ __('admin.view') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.pengumuman.edit', $pengumuman) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.pengumuman.destroy', $pengumuman) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $pengumumans->links() }}
        </div>
    </div>
@endsection
