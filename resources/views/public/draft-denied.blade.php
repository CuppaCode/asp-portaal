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

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border-left: 4px solid #ffc107;
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
        <h3><i class="fa fa-times-circle"></i> Claim Afgewezen</h3>
    </div>
    <div class="claim-form-body card-body">
        <h4 class="mb-4">De concept claim is afgewezen</h4>
        
        <div class="alert alert-danger">
            <p class="mb-2"><strong>Claim nummer:</strong> {{ $claim->claim_number }}</p>
            <p class="mb-0">Deze claim is afgewezen en wordt niet verder verwerkt.</p>
        </div>
        
        @if($claim->denied_reason)
            <div class="alert alert-warning">
                <strong>Reden:</strong><br>
                {{ $claim->denied_reason }}
            </div>
        @endif
    </div>
</div>
@endsection
