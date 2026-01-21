@extends('layouts.public')

@section('content')
<div class="card">
    <div class="card-header bg-danger text-white">
        <h3 class="mb-0"><i class="fa fa-times-circle"></i> Claim Afgewezen</h3>
    </div>
    <div class="card-body text-center py-5">
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
