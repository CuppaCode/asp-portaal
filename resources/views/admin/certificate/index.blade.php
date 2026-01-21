@extends('layouts.admin')
@section('content')

@php $user = auth()->user(); @endphp

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Overzicht certificaten per categorie
                </div>
                <div class="card-body">
                    @if(!empty($categories) && $categories->count())
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                            @foreach($categories as $category)
                                <div class="col">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center py-2">
                                            <div class="fw-bold">{{ $category->name }}</div>
                                            <div><span class="badge bg-secondary">{{ $category->certificates->count() }}</span></div>
                                        </div>
                                        <ul class="list-group list-group-flush small">
                                            @forelse($category->certificates as $certificate)
                                                @php
                                                    $expiry = $certificate->expiry_date ? \Carbon\Carbon::parse($certificate->expiry_date) : null;
                                                    $expired = $expiry ? $expiry->lte(\Carbon\Carbon::now()) : false;
                                                    $driverName = $certificate->driver->driver_name ?? ($certificate->driver->contact->first_name . ' ' . $certificate->driver->contact->last_name ?? 'Niet gevonden');
                                                @endphp
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-medium">{{ $driverName }}</div>
                                                        <div class="text-muted small">{{ $certificate->name }}</div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="mb-1">
                                                            <span class="badge @if($expired) bg-danger text-white @else bg-warning text-dark @endif">{{ $certificate->expiry_date ?? '-' }}</span>
                                                        </div>
                                                        <div>
                                                            @can('driver_show')
                                                            @if(isset($certificate->driver->id))
                                                                <a href="{{ route('admin.drivers.show', $certificate->driver->id) }}" class="btn btn-sm btn-outline-primary me-1">Chauffeur</a>
                                                            @endif
                                                            @endcan
                                                            @can('certificate_access')
                                                                <a href="{{ route('admin.certificate.show', $certificate->id) }}" class="btn btn-sm btn-success">Bekijk</a>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item">Geen certificaten</li>
                                            @endforelse
                                        </ul>
                                        <div class="card-footer text-end py-2">
                                            <a href="{{ route('admin.certificate-categories.show', $category->id) }}" class="btn btn-sm btn-link">Bekijk alle</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        Geen categorieën gevonden
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
// optional JS for future enhancements
</script>
@endsection
