@extends('layouts.admin')
@section('content')

<div class="top-bar-claims form-group d-flex justify-content-between align-items-center">
    <a class="btn btn-dark" href="{{ route('admin.claims.index') }}">
        {{ trans('global.back_to_list') }}
    </a>

    @if (auth()->user()->roles->contains(1))
        @if ($claim->assign_self == true)
            <div class="alert alert-danger" role="alert">
                Let op! Deze schadedossier wordt behandeld door klant zelf.
            </div>
        @endif
    
    @endif

    <a class="btn btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
        {{ trans('global.edit') }}
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        Schadedossier overzicht <div class="btn btn-primary claim-status">{{ App\Models\Claim::STATUS_SELECT[$claim->status] ?? '' }}</div>
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
                            
                <div class="card-title">
                    {{ trans('cruds.claim.fields.recoverable_claim') }}
                </div>
                <p class="card-text">{{ App\Models\Claim::RECOVERABLE_CLAIM_SELECT[$claim->recoverable_claim] ?? '' }}</p>

                @if ( App\Models\Claim::INJURY_SELECT[$claim->injury] == 'yes' )
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.injury_office') }}
                    </div>
                    <p class="card-text">{{ $claim->injury_office->identifier ?? '' }}</p>
                @elseif ( App\Models\Claim::INJURY_SELECT[$claim->injury] == 'other' )
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.injury_other') }}
                    </div>
                    <p class="card-text">{{ $claim->injury_other }}</p>
                @endif
                
                <div class="card-title">
                    Soort schade
                </div>
                <p class="card-text">{{ $claim->damage_kind }}</p>
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
                    Naam
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
                    <p class="card-text">{{ App\Models\Claim::DAMAGED_PART_SELECT[$claim->damaged_part] ?? '' }}</p>
            
                <div class="card-title">
                    {{ trans('cruds.claim.fields.damage_origin') }}
                </div>
                <p class="card-text">{{ $claim->damage_origin }}</p>

                <div class="card-title">
                    {{ trans('cruds.claim.fields.damaged_area') }}
                </div>
                <p class="card-text">{{ App\Models\Claim::DAMAGED_AREA_SELECT[$claim->damaged_area] ?? '' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Gegevens wederpartij
            </div>

            <div class="card-body">
                <div class="card-title">
                    {{ trans('cruds.claim.fields.vehicle_opposite') }}
                </div>
                <p class="card-text">{{ $claim->vehicle_opposite->name ?? '' }}</p>
            
                <div class="card-title">
                    {{ trans('cruds.claim.fields.damaged_part_opposite') }}
                </div>
                    <p class="card-text">{{ App\Models\Claim::DAMAGED_PART_OPPOSITE_SELECT[$claim->damaged_part_opposite] ?? '' }}</p>
            
                <div class="card-title">
                    {{ trans('cruds.claim.fields.damage_origin_opposite') }}
                </div>
                <p class="card-text">{{ $claim->damage_origin_opposite }}</p>

                <div class="card-title">
                    {{ trans('cruds.claim.fields.damaged_area_opposite') }}
                </div>
                <p class="card-text">{{ App\Models\Claim::DAMAGED_AREA_OPPOSITE_SELECT[$claim->damaged_area_opposite] ?? '' }}</p>
            </div>
        </div>
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

    <div class="card-footer text-muted">
        Footer
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
    <div class="col-md-6">
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

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.claim.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.claims.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.id') }}
                        </th>
                        <td>
                            {{ $claim->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.company') }}
                        </th>
                        <td>
                            {{ $claim->company->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.assign_self') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $claim->assign_self ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.subject') }}
                        </th>
                        <td>
                            {{ $claim->subject }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.claim_number') }}
                        </th>
                        <td>
                            {{ $claim->claim_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.status') }}
                        </th>
                        <td>
                            
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.injury') }}
                        </th>
                        <td>
                            {{ App\Models\Claim::INJURY_SELECT[$claim->injury] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.contact_lawyer') }}
                        </th>
                        <td>
                            {{ App\Models\Claim::CONTACT_LAWYER_SELECT[$claim->contact_lawyer] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.date_accident') }}
                        </th>
                        <td>
                            {{ $claim->date_accident }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.recoverable_claim') }}
                        </th>
                        <td>
                            {{ App\Models\Claim::RECOVERABLE_CLAIM_SELECT[$claim->recoverable_claim] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.injury_other') }}
                        </th>
                        <td>
                            {{ $claim->injury_other }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.injury_office') }}
                        </th>
                        <td>
                            {{ $claim->injury_office->identifier ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.vehicle') }}
                        </th>
                        <td>
                            {{ $claim->vehicle->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.vehicle_opposite') }}
                        </th>
                        <td>
                            {{ $claim->vehicle_opposite->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.opposite_type') }}
                        </th>
                        <td>
                            {{ App\Models\Claim::OPPOSITE_TYPE_SELECT[$claim->opposite_type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.damaged_part') }}
                        </th>
                        <td>
                            {{ App\Models\Claim::DAMAGED_PART_SELECT[$claim->damaged_part] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.damage_origin') }}
                        </th>
                        <td>
                            {{ $claim->damage_origin }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.damaged_area') }}
                        </th>
                        <td>
                            {{ App\Models\Claim::DAMAGED_AREA_SELECT[$claim->damaged_area] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.damaged_part_opposite') }}
                        </th>
                        <td>
                            {{ App\Models\Claim::DAMAGED_PART_OPPOSITE_SELECT[$claim->damaged_part_opposite] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.damage_origin_opposite') }}
                        </th>
                        <td>
                            {{ $claim->damage_origin_opposite }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.damaged_area_opposite') }}
                        </th>
                        <td>
                            {{ App\Models\Claim::DAMAGED_AREA_OPPOSITE_SELECT[$claim->damaged_area_opposite] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.recovery_office') }}
                        </th>
                        <td>
                            {{ $claim->recovery_office->identifier ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.damage_costs') }}
                        </th>
                        <td>
                            {{ $claim->damage_costs }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.recovery_costs') }}
                        </th>
                        <td>
                            {{ $claim->recovery_costs }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.replacement_vehicle_costs') }}
                        </th>
                        <td>
                            {{ $claim->replacement_vehicle_costs }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.expert_costs') }}
                        </th>
                        <td>
                            {{ $claim->expert_costs }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.other_costs') }}
                        </th>
                        <td>
                            {{ $claim->other_costs }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.deductible_excess_costs') }}
                        </th>
                        <td>
                            {{ $claim->deductible_excess_costs }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.insurance_costs') }}
                        </th>
                        <td>
                            {{ $claim->insurance_costs }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.expertise_office') }}
                        </th>
                        <td>
                            {{ $claim->expertise_office->identifier ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.expert_report_is_in') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $claim->expert_report_is_in ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.requested_at') }}
                        </th>
                        <td>
                            {{ $claim->requested_at }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.report_received_at') }}
                        </th>
                        <td>
                            {{ $claim->report_received_at }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.damage_files') }}
                        </th>
                        <td>
                            @foreach($claim->damage_files as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.report_files') }}
                        </th>
                        <td>
                            @foreach($claim->report_files as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.financial_files') }}
                        </th>
                        <td>
                            @foreach($claim->financial_files as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.claim.fields.other_files') }}
                        </th>
                        <td>
                            @foreach($claim->other_files as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.claims.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#claim_notes" role="tab" data-toggle="tab">
                {{ trans('cruds.note.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="claim_notes">
            @includeIf('admin.claims.relationships.claimNotes', ['notes' => $claim->claimNotes])
        </div>
    </div>
</div>

@endsection