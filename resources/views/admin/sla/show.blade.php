@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.sla.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.sla.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>

            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.sla.fields.id') }}
                        </th>
                        <td>
                            {{ $sla->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sla.fields.company') }}
                        </th>
                        <td>
                            {{ $sla->company->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sla.fields.startdate') }}
                        </th>
                        <td>
                            {{ $sla->startdate ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sla.fields.enddate') }}
                        </th>
                        <td>
                            {{ $sla->enddate ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sla.fields.label') }}
                        </th>
                        <td>
                            {{ App\Models\SLA::LABEL_SELECT[$sla->label] }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sla.fields.amount_users') }}
                        </th>
                        <td>
                            {{ $sla->amount_users }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sla.fields.max_amount') }}
                        </th>
                        <td>
                            &euro; {{ $sla->max_amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sla.fields.reports') }}
                        </th>
                        <td>
                            {{ App\Models\SLA::REPORT_SELECT[$sla->reports] }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sla.fields.analytics') }}
                        </th>
                        <td>
                            @if($sla->analytics_options !== null)
                                @foreach(json_decode($sla->analytics_options) as $options)
                                    {{ App\Models\SLA::ANALYTICS_SELECT[$options] }},
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.sla.fields.other') }}
                        </th>
                        <td>
                            {{ $sla->other }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.contacts.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection