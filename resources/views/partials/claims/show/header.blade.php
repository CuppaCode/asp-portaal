<div class="top-bar-claims form-group d-flex justify-content-between align-items-center">
    <a class="btn btn-dark" href="{{ route('admin.claims.index') }}">
        {{ trans('global.back_to_list') }}
    </a>

    @if ($isAdminOrAgent)

    @if ($claim->assign_self == true)
        <div class="alert alert-danger" role="alert">
            Let op! Dit schadedossier wordt behandeld door klant zelf.
        </div>
    @endif

@endif


    <div>
        @if($sla)
            @can('sla_access')
                <button id="sla-toggle" type="button" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-arrow-down" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1">
                        </path>
                    </svg>
                    SLA details
                </button>
            @endcan
        @endif
        @if( $claim->assign_self || $isAdminOrAgent)
            <a class="btn btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                {{ trans('global.edit') }}
            </a>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-6 offset-md-6" style="position: relative;">
        <div class="card card-sla text-white bg-blue-asp sla-show hide">
            <div class="card-header">
                SLA Details
            </div>
            <div class="card-body">
                @if ($sla)
                <table class="table table-sm text-white">
                    <tbody>
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
                                @if ($sla->label)
                                    {{ App\Models\SLA::LABEL_SELECT[$sla->label] }}
                                @endif
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
                                @if ($sla->reports !== null)
                                    {{ App\Models\SLA::REPORT_SELECT[$sla->reports] }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.sla.fields.analytics') }}
                            </th>
                            <td>
                                @if ($sla->analytics_options !== null)
                                    @foreach (json_decode($sla->analytics_options) as $options)
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
                @endif
            </div>
        </div>
    </div>
</div>