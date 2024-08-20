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
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">&euro;</div>
                        </div>
                        <input class="form-control {{ $errors->has('max_amount') ? 'is-invalid' : '' }}" type="number" name="max_amount" id="max_amount" value="{{ old('max_amount', '') }}">
                        @if ($errors->has('max_amount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('max_amount') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.sla.fields.max_amount_helper') }}</span>
                    </div>
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

