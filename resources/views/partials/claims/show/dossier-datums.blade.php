@php
    $nvt = $claim->dossier_nvt ?? [];
    $fields = [
        'bevestiging_kl_at'      => trans('cruds.claim.fields.bevestiging_kl_at'),
        'saf_binnen_at'          => trans('cruds.claim.fields.saf_binnen_at'),
        'info_chf_at'            => trans('cruds.claim.fields.info_chf_at'),
        'info_kl_wp_at'          => trans('cruds.claim.fields.info_kl_wp_at'),
        'beoordeling_at'         => trans('cruds.claim.fields.beoordeling_at'),
        'schadebedrag_bekend_at' => trans('cruds.claim.fields.schadebedrag_bekend_at'),
        'naar_vzk_at'            => trans('cruds.claim.fields.naar_vzk_at'),
        'naar_shb_gge_at'        => trans('cruds.claim.fields.naar_shb_gge_at'),
        'goedkeuring_og_at'      => trans('cruds.claim.fields.goedkeuring_og_at'),
        'factuur_ontvangen_at'   => trans('cruds.claim.fields.factuur_ontvangen_at'),
        'factuur_adm_at'         => trans('cruds.claim.fields.factuur_adm_at'),
        'brief_chf_at'           => trans('cruds.claim.fields.brief_chf_at'),
        'dossier_controle_at'    => trans('cruds.claim.fields.dossier_controle_at'),
        'dossier_heropend_at'    => trans('cruds.claim.fields.dossier_heropend_at'),
    ];
    $visibleFields = array_filter($fields, fn($label, $key) => $key === 'dossier_heropend_at' || $claim->$key || in_array($key, $nvt), ARRAY_FILTER_USE_BOTH);
    $left  = array_slice($visibleFields, 0, 7, true);
    $right = array_slice($visibleFields, 7, null, true);
@endphp

@if (count($visibleFields) > 0)
<div class="col-md-7">
    <div class="card" style="background-color: #eef1fb; border-color: #c5cef0;">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #d6ddf5; border-color: #c5cef0;">
            Dossier datums
            @if ($claim->assign_self || $isAdminOrAgent)
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}#dossier-datums">
                    {{ trans('global.edit') }}
                </a>
            @endif
        </div>
        <div class="card-body px-4 py-3">
            <div class="row">
                <div class="col-md-6">
                    @foreach ($left as $key => $label)
                        <div class="d-flex align-items-center py-2" style="border-bottom: 1px solid #c5cef0;">
                            <div class="font-weight-bold mr-3" style="min-width: 180px; font-size: 0.875rem;">{{ $label }}</div>
                            @if (in_array($key, $nvt) || ($key === 'dossier_heropend_at' && !$claim->$key))
                                <span class="text-muted">nvt</span>
                            @else
                                <span style="font-size: 0.875rem;">{{ $claim->$key }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="col-md-6">
                    @foreach ($right as $key => $label)
                        <div class="d-flex align-items-center py-2" style="border-bottom: 1px solid #c5cef0;">
                            <div class="font-weight-bold mr-3" style="min-width: 180px; font-size: 0.875rem;">{{ $label }}</div>
                            @if (in_array($key, $nvt) || ($key === 'dossier_heropend_at' && !$claim->$key))
                                <span class="text-muted">nvt</span>
                            @else
                                <span style="font-size: 0.875rem;">{{ $claim->$key }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
