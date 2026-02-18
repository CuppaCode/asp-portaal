@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-bolt"></i> Email Triggers
        </h5>
        <small class="text-muted">Overzicht van alle beschikbare email triggers en gekoppelde templates</small>
    </div>
    <div class="card-body">
        @include('partials.super-admin.manage-triggers')
    </div>
</div>

@endsection
