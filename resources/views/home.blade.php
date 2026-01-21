@extends('layouts.admin')
@section('content')

@php
$user = auth()->user();
$isAdminOrAgent = $user->isAdminOrAgent();
@endphp

<div class="content">
    <style>
        /* Ensure vertical spacing between certificate tiles even if bootstrap gutters are overridden */
        .cert-tile { margin-bottom: 1rem !important; }
    </style>
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
    @if ($isAdminOrAgent)
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card bg-warning text-white py-3" style="min-height: 134px;">
                <div class="card-body row text-center">
                    <div class="col">
                        <a href="{{ route("admin.claims.open") }}" class="text-value-xl text-white">{{ $claims_count['claims_all'] }}</a>
                        <div class="text-uppercase text-muted small">Openstaande dossier</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card bg-danger text-white py-3" style="min-height: 134px;">
                <div class="card-body row text-center">
                    <div class="col">
                        <a href="{{ route("admin.claims.unassigned") }}" class="text-value-xl text-white">{{ $unassignedClaims }}</a>
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
            @if($user->newClaims != 0 || $user->inProgressClaims != 0 || $user->assignedTask)
                <div class="col-sm-6 col-lg-3">
                    <div class="card" style="min-height: 134px;">
                        <div class="card-header py-2">
                            {{ $user->contact->first_name . ' ' . $user->contact->last_name}} 
                        </div>
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col cert-tile">
                                    <div class="text-value-xl">{{ $user->newClaims }}</div>
                                    <div class="text-uppercase text-muted small">Nieuwe Dossiers</div>
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
    @endif
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
                                <tr class='clickable-row {{ $overdue }}' data-href='{{ route('admin.claims.show', $task->claim->id) }}'>
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
    @if ($isAdminOrAgent)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Openstaande taken (Alle)
                </div>

                <div class="card-body">
                    @isset($tasks)
                    <table class="table table-borderless table-striped" width=100%>
                        <thead>
                            <tr>
                                <th scope="col">Datum</th>
                                <th scope="col">Beschrijving</th>
                                <th scope="col">status</th>
                                <th scope="col">Schadedossier</th>
                                <th scope="col">Gebruiker</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
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
                                    <td>{{ $task->user->name ?? '' }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    {{ $tasks->links() }}

                    @else
                    Geen openstaande taken
                    @endisset
                </div>
            </div>
        </div>

    @if($isAdminOrAgent)
    @can('certificate_access')
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Certificaten — verlopen of binnen 30 dagen
                </div>
                <div class="card-body">
                    @if(!empty($categories_expiring_30) && $categories_expiring_30->count())
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 gy-3">
                            @foreach($categories_expiring_30 as $category)
                                @php
                                    $categoryName = $category->name ?? 'Onbekend';
                                    $totalCount = $category->certificates->count();
                                    $showCount = min(5, $totalCount);
                                @endphp
                                <div class="col cert-tile">
                                    <div class="card h-100 mb-0" style="min-height: auto;">
                                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                            <div class="fw-semibold small mb-0">{{ $categoryName }}</div>
                                            <div>
                                                <span class="badge bg-danger text-white me-1">{{ $totalCount }}</span>
                                            </div>
                                        </div>
                                        <ul class="list-group list-group-flush small">
                                            @forelse($category->certificates->take(5) as $certificate)
                                                @php
                                                    $expired = false;
                                                    if(!empty($certificate->expiry_date)){
                                                        $expired = \Carbon\Carbon::parse($certificate->expiry_date)->lte(\Carbon\Carbon::now());
                                                        $expiryLabel = \Carbon\Carbon::parse($certificate->expiry_date)->format('d-m-Y');
                                                    } else {
                                                        $expiryLabel = '-';
                                                    }
                                                    $driverName = $certificate->driver->driver_name ?? ($certificate->driver->contact->first_name . ' ' . $certificate->driver->contact->last_name ?? 'Niet gevonden');
                                                @endphp
                                                <li class="list-group-item d-flex align-items-center py-1 clickable-row" data-href="{{ route('admin.certificate.show', $certificate->id) }}">
                                                    <div class="me-3 d-flex align-items-center" style="min-width:90px;">
                                                        <span class="badge rounded-pill @if($expired) bg-danger text-white @else bg-warning text-dark @endif">{{ $expiryLabel }}</span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-medium">{{ $driverName }}</div>
                                                        <div class="text-muted small">{{ $certificate->name }}</div>
                                                    </div>
                                                    <div class="d-flex align-items-center ms-3">
                                                        @can('driver_show')
                                                            @if(isset($certificate->driver->id))
                                                                <a href="{{ route('admin.drivers.show', $certificate->driver->id) }}" class="btn btn-sm btn-outline-primary me-1 mr-1 stop-propagation" title="Naar chauffeur">
                                                                    <i class="fa fa-user" aria-hidden="true"></i>
                                                                </a>
                                                            @endif
                                                        @endcan
                                                        @can('certificate_access')
                                                            <a href="{{ route('admin.certificate.show', $certificate->id) }}" class="btn btn-sm btn-outline-success stop-propagation" title="Naar certificaat">
                                                                <i class="fa fa-id-card" aria-hidden="true"></i>
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item py-1">Geen certificaten</li>
                                            @endforelse
                                        </ul>
                                        <div class="card-footer py-2 d-flex justify-content-between align-items-center">
                                            <small class="text-muted mb-0">{{ $showCount }} van {{ $totalCount }}</small>
                                            <a href="{{ route('admin.certificate-categories.show', $category->id) }}" class="btn btn-success btn-sm">{{ __('Bekijk alle') }}</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        Geen categorieën gevonden
                    @endif
                </div>
            </div>
        </div>
    @endcan
    @endif
        

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
                                    <th scope="col">Gebruiker</th>
                                    <th scope="col">Laatste update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditLogs as $key => $auditLog)
                                    @if (isset($auditLog->Claim[0])) 
                                        <tr class='clickable-row' data-href="{{ route('admin.claims.show', $auditLog->subject_id) }}" data-entry-id="{{ $auditLog->id }}">
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
                                                {{ $auditLog->claimAssignee ?? '' }}
                                            </td>
                                            <td>
                                                {{ $auditLog->created_at ?? '' }}
                                            </td>
                
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
@section('scripts')
@parent
<script>
// Make rows with .clickable-row navigate to their data-href, but allow buttons/links with .stop-propagation to be clicked without navigating the row
document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(e) {
        // If the clicked element or its ancestor has .stop-propagation, do nothing (allow link/button default)
        if (e.target.closest && e.target.closest('.stop-propagation')) {
            return;
        }

        var row = e.target.closest && e.target.closest('.clickable-row');
        if (row) {
            var href = row.getAttribute('data-href');
            if (href) {
                window.location = href;
            }
        }
    });
});
</script>

@endsection