<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Contactgegevens klant

            @if( $claim->assign_self || $isAdminOrAgent)
                <a class="btn btn-xs btn-success" href="{{ route('admin.claims.edit', $claim->id) }}">
                    {{ trans('global.edit') }}
                </a>
            @endif
        </div>
        <div class="card-body">
            @isset($firstContact)
                <div class="card-title">
                    Naam
                </div>
                <p class="card-text">
                    {{ $firstContact->first_name }} {{ $firstContact->last_name }}
                </p>
                <div class="card-title">
                    Email
                </div>
                <p class="card-text">
                    <a href="mailto:{{ $firstContact->email }}">{{ $firstContact->email }} </a>
                </p>   
                <div class="card-title">
                    Telefoonnummer
                </div>
                <p class="card-text">
                    <a href="tel:{{ $claim->company->phone }}">{{ $claim->company->phone }} </a>
                </p>
            @else
                Nog geen contactpersoon bekend.
            @endisset

        </div>
    </div>
</div>