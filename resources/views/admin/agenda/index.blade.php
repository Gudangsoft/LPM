@extends('layouts.admin')

@section('title', __('admin.agenda'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.agenda') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.agenda') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.agenda_list') }}</span>
            <a href="{{ route('admin.agenda.create') }}" class="btn btn-primary btn-sm">
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
                            <th>{{ __('admin.date_time') }}</th>
                            <th>{{ __('admin.location') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th width="150">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agendas as $index => $agenda)
                        <tr>
                            <td>{{ $agendas->firstItem() + $index }}</td>
                            <td><strong>{{ Str::limit($agenda->judul, 50) }}</strong></td>
                            <td>
                                <i class="bi bi-calendar me-1 text-muted"></i>{{ $agenda->tanggal_mulai->format('d M Y') }}
                                @if($agenda->tanggal_selesai && $agenda->tanggal_mulai->format('Y-m-d') != $agenda->tanggal_selesai->format('Y-m-d'))
                                <br><small class="text-muted">s/d {{ $agenda->tanggal_selesai->format('d M Y') }}</small>
                                @endif
                            </td>
                            <td>
                                @if($agenda->lokasi)
                                <i class="bi bi-geo-alt me-1 text-muted"></i>{{ Str::limit($agenda->lokasi, 30) }}
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                @if($agenda->is_active)
                                <span class="badge bg-success">{{ __('admin.published') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('admin.draft') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('agenda.show', $agenda->slug) }}" class="btn btn-sm btn-info" target="_blank" title="{{ __('admin.view') }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.agenda.edit', $agenda) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.agenda.destroy', $agenda) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
            {{ $agendas->links() }}
        </div>
    </div>
@endsection
