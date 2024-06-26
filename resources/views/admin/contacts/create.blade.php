@extends('layouts.admin')
@section('content')

@php

    $user = auth()->user();
    $isAdmin = $user->can('financial_access');
    $isAdminOrAgent = $user->isAdminOrAgent();

@endphp

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.contact.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.contacts.store") }}" enctype="multipart/form-data">
            @csrf

            @if ($isAdminOrAgent)

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
            
            @if ($isAdminOrAgent)

                <div class="form-group d-none">
                    <label for="user_id">{{ trans('cruds.contact.fields.user') }}</label>
                    <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id">
                        @foreach($users as $id => $entry)
                            <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('user'))
                        <div class="invalid-feedback">
                            {{ $errors->first('user') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.contact.fields.user_helper') }}</span>
                </div>

            @endif

            <div class="form-group">
                <label class="required" for="first_name">{{ trans('cruds.contact.fields.first_name') }}</label>
                <input class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" type="text" name="first_name" id="first_name" value="{{ old('first_name', '') }}" required>
                @if($errors->has('first_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('first_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.first_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="last_name">{{ trans('cruds.contact.fields.last_name') }}</label>
                <input class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" type="text" name="last_name" id="last_name" value="{{ old('last_name', '') }}" required>
                @if($errors->has('last_name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('last_name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.last_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="email">{{ trans('cruds.contact.fields.email') }}</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email') }}" required>
                @if($errors->has('email'))
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.email_helper') }}</span>
            </div>
            <div class="form-group">
                <div class="form-check {{ $errors->has('newsletter') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="newsletter" value="0">
                    <input class="form-check-input" type="checkbox" name="newsletter" id="newsletter" value="1" {{ old('newsletter', 0) == 1 ? 'checked' : '' }}>
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
                <div class="form-check {{ $errors->has('create_user') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="create_user" value="0">
                    <input class="form-check-input" type="checkbox" name="create_user" id="create_user" value="1" {{ old('create_user', 0) == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="create_user">{{ trans('cruds.contact.fields.create_user') }}</label>
                </div>
                @if($errors->has('create_user'))
                    <div class="invalid-feedback">
                        {{ $errors->first('create_user') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.create_user_helper') }}</span>
            </div>

            <div class="form-group">
                <div class="form-check {{ $errors->has('is_driver') ? 'is-invalid' : '' }}">
                    <input type="hidden" name="is_driver" value="0">
                    <input class="form-check-input" type="checkbox" name="is_driver" id="is_driver" value="1" {{ old('is_driver', 0) == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_driver">{{ trans('cruds.contact.fields.is_driver') }}</label>
                </div>
                @if($errors->has('is_driver'))
                    <div class="invalid-feedback">
                        {{ $errors->first('is_driver') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.contact.fields.is_driver_helper') }}</span>
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