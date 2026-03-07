@extends('layouts.admin')

@section('title', __('admin.contacts'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.contacts') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.contacts') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header">{{ __('admin.contact_list') }}</div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('admin.search') }}..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">{{ __('admin.all_status') }}</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>{{ __('admin.unread') }}</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>{{ __('admin.read') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i>{{ __('admin.filter') }}
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.email') }}</th>
                            <th>{{ __('admin.subject') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.date') }}</th>
                            <th width="120">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kontaks as $index => $kontak)
                        <tr class="{{ !$kontak->is_read ? 'table-light fw-bold' : '' }}">
                            <td>{{ $kontaks->firstItem() + $index }}</td>
                            <td>
                                @if(!$kontak->is_read)
                                <i class="bi bi-circle-fill text-primary me-1" style="font-size: 8px;"></i>
                                @endif
                                {{ $kontak->nama }}
                            </td>
                            <td>{{ $kontak->email }}</td>
                            <td>{{ Str::limit($kontak->subjek, 40) }}</td>
                            <td>
                                @if($kontak->is_read)
                                <span class="badge bg-secondary">{{ __('admin.read') }}</span>
                                @else
                                <span class="badge bg-primary">{{ __('admin.unread') }}</span>
                                @endif
                            </td>
                            <td>{{ $kontak->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.kontak.show', $kontak) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.kontak.destroy', $kontak) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
            {{ $kontaks->links() }}
        </div>
    </div>
@endsection
