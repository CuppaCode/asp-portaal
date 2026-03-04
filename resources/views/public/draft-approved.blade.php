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
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
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

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
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
        <h3><i class="fa fa-check-circle"></i> Claim Goedgekeurd</h3>
    </div>
    <div class="claim-form-body card-body">
        <h4 class="mb-4">De concept claim is succesvol goedgekeurd!</h4>
        
        <div class="alert alert-success">
            <p class="mb-2"><strong>Claim nummer:</strong> {{ $claim->claim_number }}</p>
            <p class="mb-0">De claim is omgezet naar een actieve schademelding.</p>
        </div>
        
        <p class="text-muted">
            De claim wordt nu verder verwerkt door het team.
        </p>
    </div>
</div>
@endsection
