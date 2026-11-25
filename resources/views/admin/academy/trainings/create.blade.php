@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header"><strong>Nieuw trainingsdossier</strong></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.academy.trainings.store') }}">
            @csrf

            @include('admin.academy.trainings._form', ['training' => null])

            <h5 class="mt-3">Deelnemers</h5>
            @if(!$companyId)
                <p class="text-muted">Kies eerst een werkgever om de chauffeurs te zien.</p>
            @elseif($drivers->isEmpty())
                <p class="text-muted">Deze werkgever heeft nog geen chauffeurs. Je kunt deelnemers ook later in het dossier toevoegen, met "+ Nieuwe deelnemer".</p>
            @else
                <div class="row">
                    @foreach($drivers as $driver)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="drivers[]" value="{{ $driver->id }}" id="driver_{{ $driver->id }}" {{ in_array($driver->id, old('drivers', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="driver_{{ $driver->id }}">
                                    {{ trim(($driver->contact?->first_name ?? '') . ' ' . ($driver->contact?->last_name ?? '')) }}
                                    @if(empty($driver->contact?->email))<span class="text-muted small">(geen e-mail)</span>@endif
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4">
                <button class="btn btn-primary" type="submit">Opslaan</button>
                <a class="btn btn-default" href="{{ route('admin.academy.trainings.index') }}">Annuleren</a>
            </div>
        </form>
    </div>
</div>

@endsection
