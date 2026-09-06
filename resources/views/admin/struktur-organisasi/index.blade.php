@extends('layouts.admin')

@section('title', __('admin.organizational_structure'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('admin.organizational_structure') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ __('admin.organizational_structure') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('admin.structure_list') }}</span>
            <a href="{{ route('admin.struktur-organisasi.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>{{ __('admin.add_new') }}
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="80">{{ __('admin.photo') }}</th>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.position') }}</th>
                            <th>{{ __('admin.order') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th width="150">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($strukturs as $struktur)
                        <tr>
                            <td>
                                @if($struktur->foto)
                                <img src="{{ Storage::url($struktur->foto) }}" alt="" class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                                @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $struktur->nama }}</strong>
                                @if($struktur->nip)
                                <br><small class="text-muted">NIP: {{ $struktur->nip }}</small>
                                @endif
                            </td>
                            <td>{{ $struktur->jabatan }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $struktur->urutan }}</span>
                            </td>
                            <td>
                                @if($struktur->is_active)
                                <span class="badge bg-success">{{ __('admin.active') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('admin.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.struktur-organisasi.edit', $struktur) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.struktur-organisasi.destroy', $struktur) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
            {{ $strukturs->links() }}
        </div>
    </div>
@endsection
