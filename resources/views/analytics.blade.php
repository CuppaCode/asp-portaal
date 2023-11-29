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
                                <label for="a_company_id">{{ trans('cruds.claim.fields.company') }}</label>
                                <select class="form-control select2 {{ $errors->has('company') ? 'is-invalid' : '' }}" name="a_company_id" id="a_company_id">
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
        <div class="col-md-6">
            <div class="card">  
                <div class="card-body">
                    <form id="getAnalData" method="">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="datetimepicker1Input" class="form-label">Start</label>       
                                    <div class="input-group log-event" id="datetimepicker1" data-td-target-input="nearest" data-td-target-toggle="nearest">         
                                        <input id="datetimepicker1Input" type="text" class="form-control" data-td-target="#datetimepicker1" data-td-toggle="datetimepicker"/>         
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="datetimepicker2Input" class="form-label">Eind</label>       
                                    <div class="input-group log-event" id="datetimepicker2" data-td-target-input="nearest" data-td-target-toggle="nearest">         
                                        <input id="datetimepicker2Input" type="text" class="form-control" data-td-target="#datetimepicker2" data-td-toggle="datetimepicker" value="{{ $dn }}"/>         
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mt-2 ">
                                    <div class="input-group justify-content-end">     
                                        <input type="submit" class="btn btn-success" value="OK"/>         
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
    </div>
    <hr class="hr" />
    <div style="width: 800px;"><canvas id="kind_accident"></canvas></div>
</div>

@endsection
@section('scripts')
@parent

@endsection