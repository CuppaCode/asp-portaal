<h4>Bewaken en verlengen</h4>
<div class="text-muted mb-3">
    Signaaltermijnen: Code 95 12 mnd (als er uren openstaan) en 90 dagen · rijbewijs en certificaten 6 mnd en 90 dagen · EHBO en BHV 3 mnd en 6 weken · chauffeurspas 3 mnd en 6 weken.
</div>

@if($anyExpired)
    <div class="alert alert-danger">
        <strong>STOP</strong> Document verlopen (rood)? Bel de werkgever dezelfde dag: de chauffeur mag mogelijk niet rijden. Noteer in Opmerkingen op de chauffeurskaart wanneer en met wie je sprak.
    </div>
@endif

@if($renewalItems->isNotEmpty())
    <div class="card mb-3 border-info">
        <div class="card-header"><strong>Code 95 verlengd: M12 en jaarplanning</strong></div>
        <div class="card-body">
            @foreach($renewalItems as $item)
                @php
                    $driver = $item->certificate->driver;
                    $name = trim(($driver?->contact?->first_name ?? '') . ' ' . ($driver?->contact?->last_name ?? ''));
                @endphp
                <div class="d-flex justify-content-between align-items-center border-bottom py-1">
                    <div>{{ $name }} <span class="text-muted">· {{ $driver?->company?->name }}</span> · nieuwe einddatum {{ $item->certificate->expiry_date }}</div>
                    <div>
                        <a class="btn btn-xs btn-outline-primary" href="{{ route('admin.academy.mail.compose', ['template' => 'M12', 'context' => 'certificate', 'id' => $item->certificate->id, 'item' => $item->id]) }}">M12</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="mb-3">
    <a class="btn btn-sm {{ $filter === 'action' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.academy.overview', ['tab' => 'bewaken']) }}">Actie nodig</a>
    <a class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.academy.overview', ['tab' => 'bewaken', 'filter' => 'all']) }}">Alle documenten</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Chauffeur</th>
                        <th>Document</th>
                        <th>Vervalt</th>
                        <th>Status</th>
                        <th>1e signaal</th>
                        <th>2e signaal</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $driver = $row['driver'];
                            $certificate = $row['certificate'];
                            $name = trim(($driver->contact?->first_name ?? '') . ' ' . ($driver->contact?->last_name ?? ''));
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('admin.academy.driver-cards.edit', ['driver' => $driver->id, 'back_to' => url()->full()]) }}"><strong>{{ $name }}</strong></a>
                                <div class="small"><a href="{{ route('admin.academy.employers.show', $row['company']->id) }}">{{ $row['company']->name }}</a></div>
                            </td>
                            <td>{{ $row['category']->name }}</td>
                            <td>{{ $row['status']['expiry']?->format('d-m-Y') ?? '-' }}</td>
                            <td>@include('admin.academy.partials.status-badge', ['status' => $row['status']])</td>
                            @foreach($row['signals'] as $signal)
                                <td class="small">
                                    @if(!$signal['applicable'])
                                        <span class="text-muted">-</span>
                                    @elseif($signal['sent'])
                                        <span class="badge badge-success">Verstuurd</span>
                                        <div class="text-muted">{{ $signal['sent']->sent_at?->format('d-m-Y') }}</div>
                                    @elseif($signal['due'] && $certificate)
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.academy.mail.compose', ['template' => 'M11', 'context' => 'certificate', 'id' => $certificate->id, 'level' => $signal['level']]) }}">M11 versturen</a>
                                        <div class="text-muted">vanaf {{ $signal['date']->format('d-m-Y') }}</div>
                                    @else
                                        <span class="text-muted">vanaf {{ $signal['date']->format('d-m-Y') }}</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="small" style="min-width: 240px;">
                                {{ $row['action'] }}
                                @if($row['expired'])
                                    <div class="text-danger font-weight-bold">Bel de werkgever vandaag.</div>
                                @endif
                                @if($certificate)
                                    @can('academy_manage')
                                        <details class="mt-1">
                                            <summary class="text-primary" style="cursor: pointer;">Nieuw document</summary>
                                            <form method="POST" action="{{ route('admin.academy.monitoring.new-document', $certificate->id) }}" class="form-inline mt-1">
                                                @csrf
                                                <input class="form-control form-control-sm mr-1" type="date" name="new_expiry_date" required>
                                                <button class="btn btn-xs btn-success" type="submit">Opslaan</button>
                                            </form>
                                        </details>
                                    @endcan
                                @else
                                    <div><a href="{{ route('admin.academy.driver-cards.edit', ['driver' => $driver->id, 'back_to' => url()->full()]) }}">Op de kaart invullen</a></div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-3 text-muted">Geen documenten die aandacht nodig hebben.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
