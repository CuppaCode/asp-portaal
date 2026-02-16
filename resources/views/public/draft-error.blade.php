@extends('layouts.public')

@section('styles')
<style>
.claim-form-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: none;
    overflow: hidden;
}

.claim-form-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    padding: 2rem 1.5rem;
    border-bottom: none;
}

.claim-form-header h3 {
    color: white;
    font-size: 1.75rem;
    font-weight: 600;
    margin: 0;
    text-align: center;
}

.claim-form-body {
    padding: 3rem 2rem;
    text-align: center;
}

.alert {
    border-radius: 6px;
    border: none;
    padding: 1rem 1.25rem;
    margin: 1.5rem auto;
    max-width: 500px;
}

.alert-danger {
    background-color: #fee;
    color: #c33;
    border-left: 4px solid #dc3545;
}

@media (max-width: 767px) {
    .claim-form-header {
        padding: 1.5rem 1rem;
    }
    
    .claim-form-header h3 {
        font-size: 1.5rem;
    }
    
    .claim-form-body {
        padding: 2rem 1rem;
    }
}
</style>
@endsection

@section('content')
<div class="claim-form-card card">
    <div class="claim-form-header card-header">
        <h3><i class="fa fa-exclamation-triangle"></i> Fout</h3>
    </div>
    <div class="claim-form-body card-body">
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
