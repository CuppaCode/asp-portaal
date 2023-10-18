@extends('layouts.admin')
@section('content')

@php

    $isAdmin = auth()->user()->roles->contains(1);

@endphp

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.claim.title_singular') }}
    </div>

    <div class="card-body">
        <p>Cupcake ipsum dolor sit amet jelly-o. Topping gingerbread chocolate toffee pudding. Pudding donut marzipan cookie croissant fruitcake.</p>
        <p>Ice cream jujubes jujubes oat cake tiramisu soufflé sesame snaps jelly. Macaroon wafer jelly beans toffee wafer caramels dragée bonbon. Candy jelly beans caramels dessert marzipan sugar plum sesame snaps jelly-o muffin.</p>
    </div>
</div>
<form method="POST" action="{{ route("admin.claims.store") }}" enctype="multipart/form-data">
    <div class="card">

        <div class="card-header">
            Dossier informatie
        </div>

        <div class="card-body">
            @csrf

            @if ($isAdmin)
                <div class="form-group">

                    <label class="required" for="company_id">{{ trans('cruds.claim.fields.company') }}</label>
                    <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="company_id" id="company_id" required>
                        @foreach($companies as $id => $entry)
                            <option value="{{ $id }}" {{ old('company_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
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
                <input type="hidden" name="company_id" id="company_id" value="1">

            @endif
            <input type="hidden" name="status" id="status" value="new"/>
            <div class="form-group">
                <label class="required" for="subject">{{ trans('cruds.claim.fields.subject') }}</label>
                <input class="form-control {{ $errors->has('subject') ? 'is-invalid' : '' }}" type="text" name="subject" id="subject" value="{{ old('subject', '') }}" required>
                @if($errors->has('subject'))
                    <div class="invalid-feedback">
                        {{ $errors->first('subject') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.subject_helper') }}</span>
            </div>
            <div class="form-group d-none">
                <label class="required" for="claim_number">{{ trans('cruds.claim.fields.claim_number') }}</label>
                <input class="form-control {{ $errors->has('claim_number') ? 'is-invalid' : '' }}" type="text" name="claim_number" id="claim_number" value="{{ old('claim_number', 'claim_number_format') }}" required readonly>
                @if($errors->has('claim_number'))
                    <div class="invalid-feedback">
                        {{ $errors->first('claim_number') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.claim_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="opposite_claim_no">{{ trans('cruds.claim.fields.opposite_claim_no') }}</label>
                <input class="form-control {{ $errors->has('opposite_claim_no') ? 'is-invalid' : '' }}" type="text" name="opposite_claim_no" id="opposite_claim_no" value="{{ old('opposite_claim_no', '') }}">
                @if($errors->has('opposite_claim_no'))
                    <div class="invalid-feedback">
                        {{ $errors->first('opposite_claim_no') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.invoice_settlement_helper') }}</span>
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.next') }}
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