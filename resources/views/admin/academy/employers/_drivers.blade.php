@php
    $codes = ['rijbewijs' => 'Rijbewijs', 'code_95' => 'Code 95', 'chauffeurspas' => 'Pas'];
    $stateClasses = ['incomplete' => 'badge-warning', 'unchecked' => 'badge-info', 'checked' => 'badge-success'];
@endphp
<div class="table-responsive mb-3">
    <div class="mb-2 small">
        Formulier 'Check je papieren' (PDF, een pagina per chauffeur):
        <a href="{{ route('admin.academy.employers.forms', $company->id) }}">chauffeurs met ontbrekende gegevens</a> ·
        <a href="{{ route('admin.academy.employers.forms', [$company->id, 'filter' => 'no_email']) }}">chauffeurs zonder e-mail</a> ·
        <a href="{{ route('admin.academy.employers.forms', [$company->id, 'filter' => 'all']) }}">alle chauffeurs</a>
    </div>
    <table class="table table-sm table-bordered mb-2">
        <thead>
            <tr>
                <th>Chauffeur</th>
                <th>E-mail</th>
                @foreach($codes as $label)
                    <th>{{ $label }}</th>
                @endforeach
                <th>Kaart</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($driverRows as $row)
                @php $driver = $row['driver']; @endphp
                <tr>
                    <td>
                        <strong>{{ trim(($driver->contact?->first_name ?? '') . ' ' . ($driver->contact?->last_name ?? '')) }}</strong>
                        @if($driver->function)<div class="small text-muted">{{ $driver->function }}</div>@endif
                    </td>
                    <td>{{ $driver->contact?->email ?: 'nee' }}</td>
                    @foreach($codes as $code => $label)
                        <td>
                            @if(isset($row['rows'][$code]))
                                @include('admin.academy.partials.status-badge', ['status' => $row['rows'][$code]['status']])
                                @if($row['rows'][$code]['status']['expiry'])
                                    <div class="small text-muted">{{ $row['rows'][$code]['status']['expiry']->format('d-m-Y') }}</div>
                                @endif
                            @endif
                        </td>
                    @endforeach
                    <td>
                        <span class="badge {{ $stateClasses[$row['state']] ?? 'badge-light' }}">{{ $stateLabels[$row['state']] ?? $row['state'] }}</span>
                        @if($row['planned'] > 0)<span class="badge badge-info">ingepland</span>@endif
                    </td>
                    <td class="text-nowrap">
                        <a class="btn btn-xs btn-outline-primary" href="{{ route('admin.academy.driver-cards.edit', ['driver' => $driver->id, 'back_to' => url()->current()]) }}">Kaart</a>
                        <a class="btn btn-xs btn-outline-secondary" href="{{ route('admin.academy.driver-cards.form', $driver->id) }}" title="Formulier voor de chauffeur">PDF</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ 4 + count($codes) }}" class="text-muted">Nog geen chauffeurs. Voeg de chauffeurs van de namenlijst toe.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@can('academy_manage')
    <form method="POST" action="{{ route('admin.academy.employers.drivers.store', $company->id) }}" class="border rounded p-2 mb-3">
        @csrf
        <div class="form-row align-items-end">
            <div class="col-md-3"><label class="small mb-0">Voornaam</label><input class="form-control form-control-sm" type="text" name="first_name" required></div>
            <div class="col-md-3"><label class="small mb-0">Achternaam</label><input class="form-control form-control-sm" type="text" name="last_name" required></div>
            <div class="col-md-2"><label class="small mb-0">Functie</label><input class="form-control form-control-sm" type="text" name="function"></div>
            <div class="col-md-3"><label class="small mb-0">E-mail (indien bekend)</label><input class="form-control form-control-sm" type="email" name="email"></div>
            <div class="col-md-1"><button class="btn btn-sm btn-primary" type="submit">+ Chauffeur</button></div>
        </div>
    </form>
@endcan

@if($formerDrivers->isNotEmpty())
    <div class="small text-muted">
        Uit dienst:
        @foreach($formerDrivers as $former)
            <a href="{{ route('admin.academy.driver-cards.edit', ['driver' => $former->id, 'back_to' => url()->current()]) }}">{{ trim(($former->contact?->first_name ?? '') . ' ' . ($former->contact?->last_name ?? '')) }}</a>@if(!$loop->last), @endif
        @endforeach
    </div>
@endif
