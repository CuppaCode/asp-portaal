@php
    /** @var \App\Models\AcademyChecklistItem $item */
    $item = $entry['model'];
    $definition = $entry['definition'];
    $locked = $entry['locked'];
    $isDone = (bool) $item->done_at;
    $isNa = (bool) $item->not_applicable;
    $mailCode = $definition['mail'] ?? null;
    $formCode = $definition['form'] ?? null;
    $period = $item->period ? \Carbon\Carbon::createFromFormat('Y-m', $item->period)->locale('nl')->translatedFormat('F Y') : null;
    $label = $period ? ($definition['label'] ?? '') . ' - ' . $period : ($definition['label'] ?? $item->item_key);
@endphp
<div class="d-flex align-items-start border-bottom py-2 {{ $locked ? 'text-muted' : '' }}">
    <div class="mr-3 pt-1" style="width: 28px;">
        @if($isDone)
            <span class="text-success" title="Afgevinkt"><i class="fas fa-check-square fa-lg"></i></span>
        @elseif($isNa)
            <span class="text-muted"><i class="far fa-minus-square fa-lg"></i></span>
        @elseif($locked)
            <span class="text-muted"><i class="far fa-square fa-lg"></i></span>
        @else
            @can('academy_manage')
                <form method="POST" action="{{ route('admin.academy.items.toggle', $item->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-0" title="Afvinken (pas als het bewijs er is)"><i class="far fa-square fa-lg"></i></button>
                </form>
            @else
                <span class="text-muted"><i class="far fa-square fa-lg"></i></span>
            @endcan
        @endif
    </div>

    <div class="flex-grow-1">
        <div class="{{ $isNa ? 'text-muted' : '' }}" @if($isNa) style="text-decoration: line-through;" @endif>{{ $label }}</div>
        <div class="small text-muted">
            @if(!empty($definition['when']))
                {{ $definition['when'] }}
            @endif
            @if($item->due_date)
                @if($entry['overdue'])
                    <span class="text-danger font-weight-bold">te laat sinds {{ $item->due_date->format('d-m-Y') }}</span>
                @elseif($entry['warning'])
                    <span class="text-warning font-weight-bold">uiterlijk {{ $item->due_date->format('d-m-Y') }}</span>
                @else
                    streefdatum {{ $item->due_date->format('d-m-Y') }}
                @endif
            @endif
            @if($isDone)
                gedaan {{ $item->done_at->format('d-m-Y') }}@if($item->doneBy) door {{ $item->doneBy->name }}@endif
                @if($item->mailing_id)
                    <a href="{{ route('admin.mailings.show', $item->mailing_id) }}" target="_blank">mail</a>
                @endif
            @endif
            @if($isNa)
                n.v.t.@if($entry['auto_na']): automatisch bepaald @elseif($item->not_applicable_reason): {{ $item->not_applicable_reason }}@endif
            @endif
        </div>
    </div>

    <div class="ml-3 text-right text-nowrap">
        @if($locked)
            <span class="badge badge-pill badge-light border">wacht</span>
        @else
            @if($mailCode && !$isNa)
                <a class="btn btn-xs btn-outline-primary" href="{{ route('admin.academy.mail.compose', ['template' => $mailCode, 'context' => $mailContext, 'id' => $subjectId, 'item' => $item->id]) }}" title="Mail {{ $mailCode }} versturen">{{ $mailCode }}</a>
            @endif
            @if($formCode && !$isNa)
                <span class="badge badge-pill badge-success" title="Formulier {{ $formCode }}">{{ $formCode }}</span>
            @endif
            @can('academy_manage')
                @if(!$isDone && !$isNa && !empty($definition['can_na']))
                    <form method="POST" action="{{ route('admin.academy.items.na', $item->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-outline-secondary">n.v.t.</button>
                    </form>
                @endif
                @if(($isDone || $isNa) && !$entry['auto_na'])
                    <form method="POST" action="{{ route('admin.academy.items.undo', $item->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-link text-muted">ongedaan maken</button>
                    </form>
                @endif
            @endcan
        @endif
    </div>
</div>
