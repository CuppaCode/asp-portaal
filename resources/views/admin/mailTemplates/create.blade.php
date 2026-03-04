@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.mailTemplates.title_singular') }}
    </div>
</div>
<form method="POST" action="{{ route("admin.mail-templates.store") }}" enctype="multipart/form-data">
    <div class="card">

        <div class="card-header">
          Gebruik de volgende tags: <pre>[bedrijf] [telnr] [onderwerp] [dossiernr] [status] [datumschade] [kenteken] [schade_aard] [schade_plaats] [schade_oorzaak] [schade_bedrag] [kenteken_wederpartij] [verhaalbaar] [schade_soort]</pre>
          <pre>[contact_naam] [contact_email]</pre>
          <pre>[herstel_adres] [herstel_postcode] [herstel_telnr] [herstel_contact_naam] [herstel_email]</pre>
          <pre>[chauffeur_naam] [chauffeur_email]</pre>
          <pre>[wederpartij_naam] [wederpartij_adres] [wederpartij_postcode_stad] [wederpartij_telnr] [wederpartij_email] [wederpartij_schade_aard] [wederpartij_schade_plaats] [wederpartij_schade_oorzaak]</pre>
        </div>

        <div class="card-body">
            @csrf

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.mailTemplates.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.mailTemplates.fields.name_helper') }}</span>
            </div>

            <div class="form-group">
              <label for="body">{{ trans('cruds.mailTemplates.fields.subject') }}</label>
              <input class="form-control {{ $errors->has('subject') ? 'is-invalid' : '' }}" type="text" name="subject" id="subject" value="{{ old('subject', '') }}">
              @if($errors->has('subject'))
                  <div class="invalid-feedback">
                      {{ $errors->first('subject') }}
                  </div>
              @endif
              <span class="help-block">{{ trans('cruds.mailTemplates.fields.subject_helper') }}</span>
            </div>

            <div class="form-group">
                <label for="body">{{ trans('cruds.mailTemplates.fields.body') }}</label>
                <textarea class="form-control ckeditor {{ $errors->has('body') ? 'is-invalid' : '' }}" name="body" id="body" value="{{ old('body', '') }}"></textarea>
                @if($errors->has('body'))
                    <div class="invalid-feedback">
                        {{ $errors->first('body') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.mailTemplates.fields.body_helper') }}</span>
            </div>

            <div class="form-group">
                <label for="trigger_type">{{ trans('cruds.mailTemplates.fields.trigger_type') }}</label>
                <select class="form-control {{ $errors->has('trigger_type') ? 'is-invalid' : '' }}" name="trigger_type" id="trigger_type">
                    <option value="">{{ trans('global.pleaseSelect') }}</option>
                    <option value="CLAIM_CREATED" {{ old('trigger_type') == 'CLAIM_CREATED' ? 'selected' : '' }}>{{ trans('cruds.superAdmin.triggers.types.CLAIM_CREATED.name') }}</option>
                    <option value="CLAIM_STATUS_CHANGED" {{ old('trigger_type') == 'CLAIM_STATUS_CHANGED' ? 'selected' : '' }}>{{ trans('cruds.superAdmin.triggers.types.CLAIM_STATUS_CHANGED.name') }}</option>
                    <option value="TASK_ASSIGNED" {{ old('trigger_type') == 'TASK_ASSIGNED' ? 'selected' : '' }}>{{ trans('cruds.superAdmin.triggers.types.TASK_ASSIGNED.name') }}</option>
                    <option value="MANUAL_CLAIMS" {{ old('trigger_type') == 'MANUAL_CLAIMS' ? 'selected' : '' }}>{{ trans('cruds.superAdmin.triggers.types.MANUAL_CLAIMS.name') }}</option>
                    <option value="MANUAL_GENERAL" {{ old('trigger_type') == 'MANUAL_GENERAL' ? 'selected' : '' }}>{{ trans('cruds.superAdmin.triggers.types.MANUAL_GENERAL.name') }}</option>
                </select>
                @if($errors->has('trigger_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('trigger_type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.mailTemplates.fields.trigger_type_helper') }}</span>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        {{ trans('cruds.mailTemplates.fields.is_active') }}
                    </label>
                </div>
                <span class="help-block">{{ trans('cruds.mailTemplates.fields.is_active_helper') }}</span>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_automatic" id="is_automatic" value="1" {{ old('is_automatic', false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_automatic">
                        {{ trans('cruds.mailTemplates.fields.is_automatic') }}
                    </label>
                </div>
                <span class="help-block">{{ trans('cruds.mailTemplates.fields.is_automatic_helper') }}</span>
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </div>
        
    </div>
</form>



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