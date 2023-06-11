@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.vehicle.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.vehicles.store") }}" enctype="multipart/form-data">
            @csrf

            @if (auth()->user()->roles->contains(1))
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
            <div class="form-group">
                <label for="name">{{ trans('cruds.vehicle.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}">
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.vehicle.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="plates">{{ trans('cruds.vehicle.fields.plates') }}</label>
                <input class="form-control {{ $errors->has('plates') ? 'is-invalid' : '' }}" type="text" name="plates" id="plates" value="{{ old('plates', '') }}" required>
                @if($errors->has('plates'))
                    <div class="invalid-feedback">
                        {{ $errors->first('plates') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.vehicle.fields.plates_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="drivers">{{ trans('cruds.vehicle.fields.driver') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('drivers') ? 'is-invalid' : '' }}" name="drivers[]" id="drivers" multiple>
                    @foreach($drivers as $id => $driver)
                        <option value="{{ $id }}" {{ in_array($id, old('drivers', [])) ? 'selected' : '' }}>{{ $driver }}</option>
                    @endforeach
                </select>
                @if($errors->has('drivers'))
                    <div class="invalid-feedback">
                        {{ $errors->first('drivers') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.vehicle.fields.driver_helper') }}</span>
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