@extends('layouts.admin')
@section('content')

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
                            {{ App\Models\Claim::STATUS_SELECT[$claim->status] ?? '' }}
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
                            {{ $claim->damaged_part }}
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
                            {{ $claim->damaged_part_opposite }}
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