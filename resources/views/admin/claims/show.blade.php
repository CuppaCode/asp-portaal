@extends('layouts.admin')
@section('content')

@php

    $isAdmin = auth()->user()->roles->contains(1);

@endphp

<div class="top-bar-claims form-group d-flex justify-content-between align-items-center">
    <a class="btn btn-dark" href="{{ route('admin.claims.index') }}">
        {{ trans('global.back_to_list') }}
    </a>

    @if ($isAdmin)

        @if ($claim->assign_self == true)
            <div class="alert alert-danger" role="alert">
                Let op! Dit schadedossier wordt behandeld door klant zelf.
            </div>
        @endif
    
    @endif

    @unless( !$claim->assign_self && !$isAdmin )

        <a class="btn btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
            {{ trans('global.edit') }}
        </a>

    @endunless
    
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        Schadedossier overzicht

        <select class="form-control select2 col-md-4" id="current-status" data-claim-id="{{ $claim->id }}">

            @foreach (App\Models\Claim::STATUS_SELECT as $key => $status)

                <option value="{{ $key }}" {{ $claim->status == $key ? 'selected' : '' }}>{{ $status }}</option>

            @endforeach

        </select>


  
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.claim_number') }}
                </div>
                {{ $claim->claim_number }}
            </div>
            <div class="col-md-3">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.company') }}</div>
                {{ $claim->company->name ?? '' }}
            </div>
            <div class="col-md-3">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.subject') }}</div>
                {{ $claim->subject }}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Schademelding
            </div>

            <div class="card-body">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.date_accident') }}
                </div>
                <p class="card-text">{{ $claim->date_accident }}</p>
            
                <div class="card-title">
                    {{ trans('cruds.claim.fields.injury') }}
                </div>
                <p class="card-text">{{ App\Models\Claim::INJURY_SELECT[$claim->injury] ?? '' }}</p>
                            
                @if ($claim->injury == 'yes')
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.injury_office') }}
                    </div>
                    <p class="card-text text-capitalize">
                        @if ($claim->injury_office != null)
                        {{ substr($claim->injury_office->identifier, 7) ?? '' }}
                        @endif
                    </p>
                @elseif ( $claim->injury == 'other' )
                <div class="card-title">
                    {{ trans('cruds.claim.fields.injury_other') }}
                </div>
                <p class="card-text">{{ $claim->injury_other }}</p>
                @else

                @endif
                <div class="card-title">
                    {{ trans('cruds.claim.fields.recoverable_claim') }}
                </div>
                <p class="card-text">{{ App\Models\Claim::RECOVERABLE_CLAIM_SELECT[$claim->recoverable_claim] ?? '' }}</p>
                
                <div class="card-title">
                    Soort schade
                </div>
                <p class="card-text">{{ App\Models\Claim::DAMAGE_KIND[$claim->damage_kind] ?? '' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Contactgegevens
            </div>
            <div class="card-body">
                @isset($contacts) 
                <div class="card-title">
                    Naam
                </div>
                <p class="card-text">
                    {{ $contacts->first_name}} {{ $contacts->last_name}}   
                </p>
                <div class="card-title">
                    Email
                </div>
                <p class="card-text"><a href="mailto:{{ $contacts->email}}">{{ $contacts->email}}   </a> </p>
                @else
                    Nog geen contactpersoon bekend.
                @endisset 

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Gegevens wagenpark
            </div>

            <div class="card-body">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.vehicle') }}
                </div>
                <p class="card-text">{{ $claim->vehicle->name ?? '' }}</p>
            
                <div class="card-title">
                    {{ trans('cruds.claim.fields.damaged_part') }}
                </div>
                    <p class="card-text">
                        @if ( $claim->damaged_part !== null )
                            @foreach(json_decode( $claim->damaged_part ) as $part)
                                <span class="badge badge-success">{{ App\Models\Claim::DAMAGED_PART_SELECT[$part] }}</span>
                            @endforeach
                        @endif
                    </p>
            
                <div class="card-title">
                    {{ trans('cruds.claim.fields.damage_origin') }}
                </div>
                <p class="card-text">
                    @if ( $claim->damage_origin !== null )
                        @foreach(json_decode( $claim->damage_origin ) as $origin)
                            <span class="badge badge-success">{{ App\Models\Claim::DAMAGE_ORIGIN[$origin] }}</span>
                        @endforeach
                    @endif
                </p>

                <div class="card-title">
                    {{ trans('cruds.claim.fields.damaged_area') }}
                </div>
                
                <p class="card-text">
                    @if ( $claim->damaged_area !== null )
                        @foreach(json_decode( $claim->damaged_area ) as $area)
                            <span class="badge badge-success">{{ App\Models\Claim::DAMAGED_AREA_SELECT[$area] }}</span>
                        @endforeach
                    @endif
                </p>
                <p class="card-text">{{ App\Models\Claim::DAMAGED_AREA_SELECT[$claim->damaged_area] ?? '' }}</p>

                <div class="card-title">
                    {{ trans('cruds.claim.fields.driver_vehicle') }}
                </div>
                <p class="card-text">{{ App\Models\Driver::find($claim->driver_vehicle)->driver_full_name ?? '' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Gegevens wederpartij
            </div>

            <div class="card-body">
                @if ($claim->opposite_type != 'obstacle')
                <div class="card-title">
                    {{ trans('cruds.claim.fields.vehicle_opposite') }}
                </div>
                <p class="card-text">{{ $claim->vehicle_opposite->name ?? '' }}</p>
            
                <div class="card-title">
                    {{ trans('cruds.claim.fields.damaged_part_opposite') }}
                </div>
                    <p class="card-text">
                        @if ( $claim->damaged_part_opposite !== null )
                            @foreach(json_decode( $claim->damaged_part_opposite ) as $part)
                                <span class="badge badge-success">{{ App\Models\Claim::DAMAGED_PART_SELECT[$part] }}</span>
                            @endforeach
                        @endif
                    </p>
            
                <div class="card-title">
                    {{ trans('cruds.claim.fields.damage_origin_opposite') }}
                </div>
                <p class="card-text">
                    @if ( $claim->damage_origin_opposite !== null )
                        @foreach(json_decode( $claim->damage_origin_opposite ) as $origin)
                            <span class="badge badge-success">{{ App\Models\Claim::DAMAGE_ORIGIN_OPPOSITE[$origin] }} </span>
                        @endforeach
                    @endif
                </p>

                <div class="card-title">
                    {{ trans('cruds.claim.fields.damaged_area_opposite') }}
                </div>
                <p class="card-text">
                    @if ( $claim->damaged_area_opposite !== null )
                        @foreach(json_decode( $claim->damaged_area_opposite ) as $area)
                            <span class="badge badge-success">{{ App\Models\Claim::DAMAGED_AREA_OPPOSITE_SELECT[$area] }} </span>
                        @endforeach
                    @endif
                </p>
                <div class="card-title">
                    {{ trans('cruds.claim.fields.driver_vehicle_opposite') }}
                </div>
                <p class="card-text">{{ App\Models\Driver::find($claim->driver_vehicle_opposite)->driver_full_name ?? '' }}</p>

                @else
                <div class="card-title">
                    {{ trans('cruds.claim.fields.obstacle') }}
                </div>
                <p class="card-text">{{ $claim->obstacle }}</p>
                @endif
            </div>
        </div>
        @if (!empty($opposite))
        <div class="card">
            <div class="card-header">
                Details wederpartij
            </div>

            <div class="card-body">
                <div class="card-title">
                    {{ trans('cruds.opposite.fields.name') }}
                </div>
                <p class="card-text">{{ $opposite->name ?? '' }}</p>

                <div class="card-title">
                    {{ trans('cruds.opposite.fields.street') }}
                </div>
                <p class="card-text">{{ $opposite->street ?? '' }}</p>

                <div class="card-title">
                    {{ trans('cruds.opposite.fields.zipcode') }} + {{ trans('cruds.opposite.fields.city') }}
                </div>
                <p class="card-text">{{ $opposite->zipcode ?? '' }} {{ $opposite->city ?? '' }}</p>

                <div class="card-title">
                    {{ trans('cruds.opposite.fields.phone') }}
                </div>
                <p class="card-text">{{ $opposite->phone ?? '' }}</p>

                <div class="card-title">
                    {{ trans('cruds.opposite.fields.email') }}
                </div>
                <p class="card-text"><a href="mailto:{{ $opposite->email ?? '' }}">{{ $opposite->email ?? '' }}</a></p>
            
            </div>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Bijlages
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.damage_files') }}
                        </div>
                        <p class="card-text">
                            @foreach($claim->damage_files as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ $media->name }}
                                </a>
                            @endforeach
                        </p>
                    </div>
                    <div class="col-md-3">
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.report_files') }}
                        </div>
                        <p class="card-text">
                            @foreach($claim->report_files as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ $media->name }}
                                </a>
                            @endforeach
                        </p>
                    </div>
                    <div class="col-md-3">
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.financial_files') }}
                        </div>
                        <p class="card-text">
                            @foreach($claim->financial_files as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ $media->name }}
                                </a>
                            @endforeach
                        </p>
                    </div>
                    <div class="col-md-3">
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.other_files') }}
                        </div>
                        <p class="card-text">
                            @foreach($claim->other_files as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ $media->name }}
                                </a>
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card recent-activities">
    <div class="card-header">
        Notities / Activiteiten
    </div>
    @foreach ($claim->claimNotes as $note)
        <div class="item">
            <div class="row">
                <div class="col-2 date-holder text-right">
                    <div class="icon"><i class="fa fa-user"></i></div>
                    <div class="date"> <span>{{ $note->user->name }}</span><br><span class="text-info">{{ $note->created_at }}</span></div>
                    </div>
                    <div class="col-10 content">
                    <h5> {{ $note->title }}</h5>
                    {!! $note->description !!}
                </div>
            </div>
        </div>
    @endforeach
    
    <div class="item">
        <div class="row">
            <div class="col-2 date-holder text-right">
                <div class="icon"><i class="fa fa-plus"></i></div>
                <div class="date">Nieuwe notitie<span></span><br><span class="text-info"></span></div>
                </div>
                <div class="col-10 content">
                    <form method="POST" action="{{ route("admin.notes.store") }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="required" for="title">{{ trans('cruds.note.fields.title') }}</label>
                            <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}" required>
                            @if($errors->has('title'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('title') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.note.fields.title_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label for="description">{{ trans('cruds.note.fields.description') }}</label>
                            <textarea class="form-control ckeditor {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{!! old('description') !!}</textarea>
                            @if($errors->has('description'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.note.fields.description_helper') }}</span>
                        </div>
                        <div class="form-group d-none">
                            <select class="form-control select2 {{ $errors->has('claims') ? 'is-invalid' : '' }}" name="claims[]" id="claims" multiple required>
                                <option value="{{ $claim->id }}" selected>{{ $claim->id }}</option>
                            </select>
            
                        </div>
                
                        @if (auth()->user()->roles->contains(1))
                          <div class="form-group d-none">
                              <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id" required>
                                <option value="{{ auth()->user()->id }}">{{ auth()->user()->name }}</option>
                              </select>
                          </div>
                        @else
                
                          <input type="hidden" name="user_id" id="user_id" value="{{ auth()->user()->id }}">
                          
                        @endif
                
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit" name="add-new-note" value='true'>
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
            </div>
        </div>
    </div>    
</div>
@if (auth()->user()->roles->contains(1))
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Kosten Schadedossier
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.damage_costs') }}
                        </div>
                        <p class="card-text">&euro; {{ $claim->damage_costs }}</p>
                    
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.recovery_costs') }}
                        </div>
                        <p class="card-text">&euro; {{ $claim->recovery_costs }}</p>
                    
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.replacement_vehicle_costs') }}
                        </div>
                        <p class="card-text">&euro; {{ $claim->replacement_vehicle_costs }}</p>
                        
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.expert_costs') }}
                        </div>
                        <p class="card-text">&euro; {{ $claim->expert_costs }}</p>
                    </div>
                    <div class="col-md-6">    
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.other_costs') }}
                        </div>
                        <p class="card-text">&euro; {{ $claim->other_costs }}</p>
                        
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.deductible_excess_costs') }}
                        </div>
                        <p class="card-text">&euro; {{ $claim->deductible_excess_costs }}</p>
                        
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.insurance_costs') }}
                        </div>
                        <p class="card-text">&euro; {{ $claim->insurance_costs }}</p>
                    </div>
                </div>
            
            </div>
        </div>
    </div>
    <div class="col-md-6 d-none">
        <div class="card">
            <div class="card-header">
                ...
            </div>

            <div class="card-body">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.damage_costs') }}
                </div>
                <p class="card-text">{{ $claim->vehicle_opposite->name ?? '' }}</p>
            
            
            </div>
        </div>
    </div>
</div>
@endif

@endsection