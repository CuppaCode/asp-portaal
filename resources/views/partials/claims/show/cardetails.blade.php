<div class="col-md-6">
    @if (
        !empty($claim->vehicle->name) ||
            !empty($claim->damaged_part) ||
            !empty($claim->damage_origin) ||
            !empty($claim->damaged_area) ||
            !empty($claim->driver_vehicle))
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Gegevens wagenpark

                @unless (!$claim->assign_self && !$isAdminOrAgent)
                    <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}#info-car">
                        {{ trans('global.edit') }}
                    </a>
                @endunless
            </div>

            <div class="card-body">
                @if (!empty($claim->vehicle->name))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.vehicle') }}
                    </div>
                    <p class="card-text">{{ $claim->vehicle->name ?? '' }}</p>
                @endif
                @if (!empty($claim->damaged_part))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.damaged_part') }}
                    </div>
                    <p class="card-text">
                        @if ($claim->damaged_part !== null)
                            @foreach (json_decode($claim->damaged_part) as $part)
                                <span
                                    class="badge badge-success">{{ App\Models\Claim::DAMAGED_PART_SELECT[$part] }}</span>
                            @endforeach
                        @endif
                    </p>
                @endif

                @if (!empty($claim->damage_origin))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.damage_origin') }}
                    </div>
                    <p class="card-text">
                        @if ($claim->damage_origin !== null)
                            @foreach (json_decode($claim->damage_origin) as $origin)
                                <span
                                    class="badge badge-success">{{ App\Models\Claim::DAMAGE_ORIGIN[$origin] }}</span>
                            @endforeach
                        @endif
                    </p>
                @endif

                @if (!empty($claim->damaged_area))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.damaged_area') }}
                    </div>

                    <p class="card-text">
                        @if ($claim->damaged_area !== null)
                            @foreach (json_decode($claim->damaged_area) as $area)
                                <span
                                    class="badge badge-success">{{ App\Models\Claim::DAMAGED_AREA_SELECT[$area] }}</span>
                            @endforeach
                        @endif
                    </p>
                    <p class="card-text">{{ App\Models\Claim::DAMAGED_AREA_SELECT[$claim->damaged_area] ?? '' }}
                    </p>
                @endif

                @if (!empty($claim->driver_vehicle))
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.driver_vehicle') }}
                    </div>
                    <p class="card-text">
                        {{ App\Models\Driver::find($claim->driver_vehicle)->driver_full_name ?? '' }}</p>
                @endif
            </div>
        </div>
    @endif
</div>