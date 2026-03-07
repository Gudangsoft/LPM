@extends('layouts.frontend')

@section('title', $halaman->judul ?? __('menu.profile'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ $halaman->judul ?? __('menu.profile') }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ $halaman->judul ?? __('menu.profile') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="bg-white rounded-4 shadow-sm p-4 p-lg-5" data-aos="fade-up">
                        @if($halaman)
                            <div class="content">
                                {!! $halaman->getLocalizedKonten() !!}
                            </div>
                        @else
                            <div class="alert alert-info">
                                {{ __('messages.page_not_found') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection