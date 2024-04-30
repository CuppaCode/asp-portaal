@extends('layouts.admin')
@section('content')
    @php

        $isAdmin = auth()->user()->roles->contains(1);

    @endphp

    <form method="POST" action="{{ route('admin.sla.store') }}" enctype="multipart/form-data">
        <div class="card">

            <div class="card-header">
                SLA toevoegen
            </div>

            <div class="card-body">
                @csrf
                <div class="form-group">

                    <label class="required" for="company_id">{{ trans('cruds.sla.fields.company') }}</label>
                    <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="company_id"vid="company_id" required>
                        @foreach ($companies as $id => $entry)
                            <option value="{{ $id }}" {{ old('company_id') == $id ? 'selected' : '' }}>
                                {{ $entry }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('company'))
                        <div class="invalid-feedback">
                            {{ $errors->first('company') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.sla.fields.company_helper') }}</span>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="startdate">{{ trans('cruds.sla.fields.startdate') }}</label>
                        <input
                            class="form-control date custom_datepicker {{ $errors->has('startdate') ? 'is-invalid' : '' }}" type="text" name="startdate" id="startdate">
                        @if ($errors->has('startdate'))
                            <div class="invalid-feedback">
                                {{ $errors->first('startdate') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.sla.fields.startdate_helper') }}</span>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="enddate">{{ trans('cruds.sla.fields.enddate') }}</label>
                        <input
                            class="form-control date custom_datepicker {{ $errors->has('enddate') ? 'is-invalid' : '' }}" type="text" name="enddate" id="enddate">
                        @if ($errors->has('enddate'))
                            <div class="invalid-feedback">
                                {{ $errors->first('enddate') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.sla.fields.enddate_helper') }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="amount_users">{{ trans('cruds.sla.fields.amount_users') }}</label>
                    <input class="form-control {{ $errors->has('amount_users') ? 'is-invalid' : '' }}" type="number" name="amount_users" id="amount_users" value="{{ old('amount_users', '') }}">
                    @if ($errors->has('amount_users'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount_users') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.sla.fields.amount_users_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="max_amount">{{ trans('cruds.sla.fields.max_amount') }}</label>
                    <input class="form-control {{ $errors->has('max_amount') ? 'is-invalid' : '' }}" type="number" name="max_amount" id="max_amount" value="{{ old('max_amount', '') }}">
                    @if ($errors->has('max_amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('max_amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.sla.fields.max_amount_helper') }}</span>
                </div>
                <div class="form-group">

                    <label for="reports">{{ trans('cruds.sla.fields.reports') }}</label>
                    <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="reports" id="reports" required>
                        <option value disabled {{ old('reports', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\SLA::REPORT_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('reports') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                    </select>
                    @if ($errors->has('reports'))
                        <div class="invalid-feedback">
                            {{ $errors->first('reports') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.sla.fields.reports_helper') }}</span>
                </div>
                <div class="form-group">

                    <label for="label">{{ trans('cruds.sla.fields.label') }}</label>
                    <select class="form-control select2 {{ $errors->has('label') ? 'is-invalid' : '' }}" name="label" id="label" required>
                        <option value disabled {{ old('label', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\SLA::LABEL_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('label') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                    </select>
                    @if ($errors->has('label'))
                        <div class="invalid-feedback">
                            {{ $errors->first('label') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.sla.fields.reports_helper') }}</span>
                </div>
                <div class="form-group">

                    <label for="analytics_options">{{ trans('cruds.sla.fields.analytics') }}</label>
                    <select class="form-control select2 {{ $errors->has('analytics_options') ? 'is-invalid' : '' }}" name="analytics_options[]" id="analytics_options" multiple>
                        @foreach(App\Models\SLA::ANALYTICS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('analytics_options') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                    </select>
                    @if ($errors->has('analytics_options'))
                        <div class="invalid-feedback">
                            {{ $errors->first('analytics_options') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.sla.fields.reports_helper') }}</span>
                </div>

                <div class="form-group other-show d-none">
                    <label for="other">{{ trans('cruds.sla.fields.other') }}</label>
                    <input class="form-control {{ $errors->has('label') ? 'is-invalid' : '' }}" type="text" name="other" id="other" value="{{ old('other', '') }}">
                    @if ($errors->has('other'))
                        <div class="invalid-feedback">
                            {{ $errors->first('other') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.sla.fields.other_helper') }}</span>
                </div>

                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.submit') }}
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
            success: function(file, response) {
                $('form').append('<input type="hidden" name="damage_files[]" value="' + response.name + '">')
                uploadedDamageFilesMap[file.name] = response.name
            },
            removedfile: function(file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedDamageFilesMap[file.name]
                }
                $('form').find('input[name="damage_files[]"][value="' + name + '"]').remove()
            },
            init: function() {
                @if (isset($claim) && $claim->damage_files)
                    var files =
                        {!! json_encode($claim->damage_files) !!}
                    for (var i in files) {
                        var file = files[i]
                        this.options.addedfile.call(this, file)
                        file.previewElement.classList.add('dz-complete')
                        $('form').append('<input type="hidden" name="damage_files[]" value="' + file.file_name +
                            '">')
                    }
                @endif
            },
            error: function(file, response) {
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
            success: function(file, response) {
                $('form').append('<input type="hidden" name="report_files[]" value="' + response.name + '">')
                uploadedReportFilesMap[file.name] = response.name
            },
            removedfile: function(file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedReportFilesMap[file.name]
                }
                $('form').find('input[name="report_files[]"][value="' + name + '"]').remove()
            },
            init: function() {
                @if (isset($claim) && $claim->report_files)
                    var files =
                        {!! json_encode($claim->report_files) !!}
                    for (var i in files) {
                        var file = files[i]
                        this.options.addedfile.call(this, file)
                        file.previewElement.classList.add('dz-complete')
                        $('form').append('<input type="hidden" name="report_files[]" value="' + file.file_name +
                            '">')
                    }
                @endif
            },
            error: function(file, response) {
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
            success: function(file, response) {
                $('form').append('<input type="hidden" name="financial_files[]" value="' + response.name + '">')
                uploadedFinancialFilesMap[file.name] = response.name
            },
            removedfile: function(file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedFinancialFilesMap[file.name]
                }
                $('form').find('input[name="financial_files[]"][value="' + name + '"]').remove()
            },
            init: function() {
                @if (isset($claim) && $claim->financial_files)
                    var files =
                        {!! json_encode($claim->financial_files) !!}
                    for (var i in files) {
                        var file = files[i]
                        this.options.addedfile.call(this, file)
                        file.previewElement.classList.add('dz-complete')
                        $('form').append('<input type="hidden" name="financial_files[]" value="' + file.file_name +
                            '">')
                    }
                @endif
            },
            error: function(file, response) {
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
            success: function(file, response) {
                $('form').append('<input type="hidden" name="other_files[]" value="' + response.name + '">')
                uploadedOtherFilesMap[file.name] = response.name
            },
            removedfile: function(file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedOtherFilesMap[file.name]
                }
                $('form').find('input[name="other_files[]"][value="' + name + '"]').remove()
            },
            init: function() {
                @if (isset($claim) && $claim->other_files)
                    var files =
                        {!! json_encode($claim->other_files) !!}
                    for (var i in files) {
                        var file = files[i]
                        this.options.addedfile.call(this, file)
                        file.previewElement.classList.add('dz-complete')
                        $('form').append('<input type="hidden" name="other_files[]" value="' + file.file_name +
                            '">')
                    }
                @endif
            },
            error: function(file, response) {
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
