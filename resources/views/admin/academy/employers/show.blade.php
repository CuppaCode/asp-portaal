@extends('layouts.admin')
@section('content')

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong>{{ $company->name }}</strong>
            <span class="text-muted">· Academy-werkgever sinds {{ $company->academy_started_at?->format('d-m-Y') ?? 'onbekend' }}</span>
        </div>
        <a class="btn btn-default btn-sm" href="{{ route('admin.academy.employers.index') }}">{{ trans('global.back') }}</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="text-muted small text-uppercase">Contactpersoon</div>
                @if($company->contactPerson)
                    <div>{{ trim($company->contactPerson->first_name . ' ' . $company->contactPerson->last_name) }}</div>
                    <div>{{ $company->contactPerson->email }} @if($company->contactPerson->phone) · {{ $company->contactPerson->phone }} @endif</div>
                @else
                    <div class="text-muted">Geen contactpersoon gekoppeld. @can('company_edit')<a href="{{ route('admin.companies.edit', $company->id) }}">Bedrijf aanpassen</a>@endcan</div>
                @endif
                @if($company->phone)<div>Telefoon bedrijf: {{ $company->phone }}</div>@endif
            </div>
            <div class="col-md-6">
                @can('academy_manage')
                    <form method="POST" action="{{ route('admin.academy.employers.toggle', $company->id) }}" class="form-inline mb-2">
                        @csrf
                        <label class="mr-2 small" for="academy_started_at">Startdatum</label>
                        <input class="form-control form-control-sm mr-2" type="date" name="academy_started_at" id="academy_started_at" value="{{ $company->academy_started_at?->format('Y-m-d') }}" required>
                        <button class="btn btn-sm btn-outline-primary" type="submit">Opslaan</button>
                    </form>
                    <form method="POST" action="{{ route('admin.academy.employers.toggle', $company->id) }}" onsubmit="return confirm('Werkgever uit de Academy halen? De gegevens blijven bewaard.');">
                        @csrf
                        <input type="hidden" name="disable" value="1">
                        <button class="btn btn-sm btn-link text-danger p-0" type="submit">Uit de Academy halen</button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

@foreach([1, 2, 3] as $number)
    @php $step = $steps[$number]; @endphp
    @if($number === 2)
        @include('admin.academy.partials.step-card', [
            'step' => $step,
            'mailContext' => $mailContext,
            'subjectId' => $company->id,
            'extra' => $step['state'] === 'locked'
                ? '<p class="text-muted">Wacht: rond eerst stap 1 af.</p>'
                : view('admin.academy.employers._drivers', compact('driverRows', 'formerDrivers', 'company', 'stateLabels')),
        ])
    @else
        @include('admin.academy.partials.step-card', ['step' => $step, 'mailContext' => $mailContext, 'subjectId' => $company->id])
    @endif
@endforeach

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Trainingsdossiers</strong>
        @can('academy_manage')
            <a class="btn btn-sm btn-primary" href="{{ route('admin.academy.trainings.create', ['company_id' => $company->id]) }}">+ Training</a>
        @endcan
    </div>
    <div class="card-body">
        @forelse($trainings as $training)
            <div class="border-bottom py-1">
                <a href="{{ route('admin.academy.trainings.show', $training->id) }}">{{ $training->name }}</a>
                · {{ $training->training_date->format('d-m-Y') }}
                @if($training->isClosed())<span class="badge badge-secondary">gesloten</span>@else<span class="badge badge-primary">open</span>@endif
            </div>
        @empty
            <span class="text-muted">Nog geen trainingen.</span>
        @endforelse
    </div>
</div>

@endsection
