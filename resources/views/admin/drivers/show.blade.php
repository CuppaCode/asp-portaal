@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.driver.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.drivers.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.driver.fields.company') }}
                        </th>
                        <td>
                            {{ $driver->contact->company->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.driver.fields.last_name') }}
                        </th>
                        <td>
                            {{ $driver->contact->last_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.driver.fields.email') }}
                        </th>
                        <td>
                            {{ $driver->contact->email ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.driver.fields.phone') }}
                        </th>
                        <td>
                            {{ $driver->contact->company->phone ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        {{ trans('cruds.driver.certificates') }}
    </div>
    <div class="card-body">


    </div>
</div>

@endsection