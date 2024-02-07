@extends('layouts.admin')
@section('content')

@php 

    $isAdmin = auth()->user()->roles->contains(1);

@endphp


<form method="POST" action="{{ route("admin.claims.update", [$claim->id]) }}" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <div class="card">

        <div class="card-header">
            Dossier informatie
        </div>

        <div class="card-body">
            @csrf
            @if (auth()->user()->roles->contains(1))
            <div class="form-group">
                <label class="required" for="company_id">{{ trans('cruds.claim.fields.company') }}</label>
                <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="company_id" id="company_id" required>
                    @foreach($companies as $id => $entry)
                        <option value="{{ $id }}" {{ (old('company_id') ? old('company_id') : $claim->company->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('company'))
                    <div class="invalid-feedback">
                        {{ $errors->first('company') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.company_helper') }}</span>
            </div>
            @else
                <input type="hidden" name="company_id" id="company_id" value="{{ $claim->company->id }}">

            @endif
            <div class="form-group">
                <label class="required" for="subject">{{ trans('cruds.claim.fields.subject') }}</label>
                <input class="form-control {{ $errors->has('subject') ? 'is-invalid' : '' }}" type="text" name="subject" id="subject" value="{{ old('subject', $claim->subject) }}" required>
                @if($errors->has('subject'))
                    <div class="invalid-feedback">
                        {{ $errors->first('subject') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.subject_helper') }}</span>
            </div>
            <div class="form-group d-none">
                <label class="required" for="claim_number">{{ trans('cruds.claim.fields.claim_number') }}</label>
                <input class="form-control {{ $errors->has('claim_number') ? 'is-invalid' : '' }}" type="text" name="claim_number" id="claim_number" value="{{ old('claim_number', $claim->claim_number) }}" required readonly>
                @if($errors->has('claim_number'))
                    <div class="invalid-feedback">
                        {{ $errors->first('claim_number') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.claim_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="opposite_claim_no">{{ trans('cruds.claim.fields.opposite_claim_no') }}</label>
                <input class="form-control {{ $errors->has('opposite_claim_no') ? 'is-invalid' : '' }}" type="text" name="opposite_claim_no" id="opposite_claim_no" value="{{ old('opposite_claim_no', $claim->opposite_claim_no ) }}">
                @if($errors->has('opposite_claim_no'))
                    <div class="invalid-feedback">
                        {{ $errors->first('opposite_claim_no') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.opposite_claim_no_helper') }}</span>
            </div>
            <label>{{ trans('cruds.claim.fields.assignee') }}</label>
                <select class="form-control {{ $errors->has('assignee_id') ? 'is-invalid' : '' }}" name="assignee_id" id="assignee_id">
                    <option value disabled {{ old('assignee_id', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach($assignee_options as $assignee)
                        <option value="{{ $assignee->id }}" {{ (old('assignee_id') ? old('assignee_id') : $claim->assignee_id ?? '') == $assignee->id ? 'selected' : '' }}>{{ $assignee->name }}</option>
                    @endforeach
                </select>
                @if($errors->has('assignee'))
                    <div class="invalid-feedback">
                        {{ $errors->first('assignee') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.assignee_helper') }}</span>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header">
            Schademelding
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <div class="form-check {{ $errors->has('assign_self') ? 'is-invalid' : '' }}">
                        <input type="hidden" name="assign_self" value="0">
                        <input class="form-check-input" type="checkbox" name="assign_self" id="assign_self" value="1" {{ $claim->assign_self || old('assign_self', 0) === 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="assign_self">{{ trans('cruds.claim.fields.assign_self') }}</label>
                    </div>
                    @if($errors->has('assign_self'))
                        <div class="invalid-feedback">
                            {{ $errors->first('assign_self') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.assign_self_helper') }}</span>
                </div>
                <div class="form-group col-md-6">
                    <label for="date_accident">{{ trans('cruds.claim.fields.date_accident') }}</label>
                <input class="form-control date {{ $errors->has('date_accident') ? 'is-invalid' : '' }}" type="text" name="date_accident" id="date_accident" value="{{ old('date_accident', $claim->date_accident) }}">
                @if($errors->has('date_accident'))
                    <div class="invalid-feedback">
                        {{ $errors->first('date_accident') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.date_accident_helper') }}</span>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>{{ trans('cruds.claim.fields.injury') }}</label>
                <select class="form-control {{ $errors->has('injury') ? 'is-invalid' : '' }}" name="injury" id="injury">
                    <option value disabled {{ old('injury', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Claim::INJURY_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('injury', $claim->injury) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('injury'))
                    <div class="invalid-feedback">
                        {{ $errors->first('injury') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.injury_helper') }}</span>
                </div>
                <div class="form-group col-md-6 injury-office-show d-none">
                    <label for="injury_office_id">{{ trans('cruds.claim.fields.injury_office') }}</label>
                <select class="form-control select2 {{ $errors->has('injury_office') ? 'is-invalid' : '' }}" name="injury_office_id" id="injury_office_id">
                    @foreach($injury_offices as $id => $entry)
                        <option value="{{ $id }}" {{ (old('injury_office_id') ? old('injury_office_id') : $claim->injury_office->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('injury_office'))
                    <div class="invalid-feedback">
                        {{ $errors->first('injury_office') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.injury_office_helper') }}</span>
                </div>
                <div class="form-group col-md-9 injury-other-show d-none">
                    <label for="injury_other">{{ trans('cruds.claim.fields.injury_other') }}</label>
                <input class="form-control {{ $errors->has('injury_other') ? 'is-invalid' : '' }}" type="text" name="injury_other" id="injury_other" value="{{ old('injury_other', $claim->injury_other) }}">
                @if($errors->has('injury_other'))
                    <div class="invalid-feedback">
                        {{ $errors->first('injury_other') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.injury_other_helper') }}</span>
                </div>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.claim.fields.contact_lawyer') }}</label>
                <select class="form-control {{ $errors->has('contact_lawyer') ? 'is-invalid' : '' }}" name="contact_lawyer" id="contact_lawyer">
                    <option value disabled {{ old('contact_lawyer', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Claim::CONTACT_LAWYER_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('contact_lawyer', $claim->contact_lawyer) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('contact_lawyer'))
                    <div class="invalid-feedback">
                        {{ $errors->first('contact_lawyer') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.contact_lawyer_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.claim.fields.recoverable_claim') }}</label>
                <select class="form-control {{ $errors->has('recoverable_claim') ? 'is-invalid' : '' }}" name="recoverable_claim" id="recoverable_claim">
                    <option value disabled {{ old('recoverable_claim', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Claim::RECOVERABLE_CLAIM_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('recoverable_claim', $claim->recoverable_claim) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('recoverable_claim'))
                    <div class="invalid-feedback">
                        {{ $errors->first('recoverable_claim') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.recoverable_claim_helper') }}</span>
            </div>
            <div class="form-group">
                <label>Soort schade</label>
                <select class="form-control {{ $errors->has('damage_kind') ? 'is-invalid' : '' }}" name="damage_kind" id="damage_kind">
                    <option value disabled {{ old('damage_kind', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Claim::DAMAGE_KIND as $key => $label)
                        <option value="{{ $key }}" {{ old('damage_kind', $claim->damage_kind) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                    @if($errors->has('damage_kind'))
                        <div class="invalid-feedback">
                            {{ $errors->first('damage_kind') }}
                        </div>
                    @endif
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Gegevens wagenpark
        </div>
        <div class="card-body">
            <input type="hidden" name="vehicle_id" value="1"/>
            
            <div class="form-group">
                <small>Gelieve het kenteken in te vullen met streepjes, bijv.: "XX-123-XX"</small>
                <br/>
                <label for="vehicle_plates">{{ trans('cruds.claim.fields.vehicle_plates') }}</label>
                
                <select class="form-control select2 {{ $errors->has('vehicle') ? 'is-invalid' : '' }}" name="vehicle_plates" id="vehicle_plates">
                    @foreach($vehicle as $id => $entry)
                        <option value="{{ $id }}" {{ (old('vehicle') ? old('vehicle') : $claim->vehicle->plates ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('vehicle'))
                    <div class="invalid-feedback">
                        {{ $errors->first('vehicle') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.vehicle_plates_helper') }}</span>
            </div>


            <div class="form-group">

                <label for="driver_vehicle">{{ trans('cruds.claim.fields.driver_vehicle') }}</label>
                <select class="form-control select2 {{ $errors->has('driver_vehicle') ? 'is-invalid' : '' }}" name="driver_vehicle" id="driver_vehicle">
                    @foreach($drivers as $id => $entry)
                        <option value="{{ $id }}" {{ (old('driver_vehicle') ? old('driver_vehicle') : $claim->driver_vehicle ?? '' ) == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('driver_vehicle'))
                    <div class="invalid-feedback">
                        {{ $errors->first('driver_vehicle') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.driver_vehicle_helper') }}</span>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>{{ trans('cruds.claim.fields.damaged_part') }}</label>
                    <select class="form-control select2 {{ $errors->has('damaged_part') ? 'is-invalid' : '' }}" name="damaged_part[]" id="damaged_part" aria-label="multiple select" multiple>
                        @foreach(App\Models\Claim::DAMAGED_PART_SELECT as $key => $label)
                            @if ( $claim->damaged_part !== null )
                            <option value="{{ $key }}" {{ in_array($key, json_decode( $claim->damaged_part )) ? 'selected' : '' }}>{{ $label }}</option>
                            @else
                            <option value="{{ $key }}" {{ old('damaged_part', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                    @if($errors->has('damaged_part'))
                        <div class="invalid-feedback">
                            {{ $errors->first('damaged_part') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.damaged_part_helper') }}</span>
                </div>
                <div class="form-group col-md-6">
                    <label>{{ trans('cruds.claim.fields.damaged_area') }}</label>
                    @php is_array($claim->damaged_area) ? $claim->damaged_area : array(); @endphp
                    <select class="form-control select2 {{ $errors->has('damaged_area') ? 'is-invalid' : '' }}" name="damaged_area[]" id="damaged_area" aria-label="multiple select" multiple>
                        @foreach(App\Models\Claim::DAMAGED_AREA_SELECT as $key => $label)
                            @if ( $claim->damaged_area !== null )
                            <option value="{{ $key }}" {{ in_array($key, json_decode( $claim->damaged_area )) ? 'selected' : '' }}>{{ $label }}</option>
                            @else
                            <option value="{{ $key }}" {{ old('damaged_area', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                    @if($errors->has('damaged_area'))
                        <div class="invalid-feedback">
                            {{ $errors->first('damaged_area') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.damaged_area_helper') }}</span>
                </div>
            </div>
            <div class="form-group">
                <label for="damage_origin">{{ trans('cruds.claim.fields.damage_origin') }}</label>
                <select class="form-control select2 {{ $errors->has('damage_origin') ? 'is-invalid' : '' }}" name="damage_origin[]" id="damage_origin" multiple>
                    @foreach(App\Models\Claim::DAMAGE_ORIGIN as $key => $label)
                        @if ( $claim->damage_origin !== null )
                        <option value="{{ $key }}" {{ in_array($key, json_decode( $claim->damage_origin )) ? 'selected' : '' }}>{{ $label }}</option>
                        @else
                        <option value="{{ $key }}" {{ old('damage_origin', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
                @if($errors->has('damage_origin'))
                    <div class="invalid-feedback">
                        {{ $errors->first('damage_origin') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.damage_origin_helper') }}</span>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Gegevens wederpartij
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>{{ trans('cruds.claim.fields.opposite_type') }}</label>
                    <select class="form-control {{ $errors->has('opposite_type') ? 'is-invalid' : '' }}" name="opposite_type" id="opposite_type">
                        <option value=false disabled {{ old('opposite_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Claim::OPPOSITE_TYPE_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('opposite_type', $claim->opposite_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('opposite_type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('opposite_type') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.opposite_type_helper') }}</span>
                </div>
            </div>

            <div class="obstacle-show d-none">
                <div class="form-group">
                    <label for="obstacle">{{ trans('cruds.claim.fields.obstacle') }}</label>
                    <input class="form-control {{ $errors->has('obstacle') ? 'is-invalid' : '' }}" type="text" name="obstacle" id="obstacle" value="{{ old('obstacle', $claim->obstacle) }}">
                    @if($errors->has('obstacle'))
                        <div class="invalid-feedback">
                            {{ $errors->first('obstacle') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.obstacle_helper') }}</span>
                </div>
            </div>
            
            <div class="opposite-vehicle-show d-none">
                <div class="form-group">
                    <label for="vehicle_plates_opposite">{{ trans('cruds.claim.fields.vehicle_plates_opposite') }}</label>
                    <input class="form-control {{ $errors->has('vehicle_plates_opposite') ? 'is-invalid' : '' }}" type="text" name="vehicle_plates_opposite" id="vehicle_plates_opposite" value="{{ old('vehicle_plates_opposite', $claim->vehicle_opposite->plates ?? null) }}">
                    @if($errors->has('vehicle_plates_opposite'))
                        <div class="invalid-feedback">
                            {{ $errors->first('vehicle_plates_opposite') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.vehicle_plates_opposite_helper') }}</span>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>{{ trans('cruds.claim.fields.damaged_part_opposite') }}</label>
                    <select class="form-control select2{{ $errors->has('damaged_part_opposite') ? 'is-invalid' : '' }}" name="damaged_part_opposite[]" id="damaged_part_opposite" multiple>
                        @foreach(App\Models\Claim::DAMAGED_PART_OPPOSITE_SELECT as $key => $label)
                            @if ( $claim->damaged_part_opposite !== null )
                            <option value="{{ $key }}" {{ in_array($key, json_decode( $claim->damaged_part_opposite )) ? 'selected' : '' }}>{{ $label }}</option>
                            @else
                            <option value="{{ $key }}" {{ old('damaged_part_opposite', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                    @if($errors->has('damaged_part_opposite'))
                        <div class="invalid-feedback">
                            {{ $errors->first('damaged_part_opposite') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.damaged_part_opposite_helper') }}</span>
                    </div>
                    <div class="form-group col-md-6">
                        
                        <label>{{ trans('cruds.claim.fields.damaged_area_opposite') }}</label>
                    <select class="form-control select2 {{ $errors->has('damaged_area_opposite') ? 'is-invalid' : '' }}" name="damaged_area_opposite[]" id="damaged_area_opposite" multiple>
                        @foreach(App\Models\Claim::DAMAGED_AREA_OPPOSITE_SELECT as $key => $label)
                        @if ( $claim->damaged_area_opposite !== null )    
                            <option value="{{ $key }}" {{ in_array($key, json_decode( $claim->damaged_area_opposite )) ? 'selected' : '' }}>{{ $label }}</option>
                            @else
                            <option value="{{ $key }}" {{ old('damaged_area_opposite', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                    @if($errors->has('damaged_area_opposite'))
                        <div class="invalid-feedback">
                            {{ $errors->first('damaged_area_opposite') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.damaged_area_opposite_helper') }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="damage_origin_opposite">{{ trans('cruds.claim.fields.damage_origin_opposite') }}</label>
                        <select class="form-control select2 {{ $errors->has('damage_origin_opposite') ? 'is-invalid' : '' }}" name="damage_origin_opposite[]" id="damage_origin_opposite" multiple>
                            @foreach(App\Models\Claim::DAMAGE_ORIGIN_OPPOSITE as $key => $label)
                                @if ( $claim->damage_origin_opposite !== null )    
                                <option value="{{ $key }}" {{ in_array($key, json_decode( $claim->damage_origin_opposite )) ? 'selected' : '' }}>{{ $label }}</option>
                                @else
                                <option value="{{ $key }}" {{ old('damage_origin_opposite', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if($errors->has('damage_origin_opposite'))
                            <div class="invalid-feedback">
                                {{ $errors->first('damage_origin_opposite') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.claim.fields.damage_origin_opposite_helper') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Details wederpartij
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>{{ trans('cruds.opposite.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('op_name') ? 'is-invalid' : '' }}" type="text" name="op_name" id="op_name" value="{{ old('op_name', $opposite->name ?? '') }}">
                </div>
                <div class="form-group col-md-6">
                    <label>{{ trans('cruds.opposite.fields.street') }}</label>
                    <input class="form-control {{ $errors->has('op_name') ? 'is-invalid' : '' }}" type="text" name="op_street" id="op_street" value="{{ old('op_street', $opposite->street ?? '') }}">
                </div>
                <div class="form-group col-md-6">
                    <label>{{ trans('cruds.opposite.fields.zipcode') }}</label>
                    <input class="form-control {{ $errors->has('op_name') ? 'is-invalid' : '' }}" type="text" name="op_zipcode" id="op_zipcode" value="{{ old('op_zipcode', $opposite->zipcode ?? '') }}">
                </div>
                <div class="form-group col-md-6">
                    <label>{{ trans('cruds.opposite.fields.city') }}</label>
                    <input class="form-control {{ $errors->has('op_name') ? 'is-invalid' : '' }}" type="text" name="op_city" id="op_city" value="{{ old('op_city', $opposite->city ?? '') }}">
                </div>
                <div class="form-group col-md-6">
                    <label>{{ trans('cruds.opposite.fields.country') }}</label>
                    <input class="form-control {{ $errors->has('op_name') ? 'is-invalid' : '' }}" type="text" name="op_country" id="op_country" value="{{ old('op_country', $opposite->country ?? '') }}">
                </div>
                <div class="form-group col-md-6">
                    <label>{{ trans('cruds.opposite.fields.phone') }}</label>
                    <input class="form-control {{ $errors->has('op_name') ? 'is-invalid' : '' }}" type="text" name="op_phone" id="op_phone" value="{{ old('op_phone', $opposite->phone ?? '') }}">
                </div>
                <div class="form-group col-md-6">
                    <label>{{ trans('cruds.opposite.fields.email') }}</label>
                    <input class="form-control {{ $errors->has('op_name') ? 'is-invalid' : '' }}" type="email" name="op_email" id="op_email" value="{{ old('op_email', $opposite->email ?? '') }}">
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Schadehersteller
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="recovery_office_id">{{ trans('cruds.claim.fields.recovery_office') }}</label>
                <select class="form-control select2 {{ $errors->has('recovery_office') ? 'is-invalid' : '' }}" name="recovery_office_id" id="recovery_office_id">
                    @foreach($recovery_offices as $id => $entry)
                        <option value="{{ $id }}" {{ (old('recovery_office_id') ? old('recovery_office_id') : $claim->recovery_office->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('recovery_office'))
                    <div class="invalid-feedback">
                        {{ $errors->first('recovery_office') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.recovery_office_helper') }}</span>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Expertise
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="expertise_office_id">{{ trans('cruds.claim.fields.expertise_office') }}</label>
                <select class="form-control select2 {{ $errors->has('expertise_office') ? 'is-invalid' : '' }}" name="expertise_office_id" id="expertise_office_id">
                    @foreach($expertise_offices as $id => $entry)
                        <option value="{{ $id }}" {{ (old('expertise_office_id') ? old('expertise_office_id') : $claim->expertise_office->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('expertise_office'))
                    <div class="invalid-feedback">
                        {{ $errors->first('expertise_office') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.expertise_office_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="requested_at">{{ trans('cruds.claim.fields.requested_at') }}</label>
                <input class="form-control date {{ $errors->has('requested_at') ? 'is-invalid' : '' }}" type="text" name="requested_at" id="requested_at" value="{{ old('requested_at', $claim->requested_at) }}">
                @if($errors->has('requested_at'))
                    <div class="invalid-feedback">
                        {{ $errors->first('requested_at') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.requested_at_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('expert_report_is_in') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="expert_report_is_in" value="0">
                    <input class="form-check-input" type="checkbox" name="expert_report_is_in" id="expert_report_is_in" value="1" {{ $claim->expert_report_is_in || old('expert_report_is_in', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="expert_report_is_in">{{ trans('cruds.claim.fields.expert_report_is_in') }}</label>
                </div>
                @if($errors->has('expert_report_is_in'))
                    <div class="invalid-feedback">
                        {{ $errors->first('expert_report_is_in') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.expert_report_is_in_helper') }}</span>
            </div>
            <div class="form-group expertise-report-show d-none">
                <label for="report_received_at">{{ trans('cruds.claim.fields.report_received_at') }}</label>
                <input class="form-control date {{ $errors->has('report_received_at') ? 'is-invalid' : '' }}" type="text" name="report_received_at" id="report_received_at" value="{{ old('report_received_at', $claim->report_received_at) }}">
                @if($errors->has('report_received_at'))
                    <div class="invalid-feedback">
                        {{ $errors->first('report_received_at') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.report_received_at_helper') }}</span>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Financieel
        </div>
        <div class="card-body">
            <div class="form-row">
                @if ($isAdmin)
                <div class="form-group col-md-3">
                    <label>{{ trans('cruds.claim.fields.invoice_settlement') }}</label>
                    <input type="hidden" name="invoice_settlement_asp" value="0">
                    <input class="form-control {{ $errors->has('invoice_settlement_asp') ? 'is-invalid' : '' }}" type="checkbox" name="invoice_settlement_asp" id="invoice_settlement_asp" value="1" {{ $claim->invoice_settlement_asp || old('invoice_settlement_asp', 0) === 1 ? 'checked' : '' }}>
                </div>
                <div class="form-group col-md-9">
                    <label for="invoice_comment">{{ trans('cruds.claim.fields.invoice_comment') }}</label>
                    <input class="form-control {{ $errors->has('invoice_comment') ? 'is-invalid' : '' }}" type="text" name="invoice_comment" id="invoice_comment" value="{{ old('invoice_comment', $claim->invoice_comment ) }}">
                    @if($errors->has('invoice_comment'))
                        <div class="invalid-feedback">
                            {{ $errors->first('invoice_comment') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.invoice_comment_helper') }}</span>
                </div>
                @endif
                <div class="form-group col-md-6">
                    <label for="deductible_excess_costs">{{ trans('cruds.claim.fields.deductible_excess_costs') }}</label>
                    <input class="form-control {{ $errors->has('deductible_excess_costs') ? 'is-invalid' : '' }}" type="number" name="deductible_excess_costs" id="deductible_excess_costs" value="{{ old('deductible_excess_costs', $claim->deductible_excess_costs ) }}" step="0.01">
                    @if($errors->has('deductible_excess_costs'))
                        <div class="invalid-feedback">
                            {{ $errors->first('deductible_excess_costs') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.deductible_excess_costs_helper') }}</span>
                </div>
                <div class="form-group col-md-6">
                    <label for="insurance_costs">{{ trans('cruds.claim.fields.insurance_costs') }}</label>
                    <input class="form-control {{ $errors->has('insurance_costs') ? 'is-invalid' : '' }}" type="number" name="insurance_costs" id="insurance_costs" value="{{ old('insurance_costs', $claim->insurance_costs ) }}" step="0.01">
                    @if($errors->has('insurance_costs'))
                        <div class="invalid-feedback">
                            {{ $errors->first('insurance_costs') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.insurance_costs_helper') }}</span>
                </div>
                <div class="form-group col-md-6">
                    <label for="expert_costs">{{ trans('cruds.claim.fields.expert_costs') }}</label>
                    <input class="form-control {{ $errors->has('expert_costs') ? 'is-invalid' : '' }}" type="number" name="expert_costs" id="expert_costs" value="{{ old('expert_costs', $claim->expert_costs ) }}" step="0.01">
                    @if($errors->has('expert_costs'))
                        <div class="invalid-feedback">
                            {{ $errors->first('expert_costs') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.expert_costs_helper') }}</span>
                </div>
                <div class="form-group col-md-6">
                    <label for="damage_costs">{{ trans('cruds.claim.fields.damage_costs') }}</label>
                    <input class="form-control {{ $errors->has('damage_costs') ? 'is-invalid' : '' }}" type="number" name="damage_costs" id="damage_costs" value="{{ old('damage_costs', $claim->damage_costs ) }}" step="0.01">
                    @if($errors->has('damage_costs'))
                        <div class="invalid-feedback">
                            {{ $errors->first('damage_costs') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.damage_costs_helper') }}</span>
                </div>
                <div class="form-group col-md-6">
                    <label for="recovery_costs">{{ trans('cruds.claim.fields.recovery_costs') }}</label>
                    <input class="form-control {{ $errors->has('recovery_costs') ? 'is-invalid' : '' }}" type="number" name="recovery_costs" id="recovery_costs" value="{{ old('recovery_costs', $claim->recovery_costs ) }}" step="0.01">
                    @if($errors->has('recovery_costs'))
                        <div class="invalid-feedback">
                            {{ $errors->first('recovery_costs') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.recovery_costs_helper') }}</span>
                </div>
                <div class="form-group col-md-6">
                    <label for="replacement_vehicle_costs">{{ trans('cruds.claim.fields.replacement_vehicle_costs') }}</label>
                    <input class="form-control {{ $errors->has('replacement_vehicle_costs') ? 'is-invalid' : '' }}" type="number" name="replacement_vehicle_costs" id="replacement_vehicle_costs" value="{{ old('replacement_vehicle_costs', $claim->replacement_vehicle_costs ) }}" step="0.01">
                    @if($errors->has('replacement_vehicle_costs'))
                        <div class="invalid-feedback">
                            {{ $errors->first('replacement_vehicle_costs') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.replacement_vehicle_costs_helper') }}</span>
                </div>
                <div class="form-group col-md-6">
                    <label for="other_costs">{{ trans('cruds.claim.fields.other_costs') }}</label>
                    <input class="form-control {{ $errors->has('other_costs') ? 'is-invalid' : '' }}" type="number" name="other_costs" id="other_costs" value="{{ old('other_costs', $claim->other_costs ) }}" step="0.01">
                    @if($errors->has('other_costs'))
                        <div class="invalid-feedback">
                            {{ $errors->first('other_costs') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.claim.fields.other_costs_helper') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Bijlages
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="report_files">{{ trans('cruds.claim.fields.report_files') }}</label>
                <div class="needsclick dropzone {{ $errors->has('report_files') ? 'is-invalid' : '' }}" id="report_files-dropzone">
                </div>
                @if($errors->has('report_files'))
                    <div class="invalid-feedback">
                        {{ $errors->first('report_files') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.report_files_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="financial_files">{{ trans('cruds.claim.fields.financial_files') }}</label>
                <div class="needsclick dropzone {{ $errors->has('financial_files') ? 'is-invalid' : '' }}" id="financial_files-dropzone">
                </div>
                @if($errors->has('financial_files'))
                    <div class="invalid-feedback">
                        {{ $errors->first('financial_files') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.financial_files_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="damage_files">{{ trans('cruds.claim.fields.damage_files') }}</label>
                <div class="needsclick dropzone {{ $errors->has('damage_files') ? 'is-invalid' : '' }}" id="damage_files-dropzone">
                </div>
                @if($errors->has('damage_files'))
                    <div class="invalid-feedback">
                        {{ $errors->first('damage_files') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.damage_files_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="other_files">{{ trans('cruds.claim.fields.other_files') }}</label>
                <div class="needsclick dropzone {{ $errors->has('other_files') ? 'is-invalid' : '' }}" id="other_files-dropzone">
                </div>
                @if($errors->has('other_files'))
                    <div class="invalid-feedback">
                        {{ $errors->first('other_files') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.other_files_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div



@endsection

@section('scripts')
<script>
    var uploadedDamageFilesMap = {}
Dropzone.options.damageFilesDropzone = {
    url: '{{ route('admin.claims.storeMedia') }}',
    maxFilesize: 5, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 5
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="damage_files[]" value="' + response.name + '">')
      uploadedDamageFilesMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedDamageFilesMap[file.name]
      }
      $('form').find('input[name="damage_files[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($claim) && $claim->damage_files)
          var files =
            {!! json_encode($claim->damage_files) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="damage_files[]" value="' + file.file_name + '">')
            }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
<script>
    var uploadedReportFilesMap = {}
Dropzone.options.reportFilesDropzone = {
    url: '{{ route('admin.claims.storeMedia') }}',
    maxFilesize: 5, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 5
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="report_files[]" value="' + response.name + '">')
      uploadedReportFilesMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedReportFilesMap[file.name]
      }
      $('form').find('input[name="report_files[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($claim) && $claim->report_files)
          var files =
            {!! json_encode($claim->report_files) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="report_files[]" value="' + file.file_name + '">')
            }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
<script>
    var uploadedFinancialFilesMap = {}
Dropzone.options.financialFilesDropzone = {
    url: '{{ route('admin.claims.storeMedia') }}',
    maxFilesize: 5, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 5
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="financial_files[]" value="' + response.name + '">')
      uploadedFinancialFilesMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedFinancialFilesMap[file.name]
      }
      $('form').find('input[name="financial_files[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($claim) && $claim->financial_files)
          var files =
            {!! json_encode($claim->financial_files) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="financial_files[]" value="' + file.file_name + '">')
            }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
<script>
    var uploadedOtherFilesMap = {}
Dropzone.options.otherFilesDropzone = {
    url: '{{ route('admin.claims.storeMedia') }}',
    maxFilesize: 5, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 5
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="other_files[]" value="' + response.name + '">')
      uploadedOtherFilesMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedOtherFilesMap[file.name]
      }
      $('form').find('input[name="other_files[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($claim) && $claim->other_files)
          var files =
            {!! json_encode($claim->other_files) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="other_files[]" value="' + file.file_name + '">')
            }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends it's own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>
@endsection