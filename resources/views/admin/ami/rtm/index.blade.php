@extends('layouts.admin')

@section('title', 'Rapat Tinjauan Manajemen')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Rapat Tinjauan Manajemen (RTM)</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">SPMI</li>
                    <li class="breadcrumb-item active">RTM</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.rtm.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Buat RTM</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <select name="periode_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Periode</option>
                        @foreach($periodes as $p)
                        <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Periode</th>
                            <th width="130">Tanggal</th>
                            <th width="110">Keputusan</th>
                            <th width="90">Status</th>
                            <th width="120">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rtm as $r)
                        <tr>
                            <td><a href="{{ route('admin.ami.rtm.show', $r) }}">{{ $r->judul }}</a></td>
                            <td><small>{{ optional($r->periodeAmi)->nama ?? '-' }}</small></td>
                            <td><small>{{ optional($r->tanggal)->format('d M Y') ?? '-' }}</small></td>
                            <td>{{ $r->keputusan_count }}</td>
                            <td><span class="badge bg-{{ $r->status_color }}">{{ $r->status }}</span></td>
                            <td>
                                <a href="{{ route('admin.ami.rtm.show', $r) }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.ami.rtm.edit', $r) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.ami.rtm.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada RTM.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($rtm->hasPages())<div class="card-footer">{{ $rtm->links() }}</div>@endif
    </div>
@endsection
