@extends('layouts.admin')

@section('title', __('admin.categories'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.categories') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.categories') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.category_list') }}</span>
            <a href="{{ route('admin.kategori-berita.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_new') }}
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.slug') }}</th>
                            <th>{{ __('admin.news_count') }}</th>
                            <th width="150">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kategoris as $index => $kategori)
                        <tr>
                            <td>{{ $kategoris->firstItem() + $index }}</td>
                            <td>{{ $kategori->nama }}</td>
                            <td><code>{{ $kategori->slug }}</code></td>
                            <td><span class="badge bg-info">{{ $kategori->berita_count }}</span></td>
                            <td>
                                <a href="{{ route('admin.kategori-berita.edit', $kategori) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.kategori-berita.destroy', $kategori) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
                            <td colspan="5" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $kategoris->links() }}
        </div>
    </div>
@endsection
