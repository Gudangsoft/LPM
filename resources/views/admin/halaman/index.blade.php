@extends('layouts.admin')

@section('title', __('admin.pages'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.pages') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.pages') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.page_list') }}</span>
            <a href="{{ route('admin.halaman.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_new') }}
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>{{ __('admin.title') }}</th>
                            <th>{{ __('admin.slug') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.updated_at') }}</th>
                            <th width="150">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($halamans as $index => $halaman)
                        <tr>
                            <td>{{ $halamans->firstItem() + $index }}</td>
                            <td>{{ $halaman->judul }}</td>
                            <td><code>{{ $halaman->slug }}</code></td>
                            <td>
                                @if($halaman->is_active)
                                <span class="badge bg-success">{{ __('admin.published') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('admin.draft') }}</span>
                                @endif
                            </td>
                            <td>{{ $halaman->updated_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('halaman.show', $halaman->slug) }}" class="btn btn-sm btn-info" target="_blank" title="{{ __('admin.view') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.halaman.edit', $halaman) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.halaman.destroy', $halaman) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
            {{ $halamans->links() }}
        </div>
    </div>
@endsection
