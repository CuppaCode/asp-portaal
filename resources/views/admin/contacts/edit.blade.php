@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.contact.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.contacts.update", [$contact->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="company_id">{{ trans('cruds.contact.fields.company') }}</label>
                <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="company_id" id="company_id" required>
                    @foreach($companies as $id => $entry)
                        <option value="{{ $id }}" {{ (old('company_id') ? old('company_id') : $contact->company->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('company'))
                    <div class="invalid-feedback">
                        {{ $errors->first('company') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.company_helper') }}</span>
            </div>
            <div class="form-group d-none">
                <label for="user_id">{{ trans('cruds.contact.fields.user') }}</label>
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id">
                    @foreach($users as $id => $entry)
                        <option value="{{ $id }}" {{ (old('user_id') ? old('user_id') : $contact->user->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('user'))
                    <div class="invalid-feedback">
                        {{ $errors->first('user') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.user_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="first_name">{{ trans('cruds.contact.fields.first_name') }}</label>
                <input class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" type="text" name="first_name" id="first_name" value="{{ old('first_name', $contact->first_name) }}" required>
                @if($errors->has('first_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('first_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.first_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="last_name">{{ trans('cruds.contact.fields.last_name') }}</label>
                <input class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" type="text" name="last_name" id="last_name" value="{{ old('last_name', $contact->last_name) }}" required>
                @if($errors->has('last_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('last_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.last_name_helper') }}</span>
            </div>
                <div class="form-group">
                    <label for="birthdate">{{ trans('cruds.contact.fields.birthdate') }}</label>
                    <input class="form-control {{ $errors->has('birthdate') ? 'is-invalid' : '' }}" type="date" name="birthdate" id="birthdate" value="{{ old('birthdate', $contact->birthdate) }}">
                    @if($errors->has('birthdate'))
                        <div class="invalid-feedback">
                            {{ $errors->first('birthdate') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.contact.fields.birthdate_helper') }}</span>
                </div>
                <div class="form-group">
                    <label for="driver_license_id">{{ trans('cruds.contact.fields.driver_license_id') }}</label>
                    <input class="form-control {{ $errors->has('driver_license_id') ? 'is-invalid' : '' }}" type="text" name="driver_license_id" id="driver_license_id" value="{{ old('driver_license_id', $contact->driver_license_id) }}">
                    @if($errors->has('driver_license_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('driver_license_id') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.contact.fields.driver_license_id_helper') }}</span>
                </div>
            <div class="form-group">
                <label class="required" for="email">{{ trans('cruds.contact.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email', $contact->email) }}" required>
                @if($errors->has('email'))
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.email_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="phone">{{ trans('cruds.contact.fields.phone') }}</label>
                <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone', $contact->phone) }}">
            </div>
            <div class="form-group">
                <label for="note">{{ trans('cruds.contact.fields.note') }}</label>
                <textarea class="form-control" name="note" id="note">{{ old('note', $contact->note) }}</textarea>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('newsletter') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="newsletter" value="0">
                    <input class="form-check-input" type="checkbox" name="newsletter" id="newsletter" value="1" {{ $contact->newsletter || old('newsletter', 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="newsletter">{{ trans('cruds.contact.fields.newsletter') }}</label>
                </div>
                @if($errors->has('newsletter'))
                    <div class="invalid-feedback">
                        {{ $errors->first('newsletter') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.newsletter_helper') }}</span>
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