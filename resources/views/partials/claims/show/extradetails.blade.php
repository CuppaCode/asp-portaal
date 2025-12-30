<div class="col-md-6">
    @if (
        !empty($claim->recovery_office_x) || !empty($claim->expertise_office_x))

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Extra details

                @if( $claim->assign_self || $isAdminOrAgent)
                    <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}#extra-details">
                        {{ trans('global.edit') }}
                    </a>
                @endif
            </div>

            <div class="card-body">
                @if ($claim->recovery_office_x)
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.recovery_office') }}
                    </div>
                    <p class="card-text">
                        {{ $claim->recovery_office_x }}
                    </p>
                @endif
                @if ($claim->expertise_office_x)
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.expertise_office') }}
                    </div>
                    <p class="card-text">
                        {{ $claim->expertise_office_x }}
                    </p>
                @endif
                @if (!empty($claim->requested_at))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.requested_at') }}
                    </div>
                    <p class="card-text">
                        {{ $claim->requested_at }}
                    </p>
                @endif
                @if ($claim->expert_report_is_in && !empty($claim->report_received_at))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.report_received_at') }}
                    </div>
                    <p class="card-text">
                        {{ $claim->report_received_at }}
                    </p>
                @endif
            </div>
        </div>
    @endif
</div>