@extends('layouts.frontend')

@section('title', __('menu.contact'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ __('menu.contact') }}</h1>
            <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('menu.home') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('menu.contact') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Contact Info -->
                <div class="col-lg-4 mb-4 mb-lg-0" data-aos="fade-right">
                    <div class="sidebar-widget h-100">
                        <h5>{{ __('contact.info_title') }}</h5>
                        
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-geo-alt fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ __('contact.address') }}</h6>
                                <p class="mb-0 text-muted">Jl. Kampus No. 1, Kota, Indonesia 12345</p>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-telephone fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ __('contact.phone') }}</h6>
                                <p class="mb-0 text-muted">(021) 1234567</p>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-envelope fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ __('contact.email') }}</h6>
                                <p class="mb-0 text-muted">lpm@kampus.ac.id</p>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-clock fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ __('contact.office_hours') }}</h6>
                                <p class="mb-0 text-muted">{{ __('contact.hours_detail') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-8" data-aos="fade-left">
                    <div class="sidebar-widget">
                        <h5>{{ __('contact.send_message') }}</h5>

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <form action="{{ route('kontak.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama" class="form-label">{{ __('contact.name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                                    @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">{{ __('contact.email') }} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telepon" class="form-label">{{ __('contact.phone') }}</label>
                                    <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon') }}">
                                    @error('telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subjek" class="form-label">{{ __('contact.subject') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subjek') is-invalid @enderror" id="subjek" name="subjek" value="{{ old('subjek') }}" required>
                                    @error('subjek')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="pesan" class="form-label">{{ __('contact.message') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('pesan') is-invalid @enderror" id="pesan" name="pesan" rows="5" required>{{ old('pesan') }}</textarea>
                                @error('pesan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-2"></i>{{ __('buttons.send') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Map -->
            <div class="mt-5" data-aos="fade-up">
                <div class="rounded-4 overflow-hidden shadow-sm">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.666195177992!2d106.82706831476926!3d-6.175110395529257!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTAnMzAuNCJTIDEwNsKwNDknNDYuMiJF!5e0!3m2!1sen!2sid!4v1634567890123!5m2!1sen!2sid" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>
@endsection