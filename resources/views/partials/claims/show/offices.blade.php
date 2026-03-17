@php
    $recoveryCompany  = $claim->recovery_office?->company;
    $expertiseCompany = $claim->expertise_office?->company;
    $injuryCompany    = $claim->injury_office?->company;
@endphp

{{-- Schadehersteller --}}
@if ($claim->recovery_office)
<div class="col-md-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Schadehersteller
            @if ($claim->assign_self || $isAdminOrAgent)
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}#extra-details">
                    {{ trans('global.edit') }}
                </a>
            @endif
        </div>
        <div class="card-body">
            @if ($recoveryCompany)
                <div class="card-title">Naam</div>
                <p class="card-text">{{ $recoveryCompany->name }}</p>

                @if ($recoveryCompany->street)
                    <div class="card-title">Adres</div>
                    <p class="card-text">{{ $recoveryCompany->street }}</p>
                @endif

                @if ($recoveryCompany->zipcode || $recoveryCompany->city)
                    <div class="card-title">Postc/Stad</div>
                    <p class="card-text">{{ trim($recoveryCompany->zipcode . ' ' . $recoveryCompany->city) }}</p>
                @endif

                @if ($recoveryCompany->phone)
                    <div class="card-title">Telefoon</div>
                    <p class="card-text">{{ $recoveryCompany->phone }}</p>
                @endif

                @php $recoveryContact = $recoveryCompany->contacts->first(); @endphp
                @if ($recoveryContact?->email)
                    <div class="card-title">E-mail</div>
                    <p class="card-text">{{ $recoveryContact->email }}</p>
                @endif
            @endif

            @if ($claim->herstel_op)
                <div class="card-title">Herstel op</div>
                <p class="card-text">{{ $claim->herstel_op }}</p>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Expertise --}}
@if ($claim->expertise_office)
<div class="col-md-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Expertise
            @if ($claim->assign_self || $isAdminOrAgent)
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}#extra-details">
                    {{ trans('global.edit') }}
                </a>
            @endif
        </div>
        <div class="card-body">
            @if ($expertiseCompany)
                <div class="card-title">Naam</div>
                <p class="card-text">{{ $expertiseCompany->name }}</p>

                @if ($expertiseCompany->phone)
                    <div class="card-title">Telefoon</div>
                    <p class="card-text">{{ $expertiseCompany->phone }}</p>
                @endif

                @php $expertiseContact = $expertiseCompany->contacts->first(); @endphp
                @if ($expertiseContact?->email)
                    <div class="card-title">E-mail</div>
                    <p class="card-text">{{ $expertiseContact->email }}</p>
                @endif
            @endif

            @if ($claim->requested_at)
                <div class="card-title">Aangevraagd op</div>
                <p class="card-text">{{ $claim->requested_at }}</p>
            @endif

            @if ($claim->report_received_at)
                <div class="card-title">Rapport binnen</div>
                <p class="card-text">{{ $claim->report_received_at }}</p>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Letsel --}}
@if ($claim->injury_office)
<div class="col-md-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Letsel
            @if ($claim->assign_self || $isAdminOrAgent)
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}#extra-details">
                    {{ trans('global.edit') }}
                </a>
            @endif
        </div>
        <div class="card-body">
            @if ($injuryCompany)
                <div class="card-title">Naam</div>
                <p class="card-text">{{ $injuryCompany->name }}</p>

                @if ($injuryCompany->phone)
                    <div class="card-title">Telefoon</div>
                    <p class="card-text">{{ $injuryCompany->phone }}</p>
                @endif

                @php $injuryContact = $injuryCompany->contacts->first(); @endphp
                @if ($injuryContact?->email)
                    <div class="card-title">E-mail</div>
                    <p class="card-text">{{ $injuryContact->email }}</p>
                @endif
            @endif

            @if ($claim->injury_requested_at)
                <div class="card-title">Aangevraagd op</div>
                <p class="card-text">{{ $claim->injury_requested_at }}</p>
            @endif
        </div>
    </div>
</div>
@endif
