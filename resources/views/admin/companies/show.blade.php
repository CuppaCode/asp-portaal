@extends('layouts.admin')
@section('content')


{{-- Company Header --}}

<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">{{ $company->name }}</h3>
                <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-edit"></i> Edit
                </a>
            </div>
            <div class="card-body">
                <span class="badge bg-info">{{ App\Models\Company::COMPANY_TYPE_SELECT[$company->company_type] ?? '' }}</span>
                <span class="badge bg-success">{{ $company->active ? 'Active' : 'Inactive' }}</span>
            </div>
        </div>
    </div>
</div>


{{-- Overview & Statistics --}}
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Overview</div>
            <div class="card-body">
                <p><strong>{{ trans('cruds.company.fields.address') }}:</strong> {{ $company->street }}, {{ $company->zipcode }} {{ $company->city }}, {{ $company->country }}</p>
                <p><strong>{{ trans('cruds.company.fields.phone') }}:</strong> {{ $company->phone }}</p>
                <p><strong>{{ trans('cruds.company.fields.bank_account_number') }}:</strong> {{ $bankAccountNumber ?? '-' }}</p>
                <p><strong>{{ trans('cruds.company.fields.description') }}:</strong> {!! $company->description !!}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">{{ trans('cruds.company.fields.contactperson') }}</div>
            <div class="card-body">
                @if($contact)
                    <dl class="row mb-0">
                        <dt class="col-sm-4">{{ trans('cruds.company.fields.name') }}</dt>
                        <dd class="col-sm-8">{{ $contact->first_name }} {{ $contact->last_name }}</dd>
                        <dt class="col-sm-4">{{ trans('cruds.company.fields.phone') }}</dt>
                        <dd class="col-sm-8">
                            @if($contact->phone)
                                <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                            @else
                                -
                            @endif
                        </dd>
                        <dt class="col-sm-4">{{ trans('cruds.company.fields.email') }}</dt>
                        <dd class="col-sm-8">
                            @if($contact->email)
                                <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                            @else
                                -
                            @endif
                        </dd>
                        <dt class="col-sm-4">{{ trans('cruds.company.fields.remarks') }}</dt>
                        <dd class="col-sm-8">{{ $contact->note ?? '-' }}</dd>
                    </dl>
                @else
                    <p class="mb-0 text-muted">{{ __('Geen contactpersoon gekoppeld.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>


{{-- Additional Information --}}
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">{{ trans('cruds.company.fields.additional_information') }}</div>
            <div class="card-body">{{ $additionalInformation ?? '-' }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                {{ trans('cruds.sla.title') }}
                @if($sla)
                    <a href="{{ route('admin.sla.edit', $sla->id) }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i> {{ __('Edit SLA') }}
                    </a>
                @else
                    <a href="{{ route('admin.sla.create', ['company_id' => $company->id]) }}" class="btn btn-sm btn-success">
                        <i class="fa fa-plus"></i> {{ trans('cruds.sla.create_sla') }}
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($sla)
                    <dl class="row mb-0">
                        <dt class="col-sm-3">{{ trans('cruds.sla.fields.label') }}</dt>
                        <dd class="col-sm-9">{{ \App\Models\SLA::LABEL_SELECT[$sla->label] ?? $sla->label }}</dd>
                        <dt class="col-sm-3">{{ trans('cruds.sla.fields.startdate') }}</dt>
                        <dd class="col-sm-9">{{ $sla->startdate ?? '-' }}</dd>
                        <dt class="col-sm-3">{{ trans('cruds.sla.fields.enddate') }}</dt>
                        <dd class="col-sm-9">{{ $sla->enddate ?? '-' }}</dd>
                        <dt class="col-sm-3">{{ trans('cruds.sla.fields.max_amount') }}</dt>
                        <dd class="col-sm-9">{{ $sla->max_amount ?? '-' }}</dd>
                        <dt class="col-sm-3">{{ trans('cruds.sla.fields.amount_users') }}</dt>
                        <dd class="col-sm-9">{{ $sla->amount_users ?? '-' }}</dd>
                        <dt class="col-sm-3">{{ trans('cruds.sla.fields.reports') }}</dt>
                        <dd class="col-sm-9">{{ \App\Models\SLA::REPORT_SELECT[$sla->reports] ?? $sla->reports }}</dd>
                        <dt class="col-sm-3">{{ trans('cruds.sla.fields.analytics') }}</dt>
                        <dd class="col-sm-9">
                            @if($sla->analytics_options)
                                @foreach(json_decode($sla->analytics_options) as $option)
                                    {{ \App\Models\SLA::ANALYTICS_SELECT[$option] ?? $option }}@if(!$loop->last), @endif
                                @endforeach
                            @else
                                -
                            @endif
                        </dd>
                        <dt class="col-sm-3">{{ trans('cruds.sla.fields.other') }}</dt>
                        <dd class="col-sm-9">{{ $sla->other ?? '-' }}</dd>
                    </dl>
                @else
                    <p class="mb-0 text-muted">{{ __('Geen SLA gekoppeld aan dit bedrijf.') }}</p>
                @endif
            </div>
        </div>
    </div>
    {{-- Financial Information --}}
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">{{ trans('cruds.company.fields.financial') }}</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">{{ trans('cruds.company.fields.start_fee') }}</dt>
                    <dd class="col-sm-8">
                        @if(!is_null($company->start_fee))
                            &euro; {{ number_format($company->start_fee, 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </dd>
                    <dt class="col-sm-4">{{ trans('cruds.company.fields.claims_fee') }}</dt>
                    <dd class="col-sm-8">
                        @if(!is_null($company->claims_fee))
                            &euro; {{ number_format($company->claims_fee, 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </dd>
                    <dt class="col-sm-4">{{ trans('cruds.company.fields.additional_costs') }}</dt>
                    <dd class="col-sm-8">
                        @if(!is_null($company->additional_costs))
                            &euro; {{ number_format($company->additional_costs, 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>



{{-- Claims & Drivers --}}
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">{{ trans('cruds.company.fields.claims') }}</div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                @if(isset($claims) && $claims->count())
                    <ul class="list-group">
                        @foreach($claims as $claim)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <a href="{{ route('admin.claims.show', $claim->id) }}">#{{ $claim->claim_number }}</a>
                                    @if($claim->subject)
                                        - {{ $claim->subject }}
                                    @endif
                                    <span class="ms-2 badge rounded-pill"
                                        style="background-color:
                                            @switch($claim->status)
                                                @case('open') #0d6efd; @break
                                                @case('in_progress') #ffc107; @break
                                                @case('finished') #198754; @break
                                                @case('claim_denied') #dc3545; @break
                                                @default #6c757d;
                                            @endswitch
                                            color: #fff;">
                                    {{ \App\Models\Claim::STATUS_SELECT[$claim->status] ?? $claim->status }}
                                    </span>
                                </span>
                                <span class="badge bg-secondary">{{ $claim->created_at->format('d-m-Y') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>No claims found for this company.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">{{ trans('cruds.company.fields.drivers') }}</div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                @if(isset($drivers) && $drivers->count())
                    <ul class="list-group">
                        @foreach($drivers as $driver)
                            <li class="list-group-item">
                                @php
                                    $name = $driver->driver_full_name ?? null;
                                    if (!$name && $driver->contact) {
                                        $name = trim(($driver->contact->first_name ?? '') . ' ' . ($driver->contact->last_name ?? ''));
                                    }
                                @endphp
                                {{ $name ?: 'Unknown' }}
                                @if($driver->contact && $driver->contact->phone)
                                    <span class="text-muted">({{ $driver->contact->phone }})</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>No drivers found for this company.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">{{ trans('cruds.company.fields.statistics') }}</div>
            <div class="card-body">
                <p><strong>{{ trans('cruds.company.fields.open_claims') }}:</strong> {{ $openClaims }}</p>
                <p><strong>{{ trans('cruds.company.fields.closed_claims') }}:</strong> {{ $closedClaims }}</p>
                <p><strong>{{ trans('cruds.company.fields.closed_claims_this_year') }}:</strong> {{ $closedClaimsThisYear }}</p>
                <p><strong>{{ trans('cruds.company.fields.connected_drivers') }}:</strong> {{ $driverCount }}</p>
                <p><strong>{{ trans('cruds.company.fields.company_size') }}:</strong> {{ $companySize ?? '-' }}</p>
                <p><strong>{{ trans('cruds.company.fields.truck_count') }}:</strong> {{ $truckCount ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <a class="btn btn-default" href="{{ route('admin.companies.index') }}">
        {{ trans('global.back_to_list') }}
    </a>
</div>  
</div>



@endsection