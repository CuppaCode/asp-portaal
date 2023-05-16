@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.vehicle.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.vehicles.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.vehicle.fields.id') }}
                        </th>
                        <td>
                            {{ $vehicle->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vehicle.fields.company') }}
                        </th>
                        <td>
                            {{ $vehicle->company->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vehicle.fields.name') }}
                        </th>
                        <td>
                            {{ $vehicle->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.vehicle.fields.plates') }}
                        </th>
                        <td>
                            {{ $vehicle->plates }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.vehicles.index') }}">
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
            <a class="nav-link" href="#vehicle_drivers" role="tab" data-toggle="tab">
                {{ trans('cruds.driver.title') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#vehicle_claims" role="tab" data-toggle="tab">
                {{ trans('cruds.claim.title') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" role="tabpanel" id="vehicle_drivers">
            @includeIf('admin.vehicles.relationships.vehicleDrivers', ['drivers' => $vehicle->vehicleDrivers])
        </div>
        <div class="tab-pane" role="tabpanel" id="vehicle_claims">
            @includeIf('admin.vehicles.relationships.vehicleClaims', ['claims' => $vehicle->vehicleClaims])
        </div>
    </div>
</div>

@endsection