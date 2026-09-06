@extends('layouts.admin')

@section('title', __('admin.documents'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.documents') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.documents') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.document_list') }}</span>
            <a href="{{ route('admin.dokumen.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_new') }}
            </a>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('admin.search') }}..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="jenis" class="form-select">
                        <option value="">-- {{ __('admin.select_document_type') }} --</option>
                        @foreach($jenisDokumen as $jenis)
                        <option value="{{ $jenis->id }}" {{ request('jenis') == $jenis->id ? 'selected' : '' }}>
                            {{ $jenis->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i>{{ __('admin.filter') }}
                    </button>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{ route('admin.jenis-dokumen.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-folder2 me-1"></i>{{ __('admin.document_types') }}
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>{{ __('admin.title') }}</th>
                            <th>{{ __('admin.document_types') }}</th>
                            <th>{{ __('admin.file_type') }}</th>
                            <th>{{ __('admin.file_size') }}</th>
                            <th>{{ __('admin.downloads') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.date') }}</th>
                            <th width="150">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumens as $index => $dokumen)
                        <tr>
                            <td>{{ $dokumens->firstItem() + $index }}</td>
                            <td>{{ $dokumen->judul }}</td>
                            <td>
                                @if($dokumen->jenisDokumen)
                                    <span class="badge bg-secondary">
                                        @if($dokumen->jenisDokumen->icon)
                                        <i class="bi {{ $dokumen->jenisDokumen->icon }} me-1"></i>
                                        @endif
                                        {{ $dokumen->jenisDokumen->nama }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $ext = pathinfo($dokumen->file_path, PATHINFO_EXTENSION);
                                    $icons = ['pdf' => 'bi-file-pdf text-danger', 'doc' => 'bi-file-word text-primary', 'docx' => 'bi-file-word text-primary', 'xls' => 'bi-file-excel text-success', 'xlsx' => 'bi-file-excel text-success'];
                                @endphp
                                <i class="bi {{ $icons[$ext] ?? 'bi-file-earmark' }} me-1"></i>{{ strtoupper($ext) }}
                            </td>
                            <td>{{ $dokumen->formatted_size }}</td>
                            <td><span class="badge bg-info">{{ $dokumen->download_count }}</span></td>
                            <td><span class="badge bg-{{ $dokumen->status_color }}">{{ ucfirst($dokumen->status) }}</span></td>
                            <td>{{ $dokumen->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.dokumen.show', $dokumen) }}" class="btn btn-sm btn-outline-secondary" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ Storage::url($dokumen->file_path) }}" class="btn btn-sm btn-info" target="_blank" title="{{ __('admin.download') }}">
                                    <i class="bi bi-download"></i>
                                </a>
                                <a href="{{ route('admin.dokumen.edit', $dokumen) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.dokumen.destroy', $dokumen) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
                            <td colspan="9" class="text-center text-muted py-4">{{ __('admin.no_data') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $dokumens->links() }}
        </div>
    </div>
@endsection
