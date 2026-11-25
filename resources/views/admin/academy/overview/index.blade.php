@extends('layouts.admin')
@section('content')

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'todo' ? 'active' : '' }}" href="{{ route('admin.academy.overview') }}">Nu te doen</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'bewaken' ? 'active' : '' }}" href="{{ route('admin.academy.overview', ['tab' => 'bewaken']) }}">Bewaken</a>
    </li>
</ul>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

@if($tab === 'todo')
    @include('admin.academy.overview._todo')
@else
    @include('admin.academy.overview._monitoring')
@endif

@endsection
