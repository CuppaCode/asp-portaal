@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.claim.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.claims.update", [$claim->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="company_id">{{ trans('cruds.claim.fields.company') }}</label>
                <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="company_id" id="company_id" required>
                    @foreach($companies as $id => $entry)
                        <option value="{{ $id }}" {{ (old('company_id') ? old('company_id') : $claim->company->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('company'))
                    <span class="text-danger">{{ $errors->first('company') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.company_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('assign_self') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="assign_self" value="0">
                    <input class="form-check-input" type="checkbox" name="assign_self" id="assign_self" value="1" {{ $claim->assign_self || old('assign_self', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="assign_self">{{ trans('cruds.claim.fields.assign_self') }}</label>
                </div>
                @if($errors->has('assign_self'))
                    <span class="text-danger">{{ $errors->first('assign_self') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.assign_self_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="subject">{{ trans('cruds.claim.fields.subject') }}</label>
                <input class="form-control {{ $errors->has('subject') ? 'is-invalid' : '' }}" type="text" name="subject" id="subject" value="{{ old('subject', $claim->subject) }}" required>
                @if($errors->has('subject'))
                    <span class="text-danger">{{ $errors->first('subject') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.subject_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="claim_number">{{ trans('cruds.claim.fields.claim_number') }}</label>
                <input class="form-control {{ $errors->has('claim_number') ? 'is-invalid' : '' }}" type="text" name="claim_number" id="claim_number" value="{{ old('claim_number', $claim->claim_number) }}" required>
                @if($errors->has('claim_number'))
                    <span class="text-danger">{{ $errors->first('claim_number') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.claim_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.claim.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Claim::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $claim->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.status_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.claim.fields.injury') }}</label>
                <select class="form-control {{ $errors->has('injury') ? 'is-invalid' : '' }}" name="injury" id="injury" required>
                    <option value disabled {{ old('injury', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Claim::INJURY_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('injury', $claim->injury) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('injury'))
                    <span class="text-danger">{{ $errors->first('injury') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.injury_helper') }}</span>
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
                    <span class="text-danger">{{ $errors->first('contact_lawyer') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.contact_lawyer_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="injury_other">{{ trans('cruds.claim.fields.injury_other') }}</label>
                <input class="form-control {{ $errors->has('injury_other') ? 'is-invalid' : '' }}" type="text" name="injury_other" id="injury_other" value="{{ old('injury_other', $claim->injury_other) }}">
                @if($errors->has('injury_other'))
                    <span class="text-danger">{{ $errors->first('injury_other') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.injury_other_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="injury_office_id">{{ trans('cruds.claim.fields.injury_office') }}</label>
                <select class="form-control select2 {{ $errors->has('injury_office') ? 'is-invalid' : '' }}" name="injury_office_id" id="injury_office_id">
                    @foreach($injury_offices as $id => $entry)
                        <option value="{{ $id }}" {{ (old('injury_office_id') ? old('injury_office_id') : $claim->injury_office->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('injury_office'))
                    <span class="text-danger">{{ $errors->first('injury_office') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.injury_office_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="vehicle_id">{{ trans('cruds.claim.fields.vehicle') }}</label>
                <select class="form-control select2 {{ $errors->has('vehicle') ? 'is-invalid' : '' }}" name="vehicle_id" id="vehicle_id" required>
                    @foreach($vehicles as $id => $entry)
                        <option value="{{ $id }}" {{ (old('vehicle_id') ? old('vehicle_id') : $claim->vehicle->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('vehicle'))
                    <span class="text-danger">{{ $errors->first('vehicle') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.vehicle_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="vehicle_opposite_id">{{ trans('cruds.claim.fields.vehicle_opposite') }}</label>
                <select class="form-control select2 {{ $errors->has('vehicle_opposite') ? 'is-invalid' : '' }}" name="vehicle_opposite_id" id="vehicle_opposite_id">
                    @foreach($vehicle_opposites as $id => $entry)
                        <option value="{{ $id }}" {{ (old('vehicle_opposite_id') ? old('vehicle_opposite_id') : $claim->vehicle_opposite->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('vehicle_opposite'))
                    <span class="text-danger">{{ $errors->first('vehicle_opposite') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.vehicle_opposite_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.claim.fields.opposite_type') }}</label>
                <select class="form-control {{ $errors->has('opposite_type') ? 'is-invalid' : '' }}" name="opposite_type" id="opposite_type">
                    <option value disabled {{ old('opposite_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Claim::OPPOSITE_TYPE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('opposite_type', $claim->opposite_type) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('opposite_type'))
                    <span class="text-danger">{{ $errors->first('opposite_type') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.opposite_type_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="damaged_part">{{ trans('cruds.claim.fields.damaged_part') }}</label>
                <input class="form-control {{ $errors->has('damaged_part') ? 'is-invalid' : '' }}" type="text" name="damaged_part" id="damaged_part" value="{{ old('damaged_part', $claim->damaged_part) }}">
                @if($errors->has('damaged_part'))
                    <span class="text-danger">{{ $errors->first('damaged_part') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.damaged_part_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="damage_origin">{{ trans('cruds.claim.fields.damage_origin') }}</label>
                <input class="form-control {{ $errors->has('damage_origin') ? 'is-invalid' : '' }}" type="text" name="damage_origin" id="damage_origin" value="{{ old('damage_origin', $claim->damage_origin) }}">
                @if($errors->has('damage_origin'))
                    <span class="text-danger">{{ $errors->first('damage_origin') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.damage_origin_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.claim.fields.damaged_area') }}</label>
                <select class="form-control {{ $errors->has('damaged_area') ? 'is-invalid' : '' }}" name="damaged_area" id="damaged_area">
                    <option value disabled {{ old('damaged_area', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Claim::DAMAGED_AREA_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('damaged_area', $claim->damaged_area) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('damaged_area'))
                    <span class="text-danger">{{ $errors->first('damaged_area') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.damaged_area_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="damaged_part_opposite">{{ trans('cruds.claim.fields.damaged_part_opposite') }}</label>
                <input class="form-control {{ $errors->has('damaged_part_opposite') ? 'is-invalid' : '' }}" type="text" name="damaged_part_opposite" id="damaged_part_opposite" value="{{ old('damaged_part_opposite', $claim->damaged_part_opposite) }}">
                @if($errors->has('damaged_part_opposite'))
                    <span class="text-danger">{{ $errors->first('damaged_part_opposite') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.damaged_part_opposite_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="damage_origin_opposite">{{ trans('cruds.claim.fields.damage_origin_opposite') }}</label>
                <input class="form-control {{ $errors->has('damage_origin_opposite') ? 'is-invalid' : '' }}" type="text" name="damage_origin_opposite" id="damage_origin_opposite" value="{{ old('damage_origin_opposite', $claim->damage_origin_opposite) }}">
                @if($errors->has('damage_origin_opposite'))
                    <span class="text-danger">{{ $errors->first('damage_origin_opposite') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.damage_origin_opposite_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.claim.fields.damaged_area_opposite') }}</label>
                <select class="form-control {{ $errors->has('damaged_area_opposite') ? 'is-invalid' : '' }}" name="damaged_area_opposite" id="damaged_area_opposite">
                    <option value disabled {{ old('damaged_area_opposite', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Claim::DAMAGED_AREA_OPPOSITE_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('damaged_area_opposite', $claim->damaged_area_opposite) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('damaged_area_opposite'))
                    <span class="text-danger">{{ $errors->first('damaged_area_opposite') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.damaged_area_opposite_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="recovery_office_id">{{ trans('cruds.claim.fields.recovery_office') }}</label>
                <select class="form-control select2 {{ $errors->has('recovery_office') ? 'is-invalid' : '' }}" name="recovery_office_id" id="recovery_office_id">
                    @foreach($recovery_offices as $id => $entry)
                        <option value="{{ $id }}" {{ (old('recovery_office_id') ? old('recovery_office_id') : $claim->recovery_office->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('recovery_office'))
                    <span class="text-danger">{{ $errors->first('recovery_office') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.recovery_office_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="damage_costs">{{ trans('cruds.claim.fields.damage_costs') }}</label>
                <input class="form-control {{ $errors->has('damage_costs') ? 'is-invalid' : '' }}" type="number" name="damage_costs" id="damage_costs" value="{{ old('damage_costs', $claim->damage_costs) }}" step="0.01">
                @if($errors->has('damage_costs'))
                    <span class="text-danger">{{ $errors->first('damage_costs') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.damage_costs_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="recovery_costs">{{ trans('cruds.claim.fields.recovery_costs') }}</label>
                <input class="form-control {{ $errors->has('recovery_costs') ? 'is-invalid' : '' }}" type="number" name="recovery_costs" id="recovery_costs" value="{{ old('recovery_costs', $claim->recovery_costs) }}" step="0.01">
                @if($errors->has('recovery_costs'))
                    <span class="text-danger">{{ $errors->first('recovery_costs') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.recovery_costs_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="replacement_vehicle_costs">{{ trans('cruds.claim.fields.replacement_vehicle_costs') }}</label>
                <input class="form-control {{ $errors->has('replacement_vehicle_costs') ? 'is-invalid' : '' }}" type="number" name="replacement_vehicle_costs" id="replacement_vehicle_costs" value="{{ old('replacement_vehicle_costs', $claim->replacement_vehicle_costs) }}" step="0.01">
                @if($errors->has('replacement_vehicle_costs'))
                    <span class="text-danger">{{ $errors->first('replacement_vehicle_costs') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.replacement_vehicle_costs_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="expert_costs">{{ trans('cruds.claim.fields.expert_costs') }}</label>
                <input class="form-control {{ $errors->has('expert_costs') ? 'is-invalid' : '' }}" type="number" name="expert_costs" id="expert_costs" value="{{ old('expert_costs', $claim->expert_costs) }}" step="0.01">
                @if($errors->has('expert_costs'))
                    <span class="text-danger">{{ $errors->first('expert_costs') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.expert_costs_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="other_costs">{{ trans('cruds.claim.fields.other_costs') }}</label>
                <input class="form-control {{ $errors->has('other_costs') ? 'is-invalid' : '' }}" type="number" name="other_costs" id="other_costs" value="{{ old('other_costs', $claim->other_costs) }}" step="0.01">
                @if($errors->has('other_costs'))
                    <span class="text-danger">{{ $errors->first('other_costs') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.other_costs_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="deductible_excess_costs">{{ trans('cruds.claim.fields.deductible_excess_costs') }}</label>
                <input class="form-control {{ $errors->has('deductible_excess_costs') ? 'is-invalid' : '' }}" type="number" name="deductible_excess_costs" id="deductible_excess_costs" value="{{ old('deductible_excess_costs', $claim->deductible_excess_costs) }}" step="0.01">
                @if($errors->has('deductible_excess_costs'))
                    <span class="text-danger">{{ $errors->first('deductible_excess_costs') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.deductible_excess_costs_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="insurance_costs">{{ trans('cruds.claim.fields.insurance_costs') }}</label>
                <input class="form-control {{ $errors->has('insurance_costs') ? 'is-invalid' : '' }}" type="number" name="insurance_costs" id="insurance_costs" value="{{ old('insurance_costs', $claim->insurance_costs) }}" step="0.01">
                @if($errors->has('insurance_costs'))
                    <span class="text-danger">{{ $errors->first('insurance_costs') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.insurance_costs_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="expertise_office_id">{{ trans('cruds.claim.fields.expertise_office') }}</label>
                <select class="form-control select2 {{ $errors->has('expertise_office') ? 'is-invalid' : '' }}" name="expertise_office_id" id="expertise_office_id">
                    @foreach($expertise_offices as $id => $entry)
                        <option value="{{ $id }}" {{ (old('expertise_office_id') ? old('expertise_office_id') : $claim->expertise_office->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('expertise_office'))
                    <span class="text-danger">{{ $errors->first('expertise_office') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.expertise_office_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('expert_report_is_in') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="expert_report_is_in" value="0">
                    <input class="form-check-input" type="checkbox" name="expert_report_is_in" id="expert_report_is_in" value="1" {{ $claim->expert_report_is_in || old('expert_report_is_in', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="expert_report_is_in">{{ trans('cruds.claim.fields.expert_report_is_in') }}</label>
                </div>
                @if($errors->has('expert_report_is_in'))
                    <span class="text-danger">{{ $errors->first('expert_report_is_in') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.expert_report_is_in_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="requested_at">{{ trans('cruds.claim.fields.requested_at') }}</label>
                <input class="form-control datetime {{ $errors->has('requested_at') ? 'is-invalid' : '' }}" type="text" name="requested_at" id="requested_at" value="{{ old('requested_at', $claim->requested_at) }}" required>
                @if($errors->has('requested_at'))
                    <span class="text-danger">{{ $errors->first('requested_at') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.requested_at_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="report_received_at">{{ trans('cruds.claim.fields.report_received_at') }}</label>
                <input class="form-control datetime {{ $errors->has('report_received_at') ? 'is-invalid' : '' }}" type="text" name="report_received_at" id="report_received_at" value="{{ old('report_received_at', $claim->report_received_at) }}">
                @if($errors->has('report_received_at'))
                    <span class="text-danger">{{ $errors->first('report_received_at') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.report_received_at_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="files">{{ trans('cruds.claim.fields.files') }}</label>
                <div class="needsclick dropzone {{ $errors->has('files') ? 'is-invalid' : '' }}" id="files-dropzone">
                </div>
                @if($errors->has('files'))
                    <span class="text-danger">{{ $errors->first('files') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.files_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    var uploadedFilesMap = {}
Dropzone.options.filesDropzone = {
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
      $('form').append('<input type="hidden" name="files[]" value="' + response.name + '">')
      uploadedFilesMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedFilesMap[file.name]
      }
      $('form').find('input[name="files[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($claim) && $claim->files)
          var files =
            {!! json_encode($claim->files) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="files[]" value="' + file.file_name + '">')
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