@php
    $hasRit2 = $claim->loading_photos_2 || $claim->unloading_photos_2 || $claim->waybill_signed_at_loading_2 || $claim->waybill_signed_at_unloading_2 || $claim->damaged_part_2 || $claim->damage_origin_2 || $claim->damaged_area_2 || $claim->vehicle_2_id;
@endphp

<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Gegevens opdrachtgever

            @if ($claim->assign_self || $isAdminOrAgent)
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}#info-car">
                    {{ trans('global.edit') }}
                </a>
            @endif
        </div>

        <div class="card-body">

            <div class="row">

                {{-- Rit 1 --}}
                <div class="{{ $hasRit2 ? 'col-md-6' : 'col-md-12' }}">
                    <div class="card-title font-weight-bold">Rit 1</div>

                    @if (!empty($claim->vehicle->plates))
                        <div class="card-title">Kenteken rit 1</div>
                        <p class="card-text">{{ $claim->vehicle->plates }}</p>
                    @endif

                    @if (!empty($claim->vehicle->brand))
                        <div class="card-title">Merk/type</div>
                        <p class="card-text">{{ $claim->vehicle->brand }}</p>
                    @endif

                    @if (!empty($claim->vehicle->chassis_number))
                        <div class="card-title">Chassisnummer</div>
                        <p class="card-text">{{ $claim->vehicle->chassis_number }}</p>
                    @endif

                    @if (!empty($claim->damaged_part))
                        <div class="card-title">{{ trans('cruds.claim.fields.damaged_part') }}</div>
                        <p class="card-text">
                            {{ implode(', ', array_map(fn($k) => App\Models\Claim::DAMAGED_PART_SELECT[$k] ?? $k, (array) json_decode($claim->damaged_part))) }}
                        </p>
                    @endif

                    @if (!empty($claim->damage_origin))
                        <div class="card-title">{{ trans('cruds.claim.fields.damage_origin') }}</div>
                        <p class="card-text">
                            {{ implode(', ', array_map(fn($k) => App\Models\Claim::DAMAGE_ORIGIN[$k] ?? $k, (array) json_decode($claim->damage_origin))) }}
                        </p>
                    @endif

                    @if (!empty($claim->damaged_area))
                        <div class="card-title">{{ trans('cruds.claim.fields.damaged_area') }}</div>
                        <p class="card-text">
                            {{ implode(', ', array_map(fn($k) => App\Models\Claim::DAMAGED_AREA_SELECT[$k] ?? $k, (array) json_decode($claim->damaged_area))) }}
                        </p>
                    @endif
                </div>

                {{-- Rit 2 (conditional) --}}
                @if ($hasRit2)
                    <div class="col-md-6">
                        <div class="card-title font-weight-bold">Rit 2</div>

                        @if (!empty($claim->vehicle2->plates))
                            <div class="card-title">Kenteken rit 2</div>
                            <p class="card-text">{{ $claim->vehicle2->plates }}</p>
                        @endif

                        @if (!empty($claim->vehicle2->brand))
                            <div class="card-title">Merk/type</div>
                            <p class="card-text">{{ $claim->vehicle2->brand }}</p>
                        @endif

                        @if (!empty($claim->vehicle2->chassis_number))
                            <div class="card-title">Chassisnummer</div>
                            <p class="card-text">{{ $claim->vehicle2->chassis_number }}</p>
                        @endif

@if (!empty($claim->damaged_part_2))
                        <div class="card-title">{{ trans('cruds.claim.fields.damaged_part') }}</div>
                        <p class="card-text">
                            {{ implode(', ', array_map(fn($k) => App\Models\Claim::DAMAGED_PART_SELECT[$k] ?? $k, (array) json_decode($claim->damaged_part_2))) }}
                        </p>
                    @endif

                    @if (!empty($claim->damage_origin_2))
                        <div class="card-title">{{ trans('cruds.claim.fields.damage_origin') }}</div>
                        <p class="card-text">
                            {{ implode(', ', array_map(fn($k) => App\Models\Claim::DAMAGE_ORIGIN[$k] ?? $k, (array) json_decode($claim->damage_origin_2))) }}
                        </p>
                    @endif

                    @if (!empty($claim->damaged_area_2))
                        <div class="card-title">{{ trans('cruds.claim.fields.damaged_area') }}</div>
                        <p class="card-text">
                            {{ implode(', ', array_map(fn($k) => App\Models\Claim::DAMAGED_AREA_SELECT[$k] ?? $k, (array) json_decode($claim->damaged_area_2))) }}
                            </p>
                        @endif
                    </div>
                @endif

            </div>

        </div>
    </div>
</div>
