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
            </div>
        </div>
    @endif
</div>