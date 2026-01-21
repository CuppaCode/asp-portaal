@extends('layouts.public')

@section('content')
<div class="card">
    <div class="card-header bg-danger text-white">
        <h3 class="mb-0"><i class="fa fa-exclamation-triangle"></i> Fout</h3>
    </div>
    <div class="card-body text-center py-5">
        <h4 class="mb-4">Er is een probleem opgetreden</h4>
        
        <div class="alert alert-danger">
            {{ $message }}
        </div>
        
        <p class="text-muted">
            Neem contact op met de beheerder als dit probleem zich blijft voordoen.
        </p>
    </div>
</div>
@endsection
