@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.vehicleOpposite.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.vehicle-opposites.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.vehicleOpposite.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.vehicleOpposite.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="plates">{{ trans('cruds.vehicleOpposite.fields.plates') }}</label>
                <input class="form-control {{ $errors->has('plates') ? 'is-invalid' : '' }}" type="text" name="plates" id="plates" value="{{ old('plates', '') }}" required>
                @if($errors->has('plates'))
                    <div class="invalid-feedback">
                        {{ $errors->first('plates') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.vehicleOpposite.fields.plates_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="drivers">{{ trans('cruds.vehicleOpposite.fields.driver') }}</label>
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
                <span class="help-block">{{ trans('cruds.vehicleOpposite.fields.driver_helper') }}</span>
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