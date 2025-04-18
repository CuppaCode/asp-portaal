<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Schademelding

            @if( $claim->assign_self || $isAdminOrAgent)
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                    {{ trans('global.edit') }}
                </a>
            @endif
        </div>

        <div class="card-body">

            <div class="card-title">
                {{ trans('cruds.claim.fields.created_at') }}
            </div>
            <p class="card-text">{{ $claim->created_at }}</p>

            @if (!empty($claim->date_accident))
                <div class="card-title">
                    {{ trans('cruds.claim.fields.date_accident') }}
                </div>
                <p class="card-text">{{ $claim->date_accident }}</p>
            @endif

            @if (!empty($claim->injury))
                <div class="card-title">
                    {{ trans('cruds.claim.fields.injury') }}
                </div>
                <p class="card-text">{{ App\Models\Claim::INJURY_SELECT[$claim->injury] ?? '' }}</p>
            @endif
            @if ($claim->injury == 'yes')
                <div class="card-title">
                    {{ trans('cruds.claim.fields.injury_office') }}
                </div>
                <p class="card-text text-capitalize">
                    @if ($claim->injury_office != null)
                        {{ substr($claim->injury_office->identifier, 7) ?? '' }}
                    @endif
                </p>
            @elseif ($claim->injury == 'other')
                <div class="card-title">
                    {{ trans('cruds.claim.fields.injury_other') }}
                </div>
                <p class="card-text">{{ $claim->injury_other }}</p>
            @else
            @endif
            <div class="card-title">
                {{ trans('cruds.claim.fields.recoverable_claim') }}
            </div>
            <p class="card-text">{{ App\Models\Claim::RECOVERABLE_CLAIM_SELECT[$claim->recoverable_claim] ?? '' }}
            </p>
            @if (!empty($claim->damage_kind))
                <div class="card-title">
                    Soort schade
                </div>
                <p class="card-text">{{ App\Models\Claim::DAMAGE_KIND[$claim->damage_kind] ?? '' }}</p>
            @endif
        </div>
    </div>

    @if ($claim->loading_photos || $claim->unloading_photos || $claim->waybill_signed_at_loading || $claim->waybill_signed_at_unloading)

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Vrachtbrief

                @if( $claim->assign_self || $isAdminOrAgent)
                    <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                        {{ trans('global.edit') }}
                    </a>
                @endif
            </div>

            <div class="card-body">

                <div class="row">

                    @if ($claim->loading_photos)

                        <div class="col-md-6 mb-4">
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.loading_photos') }}
                            </div>
                            <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->loading_photos] }}</p>
                        </div>

                    @endif

                    @if ($claim->unloading_photos)

                        <div class="col-md-6 mb-4">
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.unloading_photos') }}
                            </div>
                            <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->unloading_photos] }}</p>
                        </div>
                    
                    @endif


                    @if ($claim->waybill_signed_at_loading)

                        <div class="col-md-6">
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.waybill_signed_at_loading') }}
                            </div>
                            <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->waybill_signed_at_loading] }}</p>
                        </div>

                    @endif
                    
                    @if ($claim->waybill_signed_at_unloading)

                        <div class="col-md-6">
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.waybill_signed_at_unloading') }}
                            </div>
                            <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->waybill_signed_at_unloading] }}</p>
                        </div>

                    @endif

                </div>

            </div>
        </div>

    @endif
</div>