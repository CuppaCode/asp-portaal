@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            
            <a href="{{ route("admin.claims.create") }}" class="nounderline" >
                <div class="card bg-success mb-4">
                    <div class="card-header position-relative d-flex justify-content-center align-items-center">
                    <span class="text-white">Schadedossier aanmaken</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card bg-warning text-white py-3" style="min-height: 134px;">
                <div class="card-body row text-center">
                    <div class="col">
                        <div class="text-value-xl">{{ $claims_count['claims_all'] }}</div>
                        <div class="text-uppercase text-muted small">Openstaande dossier</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card bg-danger text-white py-3" style="min-height: 134px;">
                <div class="card-body row text-center">
                    <div class="col">
                        <div class="text-value-xl">{{ $unassignedClaims }}</div>
                        <div class="text-uppercase text-muted small">Niet toegewezen dossier</div>
                    </div>
                </div>
            </div>
        </div>  
        <div class="col-sm-6 col-lg-3">
            <div class="card bg-dark text-white py-3" style="min-height: 134px;">
                <div class="card-body row text-center">
                    <div class="col">
                        <a href="{{ route("admin.claims.show", [$longestClaim->id]) }}" class="text-value-xl text-white">{{ $longestClaim->claim_number }}</a>
                        <div class="text-uppercase text-muted small">Langst openstaande claim</div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
    <div class="row">

        @foreach($users as $user)
            @if($user->newClaims != 0 || $user->inProgressClaims != 0)
                <div class="col-sm-6 col-lg-3">
                    <div class="card" style="min-height: 134px;">
                        <div class="card-header py-2">
                            {{ $user->contact->first_name . ' ' . $user->contact->last_name}} 
                        </div>
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col">
                                    <div class="text-value-xl">{{ $user->newClaims }}</div>
                                    <div class="text-uppercase text-muted small">Open Dossiers</div>
                                </div>
                                <div class="vr"></div>
                                <div class="col">
                                    <div class="text-value-xl">{{ $user->inProgressClaims }}</div>
                                    <div class="text-uppercase text-muted small">In behandeling</div>
                                </div>
                            </div>
                            <div class="row pt-3">
                                <div class="col">
                                    
                                    <div class="text-value-xl">{{ $user->assignedTask }}</div>
                                    <div class="text-uppercase text-muted small">Openstaande Taken</div>
                                </div>
                                <div class="vr"></div>
                                <div class="col">
                                    {{-- <div class="text-value-xl">{{ $user->assignedTask }}</div>
                                    <div class="text-uppercase text-muted small">In behandeling</div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    Openstaande taken (persoonlijk)
                </div>

                <div class="card-body">
                    @isset($personal_tasks)
                    <table class="table table-borderless table-striped" width=100%>
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
                                @php 
                                    $deadline_at = $task->deadline_at;
                                    $today = date('d-m-Y');

                                    // dd($deadline_at);
                                    $deadline_at_time = strtotime($deadline_at);
                                    $today_time = strtotime($today);
                                @endphp

                                @if($today_time <= $deadline_at_time)
                                    @php $overdue = 'overdue-no'; @endphp
                                @else 
                                    @php $overdue = 'overdue-yes'; @endphp
                                @endif
                                <tr class='clickable-row {{ $overdue }}' data-href='{{ route('admin.tasks.show', $task->id) }}'>
                                    <td>{{ date('d-m-Y', strtotime($task->deadline_at)) }}</td>
                                    <td>{!! Str::limit($task->description, 40) !!}</td>
                                    <td>{{ App\Models\TASK::STATUS_SELECT[$task->status] }}</td>
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
                    {{ $personal_tasks->links() }}

                    @else
                    Geen openstaande taken
                    @endisset
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    Openstaande claims (persoonlijk)
                </div>

                <div class="card-body">
                    @isset($personal_claims)
                    <table class="table table-borderless table-striped" width=100%>
                        <thead>
                            <tr>
                                <th scope="col">Dossier</th>
                                <th scope="col">Klant</th>
                                <th scope="col">Kenmerk WP</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($personal_claims as $claim)
                                <tr class='clickable-row' data-href='{{ route('admin.claims.show', $claim->id) }}'>
                                    <td> {{ $claim->claim_number ?? ''}} </td>
                                    <td> {{ $claim->company->name ?? '' }} </td>
                                    <td> {{ $claim->subject ?? '' }} </td>
                                    <td> {{ App\Models\Claim::STATUS_SELECT[$claim->status] ?? '' }} </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $personal_claims->links() }}

                    @else
                    Geen openstaande claims
                    @endisset
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Recente updates
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped table-hover datatable-AuditLog">
                            <thead>
                                <tr>
                                    <th scope="col">Dossier</th>
                                    <th scope="col">Klant</th>
                                    <th scope="col">Kenmerk WP</th>
                                    <th scope="col">Status</th>
                                    <th>
                                        {{ trans('cruds.auditLog.fields.user_id') }}
                                    </th>
                                    <th>
                                        Laatste update
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditLogs as $key => $auditLog)
                                    <tr data-entry-url="{{ route('admin.audit-logs.show', $auditLog->id) }}" data-entry-id="{{ $auditLog->id }}">
                                        <td>
                                            {{ $auditLog->Claim[0]['claim_number'] ?? '' }}
                                        </td>
                                        <td>
                                            {{ $auditLog->Company[0]['name'] ?? '' }}
                                        </td>
                                        <td>
                                            {{ $auditLog->Claim[0]['subject'] ?? '' }}
                                        </td>
                                        <td>
                                            {{ App\Models\Claim::STATUS_SELECT[$auditLog->Claim[0]['status']] ?? '' }}
                                        </td>
                                        <td>
                                            {{ $auditLog->claimAssignee[0]['first_name'] ?? '' }} {{ $auditLog->claimAssignee[0]['last_name'] ?? '' }}
                                        </td>
                                        <td>
                                            {{ $auditLog->created_at ?? '' }}
                                        </td>
            
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent

@endsection