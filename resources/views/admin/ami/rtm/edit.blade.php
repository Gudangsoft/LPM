@extends('layouts.admin')

@section('title', 'Edit RTM')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit RTM</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.ami.rtm.index') }}">RTM</a></li>
                <li class="breadcrumb-item active">{{ __('admin.edit') }}</li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center"><div class="col-lg-9">
        <div class="card"><div class="card-body">
            <form action="{{ route('admin.ami.rtm.update', $rtm) }}" method="POST">
                @method('PUT')
                @include('admin.ami.rtm._form')
            </form>
        </div></div>
    </div></div>
@endsection
