@extends('layouts.admin')

@section('title', 'Tambah Butir Instrumen')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tambah Butir Instrumen</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.instrumen.index') }}">Instrumen Audit</a></li>
                <li class="breadcrumb-item active">{{ __('admin.add_new') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">Form Butir Instrumen</div>
                <div class="card-body">
                    <form action="{{ route('admin.ami.instrumen.store') }}" method="POST">
                        @include('admin.ami.instrumen._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
