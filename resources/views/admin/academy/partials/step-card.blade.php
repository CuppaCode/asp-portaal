@php
    $stateBadges = [
        'locked' => ['badge-light border', 'wacht'],
        'open' => ['badge-primary', 'bezig'],
        'done' => ['badge-success', 'gereed'],
        'ongoing' => ['badge-info', 'doorlopend'],
    ];
    [$badgeClass, $badgeText] = $stateBadges[$step['state']] ?? ['badge-light', ''];
@endphp
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Stap {{ $step['number'] }} · {{ $step['title'] }}</strong>
        <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
    </div>
    <div class="card-body">
        <div class="small mb-2">
            @if(!empty($step['start']))
                <div><span class="text-muted text-uppercase">Start als</span> {{ $step['start'] }}</div>
            @endif
            @if(!empty($step['done']))
                <div><span class="text-muted text-uppercase">Gereed als</span> {{ $step['done'] }}</div>
            @endif
        </div>

        @isset($extra)
            {!! $extra !!}
        @endisset

        @foreach($step['items'] as $entry)
            @include('admin.academy.partials.item-row', ['entry' => $entry, 'mailContext' => $mailContext, 'subjectId' => $subjectId])
        @endforeach

        @if(!empty($step['stop']))
            <div class="alert alert-danger py-2 mt-3 mb-0 small">
                <strong>STOP</strong> {{ $step['stop'] }}
            </div>
        @endif
    </div>
</div>
