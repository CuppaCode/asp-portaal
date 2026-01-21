@extends('layouts.public')

@section('content')
<div class="card">
    <div class="card-header bg-success text-white">
        <h3 class="mb-0"><i class="fa fa-check-circle"></i> Claim Goedgekeurd</h3>
    </div>
    <div class="card-body text-center py-5">
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
