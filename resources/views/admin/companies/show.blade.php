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
                <p><strong>{{ trans('cruds.company.fields.contact') }}:</strong> {{ $contact ? $contact->first_name . ' ' . $contact->last_name : '-' }}</p>
                <p><strong>{{ trans('cruds.company.fields.bank_account_number') }}:</strong> {{ $bankAccountNumber ?? '-' }}</p>
                <p><strong>{{ trans('cruds.company.fields.description') }}:</strong> {!! $company->description !!}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Statistics</div>
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


{{-- Additional Information --}}
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">{{ trans('cruds.company.fields.additional_information') }}</div>
            <div class="card-body">{{ $additionalInformation ?? '-' }}</div>
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
                                    <span class="text-muted">
                                        ({{ \App\Models\Claim::STATUS_SELECT[$claim->status] ?? $claim->status }})
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
    <div class="form-group">
        <a class="btn btn-default" href="{{ route('admin.companies.index') }}">
            {{ trans('global.back_to_list') }}
        </a>
    </div>  
</div>



@endsection