@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.vehicleOpposite.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.vehicle-opposites.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.vehicleOpposite.fields.id') }}
                        </th>
                        <td>
                            {{ $vehicleOpposite->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vehicleOpposite.fields.name') }}
                        </th>
                        <td>
                            {{ $vehicleOpposite->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vehicleOpposite.fields.plates') }}
                        </th>
                        <td>
                            {{ $vehicleOpposite->plates }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vehicleOpposite.fields.driver') }}
                        </th>
                        <td>
                            @foreach($vehicleOpposite->drivers as $key => $driver)
                                <span class="label label-info">{{ $driver->last_name }}</span>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.vehicle-opposites.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('global.relatedData') }}
    </div>
    <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#vehicle_opposite_claims" role="tab" data-toggle="tab">
                {{ trans('cruds.claim.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="vehicle_opposite_claims">
            @includeIf('admin.vehicleOpposites.relationships.vehicleOppositeClaims', ['claims' => $vehicleOpposite->vehicleOppositeClaims])
        </div>
    </div>
</div>

@endsection