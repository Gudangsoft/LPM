@extends('layouts.admin')

@section('title', 'Standar Mutu')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Standar Mutu</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Standar Mutu</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.standar-mutu.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_new') }}
        </a>
    </div>

    <div class="card">
        <div class="card-header">Daftar Standar Mutu</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">{{ __('admin.order') }}</th>
                            <th width="80">Kode</th>
                            <th>Nama</th>
                            <th width="100">Temuan</th>
                            <th width="100">Dokumen</th>
                            <th width="90">{{ __('admin.status') }}</th>
                            <th width="120">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($standarMutus as $standar)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $standar->urutan }}</span></td>
                            <td>{{ $standar->kode ?? '-' }}</td>
                            <td>{{ $standar->nama }}</td>
                            <td><span class="badge bg-info">{{ $standar->temuan_count }}</span></td>
                            <td><span class="badge bg-info">{{ $standar->dokumen_count }}</span></td>
                            <td>
                                @if($standar->is_active)
                                <span class="badge bg-success">{{ __('admin.active') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('admin.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.ami.standar-mutu.edit', $standar) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.ami.standar-mutu.destroy', $standar) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
        </div>
        @if($standarMutus->hasPages())
        <div class="card-footer">
            {{ $standarMutus->links() }}
        </div>
        @endif
    </div>
@endsection
