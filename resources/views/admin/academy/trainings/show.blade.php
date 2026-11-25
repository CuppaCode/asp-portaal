@extends('layouts.admin')
@section('content')

@php
    $step6Open = $steps[6]['state'] !== 'locked';
@endphp

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ $training->name }}</strong>
            <span class="text-muted">· {{ $training->company?->name }} · {{ $training->training_date->format('d-m-Y') }}</span>
            @if($training->isClosed())<span class="badge badge-secondary">gesloten</span>@endif
            @if($training->is_prevention)<span class="badge badge-warning">schadepreventie</span>@endif
        </div>
        <a class="btn btn-default btn-sm" href="{{ route('admin.academy.trainings.index') }}">{{ trans('global.back') }}</a>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="row small mb-2">
            <div class="col-md-3"><span class="text-muted text-uppercase">Locatie</span><div>{{ $training->location ?: '-' }}</div></div>
            <div class="col-md-3"><span class="text-muted text-uppercase">Opleider</span><div>{{ \App\Models\AcademyTraining::PROVIDER_SELECT[$training->provider_type] ?? '' }} {{ $training->provider_name }} {{ $training->provider_email }}</div></div>
            <div class="col-md-3"><span class="text-muted text-uppercase">CBR-code</span><div>{{ $training->cbr_code ?: '-' }}</div></div>
            <div class="col-md-3">
                <span class="text-muted text-uppercase">Code 95</span>
                <div>
                    @if($training->hasCode95Module())
                        {{ $training->code95_hours }} uur {{ \App\Models\AcademyTraining::CODE95_TYPE_SELECT[$training->code95_type] ?? '' }}
                    @else
                        geen Code 95-uren
                    @endif
                </div>
            </div>
        </div>

        @can('academy_manage')
            @unless($training->isClosed())
                <a class="small" data-toggle="collapse" href="#editTraining" role="button">Gegevens wijzigen</a>
                <div class="collapse mt-3" id="editTraining">
                    <form method="POST" action="{{ route('admin.academy.trainings.update', $training->id) }}">
                        @csrf
                        @method('PUT')
                        @include('admin.academy.trainings._form', ['training' => $training])
                        <button class="btn btn-sm btn-primary" type="submit">Opslaan</button>
                    </form>
                </div>
            @endunless
        @endcan
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><strong>Deelnemers ({{ $participants->count() }})</strong></div>
    <div class="card-body">

        @if($withoutBirthdate->isNotEmpty())
            <div class="alert alert-warning">
                Naam en geboortedatum zijn nodig voor M6. Geboortedatum ontbreekt bij:
                @foreach($withoutBirthdate as $participant)
                    <a href="{{ route('admin.academy.driver-cards.edit', ['driver' => $participant->driver_id, 'back_to' => url()->current()]) }}">{{ trim(($participant->driver?->contact?->first_name ?? '') . ' ' . ($participant->driver?->contact?->last_name ?? '')) }}</a>@if(!$loop->last), @endif
                @endforeach
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>E-mail</th>
                        <th>Geboortedatum</th>
                        <th>Aanwezig</th>
                        @if($training->hasCode95Module())<th>Uren in CBR</th>@endif
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participants as $participant)
                        @php $contact = $participant->driver?->contact; @endphp
                        <tr>
                            <td>
                                <a href="{{ route('admin.academy.driver-cards.edit', ['driver' => $participant->driver_id, 'back_to' => url()->current()]) }}">{{ trim(($contact?->first_name ?? '') . ' ' . ($contact?->last_name ?? '')) }}</a>
                            </td>
                            <td>{{ $contact?->email ?: 'geen' }}</td>
                            <td>{{ $contact?->birthdate ? \Carbon\Carbon::parse($contact->birthdate)->format('d-m-Y') : '-' }}</td>
                            <td class="text-nowrap">
                                @if($participant->attended === null)
                                    <span class="text-muted">nog niet vastgelegd</span>
                                @elseif($participant->attended)
                                    <span class="badge badge-success">aanwezig</span>
                                @else
                                    <span class="badge badge-secondary">afwezig</span>
                                @endif
                                @can('academy_manage')
                                    @if($step6Open && !$training->isClosed())
                                        <form method="POST" action="{{ route('admin.academy.trainings.participants.update', [$training->id, $participant->id]) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-xs btn-outline-success" name="attended" value="1" type="submit">aanwezig</button>
                                            <button class="btn btn-xs btn-outline-secondary" name="attended" value="0" type="submit">afwezig</button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                            @if($training->hasCode95Module())
                                <td class="text-nowrap">
                                    @if($participant->cbr_confirmed)
                                        <span class="badge badge-success">bevestigd, op de kaart</span>
                                        @can('academy_manage')
                                            @unless($training->isClosed())
                                                <form method="POST" action="{{ route('admin.academy.trainings.participants.update', [$training->id, $participant->id]) }}" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-xs btn-link text-muted" name="confirm_hours" value="0" type="submit">ongedaan maken</button>
                                                </form>
                                            @endunless
                                        @endcan
                                    @elseif($participant->attended)
                                        @can('academy_manage')
                                            @unless($training->isClosed())
                                                <form method="POST" action="{{ route('admin.academy.trainings.participants.update', [$training->id, $participant->id]) }}" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-xs btn-outline-primary" name="confirm_hours" value="1" type="submit" onclick="return confirm('Staan de uren al in het CBR-register? Zet geen uren op de kaart die er nog niet staan.');">Uren staan in CBR</button>
                                                </form>
                                            @endunless
                                        @endcan
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            @endif
                            <td>
                                @can('academy_manage')
                                    @unless($training->isClosed())
                                        <form method="POST" action="{{ route('admin.academy.trainings.participants.destroy', [$training->id, $participant->id]) }}" onsubmit="return confirm('Deelnemer verwijderen uit dit dossier?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-outline-danger" type="submit">Verwijderen</button>
                                        </form>
                                    @endunless
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">Nog geen deelnemers.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @can('academy_manage')
            @unless($training->isClosed())
                @if($available->isNotEmpty())
                    <form method="POST" action="{{ route('admin.academy.trainings.participants.store', $training->id) }}" class="border rounded p-2 mb-3">
                        @csrf
                        <strong class="small">Deelnemers toevoegen</strong>
                        <div class="row">
                            @foreach($available as $driver)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="drivers[]" value="{{ $driver->id }}" id="add_{{ $driver->id }}">
                                        <label class="form-check-label" for="add_{{ $driver->id }}">{{ trim(($driver->contact?->first_name ?? '') . ' ' . ($driver->contact?->last_name ?? '')) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="btn btn-sm btn-primary mt-2" type="submit">Toevoegen</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.academy.trainings.participants.new', $training->id) }}" class="border rounded p-2">
                    @csrf
                    <strong class="small">+ Nieuwe deelnemer (nog niet bekend bij de werkgever)</strong>
                    <div class="form-row mt-1">
                        <div class="col-md-2"><input class="form-control form-control-sm" type="text" name="first_name" placeholder="Voornaam" required></div>
                        <div class="col-md-2"><input class="form-control form-control-sm" type="text" name="last_name" placeholder="Achternaam" required></div>
                        <div class="col-md-2"><input class="form-control form-control-sm" type="date" name="birthdate" title="Geboortedatum" required></div>
                        <div class="col-md-3"><input class="form-control form-control-sm" type="email" name="email" placeholder="E-mail"></div>
                        <div class="col-md-2"><input class="form-control form-control-sm" type="text" name="function" placeholder="Functie"></div>
                        <div class="col-md-1"><button class="btn btn-sm btn-primary" type="submit">Opslaan</button></div>
                    </div>
                </form>
            @endunless
        @endcan
    </div>
</div>

@foreach([4, 5, 6] as $number)
    @include('admin.academy.partials.step-card', ['step' => $steps[$number], 'mailContext' => $mailContext, 'subjectId' => $training->id])
@endforeach

@can('academy_manage')
    @unless($training->isClosed())
        <div class="card mb-3">
            <div class="card-body">
                @if(!empty($closeBlockers))
                    <div class="small text-muted mb-2">Nog open: {{ count($closeBlockers) }} regel(s). Sluiten kan als alles is afgevinkt of n.v.t. is.</div>
                @endif
                <form method="POST" action="{{ route('admin.academy.trainings.close', $training->id) }}" onsubmit="return confirm('Dossier sluiten?');">
                    @csrf
                    <button class="btn btn-success" type="submit" {{ empty($closeBlockers) ? '' : 'disabled' }}>Dossier sluiten</button>
                </form>
            </div>
        </div>
    @endunless
@endcan

@endsection
