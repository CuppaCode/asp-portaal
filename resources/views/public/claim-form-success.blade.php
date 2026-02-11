@extends('layouts.public')

@section('content')
<div class="card">
    <div class="card-header bg-success text-white">
        <h3 class="mb-0"><i class="fa fa-check-circle"></i> {{ isset($isComplaint) && $isComplaint ? 'Klacht Ingediend' : 'Schademelding Ingediend' }}</h3>
    </div>
    <div class="card-body text-center py-5">
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
