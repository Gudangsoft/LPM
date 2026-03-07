@extends('layouts.frontend')

@section('title', $halaman->judul)
@section('meta_description', $halaman->meta_description ?: Str::limit(strip_tags($halaman->konten), 160))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ $halaman->judul }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ $halaman->judul }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="bg-white rounded-4 shadow-sm p-4 p-lg-5" data-aos="fade-up">
                        <div class="content">
                            {!! $halaman->getLocalizedKonten() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection