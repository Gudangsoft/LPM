@extends('layouts.admin')

@section('title', __('admin.database'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.database') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.database') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <!-- Backup Section -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-cloud-arrow-up me-2"></i>{{ __('admin.create_backup') }}
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('admin.backup_description') }}</p>
                    <form action="{{ route('admin.database.backup') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100" onclick="return confirm('{{ __('admin.confirm_backup') }}')">
                            <i class="bi bi-download me-2"></i>{{ __('admin.backup_now') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <i class="bi bi-upload me-2"></i>{{ __('admin.upload_backup') }}
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('admin.upload_description') }}</p>
                    <form action="{{ route('admin.database.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" class="form-control" name="backup_file" accept=".sql,.txt" required>
                            <small class="text-muted">{{ __('admin.allowed_extensions') }}: .sql, .txt (Max: 50MB)</small>
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-cloud-upload me-2"></i>{{ __('admin.upload_file') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ __('admin.warning') }}
                </div>
                <div class="card-body">
                    <p class="mb-0 small">{{ __('admin.restore_warning') }}</p>
                </div>
            </div>
        </div>

        <!-- Backup List Section -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-archive me-2"></i>{{ __('admin.backup_list') }}</span>
                    <span class="badge bg-secondary">{{ count($backups) }} {{ __('admin.files') }}</span>
                </div>
                <div class="card-body">
                    @if(count($backups) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('admin.filename') }}</th>
                                        <th>{{ __('admin.size') }}</th>
                                        <th>{{ __('admin.date') }}</th>
                                        <th class="text-center">{{ __('admin.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($backups as $backup)
                                        <tr>
                                            <td>
                                                <i class="bi bi-file-earmark-code text-primary me-2"></i>
                                                {{ $backup['filename'] }}
                                            </td>
                                            <td>{{ $backup['size'] }}</td>
                                            <td>{{ $backup['date'] }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('admin.database.download', $backup['filename']) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="{{ __('admin.download') }}">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <form action="{{ route('admin.database.restore', $backup['filename']) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('{{ __('admin.confirm_restore') }}')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="{{ __('admin.restore') }}">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.database.destroy', $backup['filename']) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('admin.delete') }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted mt-3">{{ __('admin.no_backups') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
