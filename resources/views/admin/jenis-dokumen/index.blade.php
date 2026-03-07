@extends('layouts.admin')

@section('title', __('admin.document_types'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.document_types') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.document_types') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.document_type_list') }}</span>
            <a href="{{ route('admin.jenis-dokumen.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_new') }}
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="60">{{ __('admin.order') }}</th>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.slug') }}</th>
                            <th>{{ __('admin.icon') }}</th>
                            <th>{{ __('admin.document_count') }}</th>
                            <th width="80">{{ __('admin.status') }}</th>
                            <th width="150">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisDokumen as $jenis)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $jenis->urutan }}</span></td>
                            <td>
                                @if($jenis->icon)
                                <i class="bi {{ $jenis->icon }} me-2"></i>
                                @endif
                                {{ $jenis->nama }}
                            </td>
                            <td><code>{{ $jenis->slug }}</code></td>
                            <td>
                                @if($jenis->icon)
                                <i class="bi {{ $jenis->icon }} fs-5"></i>
                                <code class="ms-2">{{ $jenis->icon }}</code>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><span class="badge bg-info">{{ $jenis->dokumen_count }}</span></td>
                            <td>
                                @if($jenis->is_active)
                                <span class="badge bg-success">{{ __('admin.active') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('admin.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.jenis-dokumen.edit', $jenis) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.jenis-dokumen.destroy', $jenis) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
                            <td colspan="7" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $jenisDokumen->links() }}
        </div>
    </div>
@endsection
