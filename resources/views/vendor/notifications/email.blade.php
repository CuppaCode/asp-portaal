<x-mail::layout>

<x-slot:header>

</x-slot:header>

{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-mail::subcopy>
    @lang(
        "Indien je problemen hebt om op de \":actionText\" knop te drukken, kopieer en plak de volgende url\n".
        'in jouw webbrowser:',
        [
            'actionText' => $actionText,
        ]
    ) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-mail::subcopy>
@endisset

<x-slot:footer>
</x-slot:footer>

</x-mail::layout>

