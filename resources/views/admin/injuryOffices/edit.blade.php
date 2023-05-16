@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.injuryOffice.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.injury-offices.update", [$injuryOffice->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="company_id">{{ trans('cruds.injuryOffice.fields.company') }}</label>
                <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="company_id" id="company_id" required>
                    @foreach($companies as $id => $entry)
                        <option value="{{ $id }}" {{ (old('company_id') ? old('company_id') : $injuryOffice->company->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('company'))
                    <span class="text-danger">{{ $errors->first('company') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.injuryOffice.fields.company_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="identifier">{{ trans('cruds.injuryOffice.fields.identifier') }}</label>
                <input class="form-control {{ $errors->has('identifier') ? 'is-invalid' : '' }}" type="text" name="identifier" id="identifier" value="{{ old('identifier', $injuryOffice->identifier) }}">
                @if($errors->has('identifier'))
                    <span class="text-danger">{{ $errors->first('identifier') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.injuryOffice.fields.identifier_helper') }}</span>
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