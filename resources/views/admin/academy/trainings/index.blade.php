@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Academy · Trainingen</strong>
        @can('academy_manage')
            <a class="btn btn-sm btn-primary" href="{{ route('admin.academy.trainings.create') }}">+ Training</a>
        @endcan
    </div>
    <div class="card-body">
        <ul class="nav nav-pills mb-3">
            <li class="nav-item"><a class="nav-link {{ $status !== 'closed' ? 'active' : '' }}" href="{{ route('admin.academy.trainings.index') }}">Open dossiers</a></li>
            <li class="nav-item"><a class="nav-link {{ $status === 'closed' ? 'active' : '' }}" href="{{ route('admin.academy.trainings.index', ['status' => 'closed']) }}">Gesloten</a></li>
        </ul>

        @php
            $badges = ['locked' => ['badge-light border', 'wacht'], 'open' => ['badge-primary', 'bezig'], 'done' => ['badge-success', 'gereed']];
        @endphp

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Training</th>
                        <th>Werkgever</th>
                        <th>Deelnemers</th>
                        <th>Stap 4</th>
                        <th>Stap 5</th>
                        <th>Stap 6</th>
                        <th>Te laat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainings as $row)
                        @php $training = $row['training']; @endphp
                        <tr>
                            <td>{{ $training->training_date->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('admin.academy.trainings.show', $training->id) }}">{{ $training->name }}</a>
                                @if($training->is_prevention)<span class="badge badge-warning">schadepreventie</span>@endif
                            </td>
                            <td>{{ $training->company?->name }}</td>
                            <td>{{ $training->participants_count }}</td>
                            @foreach([4, 5, 6] as $number)
                                @php [$class, $text] = $badges[$row['steps'][$number]['state']] ?? ['badge-light', '']; @endphp
                                <td><span class="badge {{ $class }}">{{ $text }}</span></td>
                            @endforeach
                            <td>@if($row['overdue'] > 0)<span class="badge badge-danger">{{ $row['overdue'] }}</span>@else 0 @endif</td>
                            <td><a class="btn btn-xs btn-primary" href="{{ route('admin.academy.trainings.show', $training->id) }}">Openen</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-muted">Geen dossiers.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
