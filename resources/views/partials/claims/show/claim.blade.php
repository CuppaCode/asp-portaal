<div class="col-md-5">
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
            @if ($claim->injury == 'other')
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

            <div class="card-title">
                Verwijtbaar
            </div>
            <p class="card-text">{{ App\Models\Claim::VERWIJTBAAR_SELECT[$claim->verwijtbaar] ?? '-' }}</p>
        </div>
    </div>

    @if (!empty($claim->custom_fields_data))
        @php
            $customFields = $claim->company->customClaimFields;
        @endphp

        @if ($customFields->isNotEmpty())
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Aanvullende Gegevens

                    @if( $claim->assign_self || $isAdminOrAgent)
                        <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                            {{ trans('global.edit') }}
                        </a>
                    @endif
                </div>

                <div class="card-body">
                    @foreach($customFields as $customField)
                        @if(isset($claim->custom_fields_data[$customField->field_name]))
                            <div class="card-title">
                                {{ $customField->field_label }}
                            </div>
                            <p class="card-text">{{ $claim->custom_fields_data[$customField->field_name] }}</p>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>