@php
    $hasRit2 = $claim->loading_photos_2 || $claim->unloading_photos_2 || $claim->waybill_signed_at_loading_2 || $claim->waybill_signed_at_unloading_2;
    $driverContact = $claim->driver_vehicle ? App\Models\Driver::find($claim->driver_vehicle)?->contact : null;
    $driver2Contact = $claim->driver_vehicle_2 ? App\Models\Driver::find($claim->driver_vehicle_2)?->contact : null;
    $hasChauffeur2 = !is_null($driver2Contact);
@endphp

<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Details AO

            @if ($claim->assign_self || $isAdminOrAgent)
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}#info-car">
                    {{ trans('global.edit') }}
                </a>
            @endif
        </div>

        <div class="card-body">

            {{-- Driver row --}}
            <div class="row mb-3">

                <div class="{{ $hasChauffeur2 ? 'col-md-6' : 'col-md-12' }}">
                    <div class="card-title font-weight-bold">Chauffeur 1</div>

                    @if ($driverContact)
                        <p class="card-text">{{ trim($driverContact->first_name . ' ' . $driverContact->last_name) }}</p>

                        @if (!empty($driverContact->email))
                            <div class="card-title">E-mail</div>
                            <p class="card-text">{{ $driverContact->email }}</p>
                        @endif

                        @if (!empty($driverContact->phone))
                            <div class="card-title">Telefoon</div>
                            <p class="card-text">{{ $driverContact->phone }}</p>
                        @endif
                    @else
                        <p class="card-text text-muted">-</p>
                    @endif
                </div>

                @if ($hasChauffeur2)
                    <div class="col-md-6">
                        <div class="card-title font-weight-bold">Chauffeur 2</div>

                        <p class="card-text">{{ trim($driver2Contact->first_name . ' ' . $driver2Contact->last_name) }}</p>

                        @if (!empty($driver2Contact->email))
                            <div class="card-title">E-mail</div>
                            <p class="card-text">{{ $driver2Contact->email }}</p>
                        @endif

                        @if (!empty($driver2Contact->phone))
                            <div class="card-title">Telefoon</div>
                            <p class="card-text">{{ $driver2Contact->phone }}</p>
                        @endif
                    </div>
                @endif

            </div>

            {{-- Waybill rows --}}
            @if ($claim->loading_photos || $claim->unloading_photos || $claim->waybill_signed_at_loading || $claim->waybill_signed_at_unloading || $hasRit2)
                <hr>

                <div class="row">
                    <div class="{{ $hasRit2 ? 'col-md-6' : 'col-md-12' }}">

                        @if ($claim->loading_photos)
                            <div class="card-title">{{ trans('cruds.claim.fields.loading_photos') }}</div>
                            <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->loading_photos] }}</p>
                        @endif

                        @if ($claim->unloading_photos)
                            <div class="card-title">{{ trans('cruds.claim.fields.unloading_photos') }}</div>
                            <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->unloading_photos] }}</p>
                        @endif

                        @if ($claim->waybill_signed_at_loading)
                            <div class="card-title">{{ trans('cruds.claim.fields.waybill_signed_at_loading') }}</div>
                            <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->waybill_signed_at_loading] }}</p>
                        @endif

                        @if ($claim->waybill_signed_at_unloading)
                            <div class="card-title">{{ trans('cruds.claim.fields.waybill_signed_at_unloading') }}</div>
                            <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->waybill_signed_at_unloading] }}</p>
                        @endif
                    </div>

                    @if ($hasRit2)
                        <div class="col-md-6">

                            @if ($claim->loading_photos_2)
                                <div class="card-title">{{ trans('cruds.claim.fields.loading_photos') }}</div>
                                <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->loading_photos_2] }}</p>
                            @endif

                            @if ($claim->unloading_photos_2)
                                <div class="card-title">{{ trans('cruds.claim.fields.unloading_photos') }}</div>
                                <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->unloading_photos_2] }}</p>
                            @endif

                            @if ($claim->waybill_signed_at_loading_2)
                                <div class="card-title">{{ trans('cruds.claim.fields.waybill_signed_at_loading') }}</div>
                                <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->waybill_signed_at_loading_2] }}</p>
                            @endif

                            @if ($claim->waybill_signed_at_unloading_2)
                                <div class="card-title">{{ trans('cruds.claim.fields.waybill_signed_at_unloading') }}</div>
                                <p class="card-text">{{ App\Models\Claim::WAYBILL_SELECT[$claim->waybill_signed_at_unloading_2] }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</div>
