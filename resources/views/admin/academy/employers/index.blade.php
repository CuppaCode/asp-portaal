@extends('layouts.admin')
@section('content')

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Academy · Werkgevers</strong>
    </div>
    <div class="card-body">

        @can('academy_manage')
            <form method="POST" action="{{ route('admin.academy.employers.store') }}" class="border rounded p-3 mb-4">
                @csrf
                <div class="form-row align-items-end">
                    <div class="form-group col-md-5 mb-0">
                        <label for="company_id">Bedrijf toevoegen aan de Academy</label>
                        <select class="form-control select2" name="company_id" id="company_id" required>
                            <option value="">Kies een bedrijf</option>
                            @foreach($availableCompanies as $available)
                                <option value="{{ $available->id }}">{{ $available->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3 mb-0">
                        <label for="academy_started_at">Startdatum</label>
                        <input class="form-control" type="date" name="academy_started_at" id="academy_started_at" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" type="submit">+ Werkgever</button>
                    </div>
                </div>
            </form>
        @endcan

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Werkgever</th>
                        <th>Contactpersoon</th>
                        <th>Chauffeurs</th>
                        <th>Kaarten niet gereed</th>
                        <th>Stap 1</th>
                        <th>Stap 2</th>
                        <th>Stap 3</th>
                        <th>Te laat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $badges = ['locked' => ['badge-light border', 'wacht'], 'open' => ['badge-primary', 'bezig'], 'done' => ['badge-success', 'gereed'], 'ongoing' => ['badge-info', 'doorlopend']];
                    @endphp
                    @forelse($employers as $employer)
                        @php $company = $employer['company']; @endphp
                        <tr>
                            <td><a href="{{ route('admin.academy.employers.show', $company->id) }}">{{ $company->name }}</a></td>
                            <td>
                                @if($company->contactPerson)
                                    {{ trim($company->contactPerson->first_name . ' ' . $company->contactPerson->last_name) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $employer['driver_count'] }}</td>
                            <td>{{ $employer['incomplete_cards'] }}</td>
                            @foreach([1, 2, 3] as $number)
                                @php [$class, $text] = $badges[$employer['steps'][$number]['state']] ?? ['badge-light', '']; @endphp
                                <td><span class="badge {{ $class }}">{{ $text }}</span></td>
                            @endforeach
                            <td>
                                @if($employer['overdue'] > 0)
                                    <span class="badge badge-danger">{{ $employer['overdue'] }}</span>
                                @else
                                    0
                                @endif
                            </td>
                            <td><a class="btn btn-xs btn-primary" href="{{ route('admin.academy.employers.show', $company->id) }}">Openen</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-muted">Nog geen Academy-werkgevers. Begin met de eerste werkgever: daarna begeleidt het portaal je door stap 1 tot en met 7.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
