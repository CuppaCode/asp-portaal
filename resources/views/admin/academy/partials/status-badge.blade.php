@php
    $classes = [
        'green' => 'badge-success',
        'orange' => 'badge-warning',
        'red' => 'badge-danger',
        'na' => 'badge-secondary',
        'todo' => 'badge-light border',
    ];
@endphp
<span class="badge {{ $classes[$status['status']] ?? 'badge-light' }}">{{ $status['label'] }}</span>
