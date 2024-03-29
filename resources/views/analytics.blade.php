@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">  
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
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
                                            <div class="input-group log-event custom_datepicker" id="datetimepicker2" data-td-target-input="nearest" data-td-target-toggle="nearest">         
                                                <input id="datetimepicker2Input" type="text" class="form-control" data-td-target="#datetimepicker2" data-td-toggle="datetimepicker" value="{{ $dn }}"/>         
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mt-2 ">
                                            <div class="input-group justify-content-end">     
                                                <input type="submit" class="btn btn-success" value="Statistieken tonen"/>         
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="hr" />
    <div id="analytics-area" class="row d-none">
        <div class="col-md-6">
            <div class="card d-flex align-items-center">
                <div class="d-flex flex-column" style="width: 500px; height: 566.5px;">
                    <div class="kind_accident_legend">
                        <div class="legend_transportation"></div>
                        <div class="legend_traffic"></div>
                        <div class="legend_other"></div>
                    </div>
                    <canvas id="kind_accident"></canvas>
                    
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card d-flex align-items-center">
                <div style="width: 500px; height: 270px;"><canvas id="damage_costs"></canvas></div>
            </div>
            <div class="card d-flex align-items-center">
                <div style="width: 500px; height: 270px;"><canvas id="saved_costs"></canvas></div>
            </div>
        </div>
</div>

@endsection
@section('scripts')
@parent

@endsection