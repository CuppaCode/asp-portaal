@extends('layouts.admin')
@section('content')

@php $user = auth()->user(); @endphp

<div class="content">
    <div class="row">
        <div class="col-md-12">
            {{-- Bulk Renewal Toolbar --}}
            @can('certificate_access')
            <div class="card mb-3" id="bulkRenewalToolbar" style="display: none;">
                <div class="card-body">
                    <form action="{{ route('certificate.bulk-renew') }}" method="POST" id="bulkRenewalForm">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="bulk_new_expiry_date" class="form-label">Nieuwe vervaldatum voor geselecteerde certificaten</label>
                                <input type="date" class="form-control" id="bulk_new_expiry_date" name="new_expiry_date" required>
                            </div>
                            <div class="col-md-4">
                                <label for="bulk_notes" class="form-label">Notities (optioneel)</label>
                                <input type="text" class="form-control" id="bulk_notes" name="notes" placeholder="Bijv. nieuwe scans ontvangen">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success me-2">
                                    <i class="fa fa-refresh"></i> <span id="selectedCount">0</span> certificaten verlengen
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="deselectAll()">Annuleren</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endcan

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Overzicht certificaten per categorie</span>
                    @can('certificate_access')
                    <div>
                        <input type="checkbox" id="selectAllCertificates" onchange="toggleSelectAll()">
                        <label for="selectAllCertificates" class="ms-1">Selecteer alles</label>
                    </div>
                    @endcan
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
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        @can('certificate_access')
                                                        <div class="form-check">
                                                            <input class="form-check-input certificate-checkbox" type="checkbox" name="certificate_ids[]" value="{{ $certificate->id }}" onchange="updateBulkToolbar()">
                                                        </div>
                                                        @endcan
                                                        <div class="flex-grow-1 ms-2">
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
function updateBulkToolbar() {
    const checkboxes = document.querySelectorAll('.certificate-checkbox:checked');
    const count = checkboxes.length;
    const toolbar = document.getElementById('bulkRenewalToolbar');
    const countSpan = document.getElementById('selectedCount');
    
    if (count > 0) {
        toolbar.style.display = 'block';
        countSpan.textContent = count;
        
        // Update hidden input values in form
        const form = document.getElementById('bulkRenewalForm');
        // Remove existing certificate_ids inputs
        form.querySelectorAll('input[name="certificate_ids[]"]').forEach(input => input.remove());
        
        // Add selected certificate IDs
        checkboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'certificate_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });
    } else {
        toolbar.style.display = 'none';
    }
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAllCertificates');
    const checkboxes = document.querySelectorAll('.certificate-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkToolbar();
}

function deselectAll() {
    const checkboxes = document.querySelectorAll('.certificate-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAllCertificates').checked = false;
    updateBulkToolbar();
}
</script>
@endsection
