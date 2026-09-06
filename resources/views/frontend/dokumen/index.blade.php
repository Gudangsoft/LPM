@extends('layouts.frontend')

@section('title', __('menu.documents'))

@push('styles')
<style>
    /* Keep the category dropdown above the table card (AOS transforms create
       a stacking context that would otherwise hide the open menu). */
    .dokumen-toolbar {
        position: relative;
        z-index: 20;
    }

    .dokumen-toolbar .dropdown-menu {
        max-height: 320px;
        overflow-y: auto;
        border: none;
        border-radius: 12px;
        padding: 6px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, .12);
    }

    .dokumen-toolbar .dropdown-item {
        border-radius: 8px;
        padding: 8px 14px;
        font-weight: 500;
    }

    .dokumen-toolbar .dropdown-item.active,
    .dokumen-toolbar .dropdown-item:active {
        background-color: var(--primary-color);
        color: #fff;
    }

    .dokumen-table-card {
        position: relative;
        z-index: 1;
        overflow: hidden;            /* clip content to the rounded corners */
    }

    .dokumen-table-card .table > thead th {
        border-bottom: 0;
        padding: 16px;
        font-size: .78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #64748b;
        white-space: nowrap;
    }

    .dokumen-table-card .table > tbody td {
        padding: 16px;
        vertical-align: middle;
    }

    .dokumen-table-card .table > tbody tr:last-child td {
        border-bottom: 0;
    }

    @media (max-width: 575.98px) {
        .dokumen-table-card { border-radius: 14px; }
    }
</style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ __('menu.documents') }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('menu.documents') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <!-- Search & Filter -->
            <div class="row g-3 mb-4 align-items-center dokumen-toolbar" data-aos="fade-up">
                <div class="col-md-6">
                    <form action="{{ route('dokumen.index') }}" method="GET" class="d-flex">
                        @if(request('kategori'))
                        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                        @endif
                        <input type="text" name="search" class="form-control me-2" placeholder="{{ __('labels.search_documents') }}" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary flex-shrink-0"><i class="bi bi-search"></i></button>
                    </form>
                </div>
                <div class="col-md-6 text-md-end">
                    @if($kategoris->count() > 0)
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-funnel me-1"></i>{{ request('kategori') ?: __('labels.all_categories') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-md-end">
                            <li>
                                <a class="dropdown-item {{ request('kategori') ? '' : 'active' }}" href="{{ route('dokumen.index', ['search' => request('search')]) }}">
                                    {{ __('labels.all_categories') }}
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            @foreach($kategoris as $kategori)
                            <li>
                                <a class="dropdown-item {{ request('kategori') === $kategori ? 'active' : '' }}" href="{{ route('dokumen.index', ['kategori' => $kategori, 'search' => request('search')]) }}">
                                    {{ $kategori }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Documents List -->
            <div class="dokumen-table-card bg-white rounded-4 shadow-sm" data-aos="fade-up">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>{{ __('labels.document_name') }}</th>
                                <th style="width: 120px;">{{ __('labels.type') }}</th>
                                <th style="width: 100px;">{{ __('labels.size') }}</th>
                                <th style="width: 110px;">{{ __('labels.downloads') }}</th>
                                <th style="width: 130px;" class="text-end">{{ __('labels.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dokumen as $index => $item)
                            <tr>
                                <td class="text-muted">{{ $dokumen->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->file_type == 'pdf')
                                        <i class="bi bi-file-pdf text-danger fs-4 me-2"></i>
                                        @elseif(in_array($item->file_type, ['doc', 'docx']))
                                        <i class="bi bi-file-word text-primary fs-4 me-2"></i>
                                        @elseif(in_array($item->file_type, ['xls', 'xlsx']))
                                        <i class="bi bi-file-excel text-success fs-4 me-2"></i>
                                        @elseif(in_array($item->file_type, ['ppt', 'pptx']))
                                        <i class="bi bi-file-ppt text-warning fs-4 me-2"></i>
                                        @else
                                        <i class="bi bi-file-earmark fs-4 me-2"></i>
                                        @endif
                                        <div>
                                            <strong>{{ $item->judul }}</strong>
                                            @if($item->deskripsi)
                                            <br><small class="text-muted">{{ Str::limit($item->deskripsi, 60) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary text-uppercase">{{ $item->file_type }}</span></td>
                                <td class="text-nowrap">{{ $item->formatted_size }}</td>
                                <td>{{ $item->download_count }}</td>
                                <td class="text-end">
                                    <a href="{{ route('dokumen.download', $item->slug) }}" class="btn btn-sm btn-primary text-nowrap">
                                        <i class="bi bi-download me-1"></i>{{ __('buttons.download') }}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-folder2-open fs-1 text-muted d-block mb-2"></i>
                                    {{ __('messages.no_documents') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($dokumen->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $dokumen->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </section>
@endsection
