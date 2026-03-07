@extends('layouts.frontend')

@section('title', __('menu.documents'))

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
            <!-- Search -->
            <div class="row mb-4" data-aos="fade-up">
                <div class="col-md-6">
                    <form action="{{ route('dokumen.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="{{ __('labels.search_documents') }}" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                    </form>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    @if($kategoris->count() > 0)
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            {{ request('kategori') ?: __('labels.all_categories') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dokumen.index') }}">{{ __('labels.all_categories') }}</a></li>
                            @foreach($kategoris as $kategori)
                            <li><a class="dropdown-item" href="{{ route('dokumen.index', ['kategori' => $kategori]) }}">{{ $kategori }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Documents List -->
            <div class="table-responsive bg-white rounded-4 shadow-sm" data-aos="fade-up">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('labels.document_name') }}</th>
                            <th style="width: 120px;">{{ __('labels.type') }}</th>
                            <th style="width: 100px;">{{ __('labels.size') }}</th>
                            <th style="width: 100px;">{{ __('labels.downloads') }}</th>
                            <th style="width: 120px;">{{ __('labels.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumen as $index => $item)
                        <tr>
                            <td>{{ $dokumen->firstItem() + $index }}</td>
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
                            <td>{{ $item->formatted_size }}</td>
                            <td>{{ $item->download_count }}</td>
                            <td>
                                <a href="{{ route('dokumen.download', $item->slug) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-download"></i> {{ __('buttons.download') }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-folder2-open fs-1 text-muted d-block mb-2"></i>
                                {{ __('messages.no_documents') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $dokumen->links() }}
            </div>
        </div>
    </section>
@endsection