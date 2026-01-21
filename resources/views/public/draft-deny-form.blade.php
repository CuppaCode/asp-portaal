@extends('layouts.public')

@section('content')
<div class="card">
    <div class="card-header bg-warning text-dark">
        <h3 class="mb-0"><i class="fa fa-times-circle"></i> Claim Afwijzen</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <p class="mb-2"><strong>Claim nummer:</strong> {{ $claim->claim_number }}</p>
            <p class="mb-0"><strong>Onderwerp:</strong> {{ $claim->subject }}</p>
        </div>
        
        <form action="{{ route('draft-claim.deny', $claim->id) }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="form-group">
                <label for="reason" class="required-field">Reden voor afwijzing</label>
                <textarea name="reason" id="reason" class="form-control" rows="5" required 
                    placeholder="Geef aan waarom deze claim wordt afgewezen...">{{ old('reason') }}</textarea>
                <small class="form-text text-muted">Minimaal 10 karakters vereist</small>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-danger btn-lg btn-block">
                    <i class="fa fa-times"></i> Claim Afwijzen
                </button>
                <a href="javascript:history.back()" class="btn btn-secondary btn-block">Annuleren</a>
            </div>
        </form>
    </div>
</div>
@endsection
