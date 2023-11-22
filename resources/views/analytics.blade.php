@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="card">  
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="required" for="a_company_id">{{ trans('cruds.claim.fields.company') }}</label>
                                <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="a_company_id" id="a_company_id" required>
                                    @foreach($companies as $id => $entry)
                                    <option value="{{ $id }}" {{ (old('a_company_id') ? old('a_company_id') : $claim->company->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="hr" />
</div>

@endsection
@section('scripts')
@parent

@endsection