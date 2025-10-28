@extends('layouts.admin')
@section('content')


<form method="POST" action="{{ route('admin.companies.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ trans('cruds.company.fields.name') }} & {{ trans('cruds.company.fields.company_type') }}</div>
                <div class="card-body">
                    @can('company_access')
                        <div class="form-group">
                            <label class="required" for="name">{{ trans('cruds.company.fields.name') }}</label>
                            <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.company.fields.name_helper') }}</span>
                        </div>
                    @endcan
                    <div class="form-group">
                        <label>{{ trans('cruds.company.fields.contact') }}</label>
                        <select class="form-control {{ $errors->has('contact') ? 'is-invalid' : '' }}" name="contact_id" id="contact_id">
                            <option value disabled {{ old('contact', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                            @foreach($contacts as $id => $entry)
                                <option value="{{ $entry->id }}" {{ (old('contact') ? old('contact') : $company->contact_id ?? '' ) == $id ? 'selected' : '' }}>{{ $entry->first_name ." ". $entry->last_name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('contact'))
                            <div class="invalid-feedback">
                                {{ $errors->first('contact') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.company.fields.company_type_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label>{{ trans('cruds.company.fields.company_type') }}</label>
                        <select class="form-control {{ $errors->has('company_type') ? 'is-invalid' : '' }}" name="company_type" id="company_type">
                            <option value disabled {{ old('company_type', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                            @foreach(App\Models\Company::COMPANY_TYPE_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('company_type', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('company_type'))
                            <div class="invalid-feedback">
                                {{ $errors->first('company_type') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.company.fields.company_type_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="required" for="street">{{ trans('cruds.company.fields.street') }}</label>
                        <input class="form-control {{ $errors->has('street') ? 'is-invalid' : '' }}" type="text" name="street" id="street" value="{{ old('street', '') }}" required>
                        @if($errors->has('street'))
                            <div class="invalid-feedback">
                                {{ $errors->first('street') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.company.fields.street_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="required" for="zipcode">{{ trans('cruds.company.fields.zipcode') }}</label>
                        <input class="form-control {{ $errors->has('zipcode') ? 'is-invalid' : '' }}" type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', '') }}" required>
                        @if($errors->has('zipcode'))
                            <div class="invalid-feedback">
                                {{ $errors->first('zipcode') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.company.fields.zipcode_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="required" for="city">{{ trans('cruds.company.fields.city') }}</label>
                        <input class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}" type="text" name="city" id="city" value="{{ old('city', '') }}" required>
                        @if($errors->has('city'))
                            <div class="invalid-feedback">
                                {{ $errors->first('city') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.company.fields.city_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="required" for="country">{{ trans('cruds.company.fields.country') }}</label>
                        <input class="form-control {{ $errors->has('country') ? 'is-invalid' : '' }}" type="text" name="country" id="country" value="{{ old('country', '') }}" required>
                        @if($errors->has('country'))
                            <div class="invalid-feedback">
                                {{ $errors->first('country') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.company.fields.country_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="required" for="phone">{{ trans('cruds.company.fields.phone') }}</label>
                        <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" type="text" name="phone" id="phone" value="{{ old('phone', '') }}" required>
                        @if($errors->has('phone'))
                            <div class="invalid-feedback">
                                {{ $errors->first('phone') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.company.fields.phone_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <div class="form-check {{ $errors->has('active') ? 'is-invalid' : '' }}">
                            <input type="hidden" name="active" value="0">
                            <input class="form-check-input" type="checkbox" name="active" id="active" value="1" {{ old('active', 0) == 1 || old('active') === null ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">{{ trans('cruds.company.fields.active') }}</label>
                        </div>
                        @if($errors->has('active'))
                            <div class="invalid-feedback">
                                {{ $errors->first('active') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.company.fields.active_helper') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ trans('cruds.company.fields.description') }} & {{ trans('cruds.company.fields.bank_account_number') }}</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="description">{{ trans('cruds.company.fields.description') }}</label>
                        <textarea class="form-control ckeditor {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{!! old('description') !!}</textarea>
                        @if($errors->has('description'))
                            <div class="invalid-feedback">
                                {{ $errors->first('description') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.company.fields.description_helper') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="bank_account_number">{{ trans('cruds.company.fields.bank_account_number') }}</label>
                        <input class="form-control {{ $errors->has('bank_account_number') ? 'is-invalid' : '' }}" type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', '') }}">
                        @if($errors->has('bank_account_number'))
                            <div class="invalid-feedback">
                                {{ $errors->first('bank_account_number') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ trans('cruds.company.fields.company_size') }} & {{ trans('cruds.company.fields.truck_count') }}</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="company_size">{{ trans('cruds.company.fields.company_size') }}</label>
                        <input class="form-control {{ $errors->has('company_size') ? 'is-invalid' : '' }}" type="text" name="company_size" id="company_size" value="{{ old('company_size', '') }}">
                        @if($errors->has('company_size'))
                            <div class="invalid-feedback">
                                {{ $errors->first('company_size') }}
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="truck_count">{{ trans('cruds.company.fields.truck_count') }}</label>
                        <input class="form-control {{ $errors->has('truck_count') ? 'is-invalid' : '' }}" type="number" name="truck_count" id="truck_count" value="{{ old('truck_count', '') }}">
                        @if($errors->has('truck_count'))
                            <div class="invalid-feedback">
                                {{ $errors->first('truck_count') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ trans('cruds.company.fields.additional_information') }}</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="additional_information">{{ trans('cruds.company.fields.additional_information') }}</label>
                        <textarea class="form-control {{ $errors->has('additional_information') ? 'is-invalid' : '' }}" name="additional_information" id="additional_information">{{ old('additional_information', '') }}</textarea>
                        @if($errors->has('additional_information'))
                            <div class="invalid-feedback">
                                {{ $errors->first('additional_information') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header">{{ trans('cruds.company.fields.financial') }}</div>
        <div class="card-body">
            <div class="form-group">
                <label for="start_fee">{{ trans('cruds.company.fields.start_fee') }}</label>
                <input class="form-control" type="number" step="0.01" name="start_fee" id="start_fee" value="{{ old('start_fee') }}">
            </div>
            <div class="form-group">
                <label for="claims_fee">{{ trans('cruds.company.fields.claims_fee') }}</label>
                <input class="form-control" type="number" step="0.01" name="claims_fee" id="claims_fee" value="{{ old('claims_fee') }}">
            </div>
            <div class="form-group">
                <label for="additional_costs">{{ trans('cruds.company.fields.additional_costs') }}</label>
                <input class="form-control" type="number" step="0.01" name="additional_costs" id="additional_costs" value="{{ old('additional_costs') }}">
            </div>
        </div>
    </div>
    <div class="form-group mt-3">
        <button class="btn btn-primary" type="submit">
            {{ trans('global.save') }}
        </button>
        <a class="btn btn-default" href="{{ route('admin.companies.index') }}">
            {{ trans('global.back_to_list') }}
        </a>
    </div>
</form>



@endsection

@section('scripts')
<script>
    $(document).ready(function () {
  function SimpleUploadAdapter(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
      return {
        upload: function() {
          return loader.file
            .then(function (file) {
              return new Promise(function(resolve, reject) {
                // Init request
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.companies.storeCKEditorImages') }}', true);
                xhr.setRequestHeader('x-csrf-token', window._token);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.responseType = 'json';

                // Init listeners
                var genericErrorText = `Couldn't upload file: ${ file.name }.`;
                xhr.addEventListener('error', function() { reject(genericErrorText) });
                xhr.addEventListener('abort', function() { reject() });
                xhr.addEventListener('load', function() {
                  var response = xhr.response;

                  if (!response || xhr.status !== 201) {
                    return reject(response && response.message ? `${genericErrorText}\n${xhr.status} ${response.message}` : `${genericErrorText}\n ${xhr.status} ${xhr.statusText}`);
                  }

                  $('form').append('<input type="hidden" name="ck-media[]" value="' + response.id + '">');

                  resolve({ default: response.url });
                });

                if (xhr.upload) {
                  xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                      loader.uploadTotal = e.total;
                      loader.uploaded = e.loaded;
                    }
                  });
                }

                // Send request
                var data = new FormData();
                data.append('upload', file);
                data.append('crud_id', '{{ $company->id ?? 0 }}');
                xhr.send(data);
              });
            })
        }
      };
    }
  }

  var allEditors = document.querySelectorAll('.ckeditor');
  for (var i = 1; i < allEditors.length; ++i) {
    ClassicEditor.create(
      allEditors[i], {
        extraPlugins: [SimpleUploadAdapter]
      }
    );
  }
});
</script>

@endsection