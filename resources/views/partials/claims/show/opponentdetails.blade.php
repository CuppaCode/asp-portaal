<div class="col-md-6">
    @if (
        !empty($claim->vehicle_opposite) ||
            !empty($claim->damaged_part_opposite) ||
            !empty($claim->damage_origin_opposite) ||
            !empty($claim->damaged_area_opposite) ||
            !empty($claim->driver_vehicle_opposite))

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Gegevens wederpartij

                @if( $claim->assign_self || $isAdminOrAgent)
                    <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                        {{ trans('global.edit') }}
                    </a>
                @endif
            </div>

            <div class="card-body">
                @if ($claim->opposite_type != 'obstacle')
                    @if (!empty($claim->vehicle_opposite))
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.vehicle_opposite') }}
                        </div>
                        <p class="card-text">{{ $claim->vehicle_opposite->name ?? '' }}</p>
                    @endif
                    @if (!empty($claim->damaged_part_opposite))
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.damaged_part_opposite') }}
                        </div>
                        <p class="card-text">
                            @if ($claim->damaged_part_opposite !== null)
                                @foreach (json_decode($claim->damaged_part_opposite) as $part)
                                    <span
                                        class="badge badge-success">{{ App\Models\Claim::DAMAGED_PART_SELECT[$part] }}</span>
                                @endforeach
                            @endif
                        </p>
                    @endif
                    @if (!empty($claim->damage_origin_opposite))
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.damage_origin_opposite') }}
                        </div>
                        <p class="card-text">
                            @if ($claim->damage_origin_opposite !== null)
                                @foreach (json_decode($claim->damage_origin_opposite) as $origin)
                                    <span
                                        class="badge badge-success">{{ App\Models\Claim::DAMAGE_ORIGIN_OPPOSITE[$origin] }}
                                    </span>
                                @endforeach
                            @endif
                        </p>
                    @endif
                    @if (!empty($claim->damaged_area_opposite))
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.damaged_area_opposite') }}
                        </div>
                        <p class="card-text">
                            @if ($claim->damaged_area_opposite !== null)
                                @foreach (json_decode($claim->damaged_area_opposite) as $area)
                                    <span
                                        class="badge badge-success">{{ App\Models\Claim::DAMAGED_AREA_OPPOSITE_SELECT[$area] }}
                                    </span>
                                @endforeach
                            @endif
                        </p>
                    @endif
                    @if (!empty($claim->driver_vehicle_opposite))
                        <div class="card-title">
                            {{ trans('cruds.claim.fields.driver_vehicle_opposite') }}
                        </div>
                        <p class="card-text">
                            {{ App\Models\Driver::find($claim->driver_vehicle_opposite)->driver_full_name ?? '' }}
                        </p>
                    @endif
                @else
                    <div class="card-title">
                        {{ trans('cruds.claim.fields.obstacle') }}
                    </div>
                    <p class="card-text">{{ $claim->obstacle }}</p>
                @endif
            </div>
        </div>
    @endif
    @if (!empty($opposite))
        @if (!empty($opposite->name) || !empty($opposite->street) || !empty($opposite->phone) || !empty($opposite->email))
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Details wederpartij

                    @if( $claim->assign_self || $isAdminOrAgent)
                        <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                            {{ trans('global.edit') }}
                        </a>
                    @endif
                </div>

                <div class="card-body">
                    @if (!empty($opposite->name))
                        <div class="card-title">
                            {{ trans('cruds.opposite.fields.name') }}
                        </div>
                        <p class="card-text">{{ $opposite->name ?? '' }}</p>
                    @endif
                    @if (!empty($opposite->street))
                        <div class="card-title">
                            {{ trans('cruds.opposite.fields.street') }}
                        </div>
                        <p class="card-text">{{ $opposite->street ?? '' }}</p>
                    @endif
                    @if (!empty($opposite->zipcode))
                        <div class="card-title">
                            {{ trans('cruds.opposite.fields.zipcode') }} +
                            {{ trans('cruds.opposite.fields.city') }}
                        </div>
                        <p class="card-text">{{ $opposite->zipcode ?? '' }} {{ $opposite->city ?? '' }}</p>
                    @endif
                    @if (!empty($opposite->phone))
                        <div class="card-title">
                            {{ trans('cruds.opposite.fields.phone') }}
                        </div>
                        <p class="card-text">{{ $opposite->phone ?? '' }}</p>
                    @endif
                    @if (!empty($opposite->email))
                        <div class="card-title">
                            {{ trans('cruds.opposite.fields.email') }}
                        </div>
                        <p class="card-text"><a
                                href="mailto:{{ $opposite->email ?? '' }}">{{ $opposite->email ?? '' }}</a></p>
                    @endif

                </div>
            </div>
        @endif
    @endif
</div>