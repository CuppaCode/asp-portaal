@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.view') }} {{ trans('cruds.certificateCategory.title_singular') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.certificate-categories.index') }}">{{ trans('global.back_to_list') ?? 'Back to list' }}</a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.certificateCategory.fields.id') }}</th>
                        <td>{{ $certificateCategory->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.certificateCategory.fields.name') }}</th>
                        <td>{{ $certificateCategory->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.certificateCategory.fields.duration') }}</th>
                        <td>{{ $certificateCategory->duration }} maanden</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        {{-- Certificates list as a table --}}
        <div class="mt-4">
            <h5>Certificaten in deze categorie</h5>
            @if($certificateCategory->certificates && $certificateCategory->certificates->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Chauffeur</th>
                                <th scope="col">Certificaat</th>
                                <th scope="col">Vervaldatum</th>
                                <th scope="col">Melding</th>
                                <th scope="col">Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificateCategory->certificates as $certificate)
                                @php
                                    $expiry = $certificate->expiry_date ? \Carbon\Carbon::parse($certificate->expiry_date) : null;
                                    $expired = $expiry ? $expiry->lte(\Carbon\Carbon::now()) : false;
                                    $driverName = $certificate->driver->driver_name ?? ($certificate->driver->contact->first_name . ' ' . $certificate->driver->contact->last_name ?? 'Niet gevonden');
                                @endphp
                                <tr class="@if($expired) table-danger @endif">
                                    <td>{{ $certificate->id }}</td>
                                    <td>
                                        @if(isset($certificate->driver->id))
                                            <a href="{{ route('admin.drivers.show', $certificate->driver->id) }}">{{ $driverName }}</a>
                                        @else
                                            {{ $driverName }}
                                        @endif
                                    </td>
                                    <td>{{ $certificate->name }}</td>
                                    <td><span class="badge @if($expired) bg-danger text-white @else bg-warning text-dark @endif">{{ $certificate->expiry_date ?? '-' }}</span></td>
                                    <td>{{ $certificate->notify_date ?? '-' }}</td>
                                    <td>
                                        @can('certificate_access')
                                            <a href="{{ route('admin.certificate.show', $certificate->id) }}" class="btn btn-sm btn-success">Bekijk</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted">Geen certificaten in deze categorie</div>
            @endif
        </div>
    </div>
</div>

@endsection
