@extends('layouts.admin')
@section('content')

<div class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Certificaat details</div>
                <div class="card-body">
                    <h5>{{ $certificate->name }}</h5>
                    <p><strong>Categorie:</strong> {{ $certificate->category->name ?? 'Onbekend' }}</p>
                    <p><strong>Chauffeur:</strong>
                        @if(isset($certificate->driver->id))
                            <a href="{{ route('admin.drivers.show', $certificate->driver->id) }}">{{ $certificate->driver->driver_name ?? ($certificate->driver->contact->first_name . ' ' . $certificate->driver->contact->last_name ?? 'Niet gevonden') }}</a>
                        @else
                            Niet gevonden
                        @endif
                    </p>
                    <p><strong>Vervaldatum:</strong> {{ $certificate->expiry_date ?? '-' }}</p>
                    <p><strong>Melding datum:</strong> {{ $certificate->notify_date ?? '-' }}</p>
                </div>
                <div class="card-footer text-end">
                    @can('certificate_access')
                        <a href="{{ route('admin.certificate.edit', $certificate->id) }}" class="btn btn-primary btn-sm me-2">Bewerk</a>
                    @endcan
                    <a href="javascript:history.back()" class="btn btn-secondary btn-sm">Terug</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
