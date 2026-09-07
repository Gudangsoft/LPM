@extends('layouts.admin')

@section('title', 'Audit Trail')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Audit Trail AMI</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">SPMI</li>
                <li class="breadcrumb-item active">Audit Trail</li>
            </ol>
        </nav>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Entitas</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($typeOptions as $class => $label)
                        <option value="{{ $class }}" {{ request('type') === $class ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Aksi</label>
                    <select name="event" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($eventOptions as $ev)
                        <option value="{{ $ev }}" {{ request('event') === $ev ? 'selected' : '' }}>{{ $ev }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Pengguna</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Dari</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Sampai</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-funnel"></i></button>
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
                            <th width="150">Waktu</th>
                            <th width="150">Pengguna</th>
                            <th width="120">Entitas</th>
                            <th width="110">Aksi</th>
                            <th>Perubahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trails as $t)
                        <tr>
                            <td><small>{{ $t->created_at->format('d M Y H:i') }}</small></td>
                            <td><small>{{ optional($t->user)->name ?? 'Sistem' }}</small></td>
                            <td><small>{{ $t->model_label }} #{{ $t->auditable_id }}</small></td>
                            <td><span class="badge bg-{{ $t->event_color }}">{{ $t->event_label }}</span></td>
                            <td>
                                @if($t->description)
                                    <small>{{ $t->description }}</small>
                                @elseif($t->changes)
                                    <small class="text-muted">
                                        @foreach($t->changes as $field => $pair)
                                            <span class="d-inline-block me-3">
                                                <code>{{ $field }}</code>:
                                                <span class="text-danger">{{ \Illuminate\Support\Str::limit((string) ($pair[0] ?? '∅'), 30) }}</span>
                                                &rarr;
                                                <span class="text-success">{{ \Illuminate\Support\Str::limit((string) ($pair[1] ?? '∅'), 30) }}</span>
                                            </span>
                                        @endforeach
                                    </small>
                                @else
                                    <small class="text-muted">—</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($trails->hasPages())
        <div class="card-footer">{{ $trails->links() }}</div>
        @endif
    </div>
@endsection
