@extends('layouts.admin')

@section('title', 'Instrumen Audit')

@section('content')
    <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="page-title">Instrumen Audit</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">SPMI</li>
                    <li class="breadcrumb-item active">Instrumen Audit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.ami.instrumen.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Butir
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small mb-1">Standar Mutu</label>
                    <select name="standar_mutu_id" class="form-select form-select-sm">
                        <option value="">Semua Standar</option>
                        @foreach($standarMutus as $s)
                        <option value="{{ $s->id }}" {{ request('standar_mutu_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->kode ? $s->kode.' — ' : '' }}{{ $s->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Cari pertanyaan</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                    <a href="{{ route('admin.ami.instrumen.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                            <th width="70">Kode</th>
                            <th>Pertanyaan / Indikator</th>
                            <th>Standar</th>
                            <th width="70">Bobot</th>
                            <th width="110">Target</th>
                            <th width="120">Jenis Bukti</th>
                            <th width="80">Status</th>
                            <th width="110">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($butir as $b)
                        <tr>
                            <td>{{ $b->kode ?? '-' }}</td>
                            <td>
                                <div>{{ $b->pertanyaan }}</div>
                                @if($b->indikator)
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($b->indikator, 120) }}</small>
                                @endif
                            </td>
                            <td><small>{{ $b->standarMutu->kode ? $b->standarMutu->kode.' — ' : '' }}{{ $b->standarMutu->nama }}</small></td>
                            <td>{{ rtrim(rtrim(number_format($b->bobot, 2), '0'), '.') }}</td>
                            <td>{{ $b->target ?? '-' }}</td>
                            <td>{{ $b->jenis_bukti ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $b->is_active ? 'success' : 'secondary' }}">
                                    {{ $b->is_active ? __('admin.active') : __('admin.inactive') }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.ami.instrumen.edit', $b) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.ami.instrumen.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada butir instrumen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($butir->hasPages())
        <div class="card-footer">{{ $butir->links() }}</div>
        @endif
    </div>
@endsection
