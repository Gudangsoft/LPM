@extends('layouts.admin')

@section('title', 'DKPS')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Data Kinerja Program Studi (DKPS)</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">DKPS</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.dkps.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Draft Baru
        </a>
    </div>

    <div class="card">
        <div class="card-header">Daftar Draft DKPS</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Program Studi</th>
                            <th>Tahun Akademik (TS)</th>
                            <th>Nama Pengusul</th>
                            <th>{{ __('admin.status') }}</th>
                            <th width="150">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                        <tr>
                            <td>{{ $submission->prodi->full_name ?? '-' }}</td>
                            <td>{{ $submission->tahun_ajaran }}</td>
                            <td>{{ $submission->nama_pengusul ?? '-' }}</td>
                            <td><span class="badge bg-{{ $submission->status_color }}">{{ ucfirst($submission->status) }}</span></td>
                            <td>
                                <a href="{{ route('admin.dkps.show', $submission) }}" class="btn btn-sm btn-primary" title="Isi Data">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.dkps.destroy', $submission) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
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
        </div>
        @if($submissions->hasPages())
        <div class="card-footer">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
@endsection
