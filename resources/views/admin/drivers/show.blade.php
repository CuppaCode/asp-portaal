@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.driver.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ url()->previous() ?: route('admin.drivers.index') }}" onclick="event.preventDefault(); history.back();" aria-label="{{ trans('global.back') }}">
                    {{ trans('global.back') }}
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

@can('certificate_access')

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.certificate.create', $driver->id) }}">
                {{ trans('cruds.certificate.title_singular') }} aanmaken
            </a>
        </div>
    </div>


<div class="card">
    <div class="card-header">
        {{ trans('cruds.certificate.title') }}
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>
                        {{ trans('cruds.certificate.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.certificate.fields.notify_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.certificate.fields.expiry_date') }}
                    </th>
                </tr>
                @foreach($driver->certificates as $certificate)
                    <tr>
                        <td>
                            {{ $certificate->name }}
                        </td>
                        <td>
                            {{ $certificate->notify_date }}
                        </td>
                        <td>
                            {{ $certificate->expiry_date }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endcan

@endsection