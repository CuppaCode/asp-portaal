@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.certificate.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.certificate.store", ['driver' => $driver->id]) }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.contact.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('first_name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.name') }}</span>
            </div>

            <div class="form-group">
                <label for="notify_date">{{ trans('cruds.claim.fields.notify_date') }}</label>
                <input class="form-control date custom_datepicker {{ $errors->has('notify_date') ? 'is-invalid' : '' }}" type="text" name="notify_date" id="notify_date" value="{{ old('notify_date') }}">
                @if($errors->has('notify_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('notify_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.notify_date') }}</span>
            </div>

            <div class="form-group">
                <label for="expiry_date">{{ trans('cruds.claim.fields.expiry_date') }}</label>
                <input class="form-control date custom_datepicker {{ $errors->has('expiry_date') ? 'is-invalid' : '' }}" type="text" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}">
                @if($errors->has('expiry_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('expiry_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.claim.fields.expiry_date') }}</span>
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