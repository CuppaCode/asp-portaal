@can ('financial_access')
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Financieel

                        @if( $claim->assign_self || $isAdminOrAgent)
                            <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                                {{ trans('global.edit') }}
                            </a>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card-title">
                                    {{ trans('cruds.claim.fields.damage_costs') }}
                                </div>
                                <p class="card-text">&euro; {{ $claim->damage_costs }}</p>

                                <div class="card-title">
                                    {{ trans('cruds.claim.fields.recovery_costs') }}
                                </div>
                                <p class="card-text">&euro; {{ $claim->recovery_costs }}</p>

                                <div class="card-title">
                                    {{ trans('cruds.claim.fields.replacement_vehicle_costs') }}
                                </div>
                                <p class="card-text">&euro; {{ $claim->replacement_vehicle_costs }}</p>

                                <div class="card-title">
                                    {{ trans('cruds.claim.fields.expert_costs') }}
                                </div>
                                <p class="card-text">&euro; {{ $claim->expert_costs }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="card-title">
                                    {{ trans('cruds.claim.fields.other_costs') }}
                                </div>
                                <p class="card-text">&euro; {{ $claim->other_costs }}</p>

                                <div class="card-title">
                                    {{ trans('cruds.claim.fields.deductible_excess_costs') }}
                                </div>
                                <p class="card-text">&euro; {{ $claim->deductible_excess_costs }}</p>

                                <div class="card-title">
                                    {{ trans('cruds.claim.fields.insurance_costs') }}
                                </div>
                                <p class="card-text">&euro; {{ $claim->insurance_costs }}</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            ASP Financieel

                            @if( $claim->assign_self || $isAdminOrAgent)
                                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                                    {{ trans('global.edit') }}
                                </a>
                            @endif
                        </div>

                        <div class="card-body">
                            <div class="card-title">
                                {{ trans('cruds.claim.fields.invoice_settlement') }}
                            </div>
                            <p class="card-text">
                                @if ($claim->invoice_settlement_asp == 1)
                                    Ja
                                @else
                                    Nee
                                @endif
                            </p>

                            @if ($claim->invoice_comment)
                                <div class="card-title">
                                    {{ trans('cruds.claim.fields.invoice_comment') }}
                                </div>
                                <p class="card-text">
                                    {{ $claim->invoice_comment }}
                                </p>
                            @endif

                            @if ($claim->invoice_amount)
                                <div class="card-title">
                                    {{ trans('cruds.claim.fields.invoice_amount') }}
                                </div>
                                <p class="card-text">
                                    &euro; {{ $claim->invoice_amount }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
        </div>
    @endcan