@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                @if (auth()->user()->roles->contains(1))
                    <div class="card-header">
                        Alle openstaande schadedossiers
                    </div>

                    <div class="card-body">
                        <table class="table table-borderless table-striped table-responsive">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Bedrijf</th>
                                    <th scope="col">Onderwerp</th>
                                    <th scope="col">status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($claims as $claim)
                                <tr class='clickable-row' data-href='{{ route('admin.claims.show', $claim->id) }}'>
                                    <td>{{ $claim->claim_number }}</td>
                                    <td>{{ $claim->company->name }}</td>
                                    <td>{{ $claim->subject }}</td>
                                    <td>{{ $claim->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                @else 
                    <div class="card-header">
                        Alle openstaande schadedossiers
                    </div>

                    <div class="card-body">
                        <table class="table table-borderless table-striped table-responsive">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Onderwerp</th>
                                    <th scope="col">status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($company_claims as $claim)
                                <tr class='clickable-row' data-href='{{ route('admin.claims.show', $claim->id) }}'>
                                    <td>{{ $claim->claim_number }}</td>
                                    <td>{{ $claim->subject }}</td>
                                    <td>{{ $claim->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                @endif
            </div>
        </div>
        @if (auth()->user()->roles->contains(1))
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Alle openstaande taken
                </div>

                <div class="card-body">
                    @isset($tasks)
                    <table class="table table-borderless table-striped table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">Datum</th>
                                <th scope="col">Beschrijving</th>
                                <th scope="col">Owner</th>
                                <th scope="col">status</th>
                                <th scope="col">Schadedossier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                @if(date('d-m-Y', strtotime($task->deadline_at)) >= date('d-m-Y'))
                                    @php $overdue = 'overdue-no'; @endphp
                                @else 
                                    @php $overdue = 'overdue-yes'; @endphp  
                                @endif
                                <tr class='clickable-row {{ $overdue }}' data-href='{{ route('admin.notes.show', $task->id) }}'>
                                    <td>{{ date('d-m-Y', strtotime($task->deadline_at)) }}</td>
                                    <td>{!! Str::limit($task->description, 25) !!}</td>
                                    <td>{{ $task->user->name }}</td>
                                    <td>{{ $task->status }}</td>
                                    <td>
                                        @isset($task->claim->claim_number) 
                                        {{ $task->claim->claim_number }}
                                        @else 
                                        ...
                                        @endisset
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    @else
                    Geen openstaande taken
                    @endisset

                </div>
            </div>
        </div>
    @endif
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Openstaande taken (persoonlijk)
                </div>

                <div class="card-body">
                    @isset($personal_tasks)
                    <table class="table table-borderless table-striped table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">Datum</th>
                                <th scope="col">Beschrijving</th>
                                <th scope="col">status</th>
                                <th scope="col">Schadedossier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($personal_tasks as $task)
                                @if(date('d-m-Y', strtotime($task->deadline_at)) >= date('d-m-Y'))
                                    @php $overdue = 'overdue-no'; @endphp
                                @else 
                                    @php $overdue = 'overdue-yes'; @endphp
                                @endif
                                <tr class='clickable-row {{ $overdue }}' data-href='{{ route('admin.notes.show', $task->id) }}'>
                                    <td>{{ date('d-m-Y', strtotime($task->deadline_at)) }}</td>
                                    <td>{!! Str::limit($task->description, 40) !!}</td>
                                    <td>{{ $task->status }}</td>
                                    <td>
                                        @isset($task->claim->claim_number) 
                                        {{ $task->claim->claim_number }}
                                        @else 

                                        @endisset
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    @else
                    Geen openstaande taken
                    @endisset
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent

@endsection