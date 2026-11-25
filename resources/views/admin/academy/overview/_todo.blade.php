@php
    $tiles = [
        ['value' => $kpis['overdue'], 'label' => 'taken te laat', 'class' => 'text-danger'],
        ['value' => $kpis['upcoming'], 'label' => 'taken binnen ' . config('academy.todo.days_ahead', 7) . ' dagen', 'class' => ''],
        ['value' => $kpis['red'], 'label' => 'documenten rood', 'class' => 'text-danger'],
        ['value' => $kpis['orange'], 'label' => 'documenten oranje', 'class' => 'text-warning'],
        ['value' => $kpis['open_trainings'], 'label' => 'open trainingsdossiers', 'class' => ''],
        ['value' => $kpis['drivers'], 'label' => 'chauffeurs in dienst', 'class' => ''],
    ];
@endphp

<h4 class="mb-0">Overzicht</h4>
<div class="text-muted mb-3">{{ $today->format('d-m-Y') }} · wat er deze week moet gebeuren, over alle werkgevers en trainingen.</div>

<div class="row mb-3">
    @foreach($tiles as $tile)
        <div class="col-6 col-md-2 mb-2">
            <div class="card h-100">
                <div class="card-body py-2">
                    <div class="h3 mb-0 {{ $tile['class'] }}">{{ $tile['value'] }}</div>
                    <div class="small text-muted">{{ $tile['label'] }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <strong>Nu te doen</strong>
                <span class="small text-muted">te laat en komende {{ config('academy.todo.days_ahead', 7) }} dagen</span>
            </div>
            <div class="card-body p-0">
                @forelse($tasks as $task)
                    <div class="d-flex align-items-start border-bottom p-3">
                        <div class="mr-3 text-nowrap" style="width: 110px;">
                            @if($task['status'] === 'overdue')
                                <span class="text-danger font-weight-bold">te laat {{ $task['due']->format('d-m') }}</span>
                            @elseif($task['status'] === 'warning')
                                <span class="text-warning font-weight-bold">{{ $task['due']->format('d-m-Y') }}</span>
                            @elseif($task['due'])
                                <span>{{ $task['due']->format('d-m-Y') }}</span>
                            @else
                                <span class="font-weight-bold">open</span>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <a href="{{ $task['url'] }}" class="text-body font-weight-bold">{{ $task['title'] }}</a>
                            <div class="small text-muted">{{ $task['where'] }}</div>
                        </div>
                        @if($task['mail'])
                            <div class="ml-2">
                                <a class="btn btn-xs btn-outline-primary" href="{{ $task['mail']['url'] }}">{{ $task['mail']['code'] }}</a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-3 text-muted">Niets te doen. Alles is bij.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <strong>Vaste routine vandaag</strong>
                <span class="small text-muted">{{ $today->locale('nl')->translatedFormat('l') }}</span>
            </div>
            <div class="card-body">
                @foreach($routine as $entry)
                    <div class="d-flex align-items-start mb-2">
                        <form method="POST" action="{{ route('admin.academy.routine.toggle') }}" class="mr-2">
                            @csrf
                            <input type="hidden" name="key" value="{{ $entry['key'] }}">
                            @can('academy_manage')
                                <button type="submit" class="btn btn-link p-0" title="{{ $entry['done'] ? 'Terugzetten' : 'Afvinken' }}">
                                    <i class="{{ $entry['done'] ? 'fas fa-check-square text-success' : 'far fa-square' }} fa-lg"></i>
                                </button>
                            @else
                                <i class="{{ $entry['done'] ? 'fas fa-check-square text-success' : 'far fa-square' }} fa-lg"></i>
                            @endcan
                        </form>
                        <div class="{{ $entry['done'] ? 'text-muted' : '' }}">
                            {{ $entry['label'] }}
                            @if($entry['done'] && $entry['done_by'])<div class="small text-muted">{{ $entry['done_by'] }}</div>@endif
                        </div>
                    </div>
                @endforeach
                <div class="small text-muted mt-3">Vink af als het gedaan is. De volgende dag begint de lijst opnieuw.</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><strong>Statussen</strong></div>
            <div class="card-body small">
                <div><span class="badge badge-success">groen</span> Langer dan 6 maanden geldig</div>
                <div><span class="badge badge-warning">oranje</span> Binnen 6 maanden, of Code 95-uren tekort</div>
                <div><span class="badge badge-danger">rood</span> Binnen 90 dagen, verlopen of ontbreekt</div>
                <div><span class="badge badge-secondary">n.v.t.</span> Niet van toepassing</div>
            </div>
        </div>
    </div>
</div>
