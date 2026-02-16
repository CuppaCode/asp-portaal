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
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
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
    padding: 2rem 1.5rem;
}

.form-control {
    border: 1.5px solid #e1e8ed;
    border-radius: 6px;
    padding: 0.625rem 0.875rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.15rem rgba(255, 193, 7, 0.15);
}

textarea.form-control {
    resize: vertical;
}

label {
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.required-field::after {
    content: " *";
    color: #e74c3c;
    font-weight: 600;
}

.form-text {
    color: #6c757d;
    font-size: 0.85rem;
}

.alert {
    border-radius: 6px;
    border: none;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
}

.alert-info {
    background-color: #e7f3ff;
    color: #0056b3;
    border-left: 4px solid #007bff;
}

.alert-danger {
    background-color: #fee;
    color: #c33;
    border-left: 4px solid #dc3545;
}

.btn {
    border-radius: 6px;
    font-weight: 600;
    padding: 0.875rem 2rem;
    transition: all 0.2s ease;
    border: none;
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.25);
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.35);
}

.btn-secondary {
    background: #6c757d;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
}

@media (max-width: 767px) {
    .claim-form-header {
        padding: 1.5rem 1rem;
    }
    
    .claim-form-header h3 {
        font-size: 1.5rem;
    }
    
    .claim-form-body {
        padding: 1.5rem 1rem;
    }
}
</style>
@endsection

@section('content')
<div class="claim-form-card card">
    <div class="claim-form-header card-header">
        <h3><i class="fa fa-times-circle"></i> Claim Afwijzen</h3>
    </div>
    <div class="claim-form-body card-body">
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
