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

.alert-info {
    background-color: #e7f3ff;
    color: #0056b3;
    border-left: 4px solid #007bff;
}

.text-muted {
    color: #6c757d !important;
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
        <h3><i class="fa fa-check-circle"></i> {{ isset($isComplaint) && $isComplaint ? 'Klacht Ingediend' : 'Schademelding Ingediend' }}</h3>
    </div>
    <div class="claim-form-body card-body">
        @if(isset($isComplaint) && $isComplaint)
            <h4 class="mb-4">Uw klacht is succesvol ingediend!</h4>
            
            <div class="alert alert-info">
                <p class="mb-0"><strong>Bedrijf:</strong> {{ $company->name }}</p>
            </div>
            
            <p class="text-muted">
                Uw klacht is ter informatie verstuurd naar de verantwoordelijke medewerkers. U ontvangt zo spoedig mogelijk een reactie.
            </p>
        @else
            <h4 class="mb-4">Uw schademelding is succesvol ingediend!</h4>
            
            <div class="alert alert-info">
                <p class="mb-2"><strong>Claim nummer:</strong> {{ $claim->claim_number }}</p>
                <p class="mb-0"><strong>Bedrijf:</strong> {{ $company->name }}</p>
            </div>
            
            <p class="text-muted">
                Uw melding wordt binnenkort beoordeeld. U ontvangt een bevestiging zodra de melding is goedgekeurd.
            </p>
            
            @if($claim->draft_expires_at)
            <p class="text-muted">
                <small>Deze melding is geldig tot {{ $claim->draft_expires_at->format('d-m-Y H:i') }}</small>
            </p>
            @endif
        @endif
    </div>
</div>
@endsection
