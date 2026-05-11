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
                    @if($certificate->isRenewed())
                        <p><strong>Originele vervaldatum:</strong> {{ $certificate->original_expiry_date ? \Carbon\Carbon::parse($certificate->original_expiry_date)->format('d-m-Y') : '-' }}</p>
                        <span class="badge bg-success">Verlengd</span>
                    @endif
                    <p><strong>Melding datum:</strong> {{ $certificate->notify_date ?? '-' }}</p>
                </div>
                <div class="card-footer text-end">
                    @can('certificate_access')
                        <a href="{{ route('admin.certificate.edit', $certificate->id) }}" class="btn btn-primary btn-sm me-2">Bewerk</a>
                    @endcan
                    <a href="javascript:history.back()" class="btn btn-secondary btn-sm">Terug</a>
                </div>
            </div>

            {{-- Manual Renewal Section --}}
            @can('certificate_access')
            <div class="card mt-3">
                <div class="card-header">Handmatige verlenging</div>
                <div class="card-body">
                    <form action="{{ route('admin.certificate.renew', $certificate->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_expiry_date" class="form-label">Nieuwe vervaldatum</label>
                                    <input type="date" class="form-control @error('new_expiry_date') is-invalid @enderror" 
                                           id="new_expiry_date" name="new_expiry_date" 
                                           min="{{ $certificate->expiry_date ? \Carbon\Carbon::parse($certificate->expiry_date)->addDay()->format('Y-m-d') : date('Y-m-d') }}"
                                           required>
                                    @error('new_expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notities (optioneel)</label>
                                    <input type="text" class="form-control @error('notes') is-invalid @enderror" 
                                           id="notes" name="notes" placeholder="Bijv. nieuwe scan ontvangen">
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-refresh"></i> Verlengen
                        </button>
                    </form>
                </div>
            </div>
            @endcan

            {{-- Renewal History --}}
            @if($certificate->renewals && $certificate->renewals->count() > 0)
            <div class="card mt-3">
                <div class="card-header">Verlengingsgeschiedenis</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Oude vervaldatum</th>
                                <th>Nieuwe vervaldatum</th>
                                <th>Verlengd door</th>
                                <th>Methode</th>
                                <th>Notities</th>
                                <th>Datum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificate->renewals()->orderByDesc('created_at')->get() as $renewal)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($renewal->old_expiry_date)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($renewal->new_expiry_date)->format('d-m-Y') }}</td>
                                <td>
                                    @if($renewal->renewedByUser)
                                        {{ $renewal->renewedByUser->name }}
                                    @elseif($renewal->renewed_by_email)
                                        {{ $renewal->renewed_by_email }}
                                    @else
                                        Onbekend
                                    @endif
                                </td>
                                <td>
                                    @if($renewal->renewal_method == 'email_link')
                                        <span class="badge bg-primary">Email link</span>
                                    @elseif($renewal->renewal_method == 'admin_manual')
                                        <span class="badge bg-info">Admin handmatig</span>
                                    @elseif($renewal->renewal_method == 'admin_bulk')
                                        <span class="badge bg-warning">Admin bulk</span>
                                    @else
                                        {{ $renewal->renewal_method }}
                                    @endif
                                </td>
                                <td>{{ $renewal->notes ?? '-' }}</td>
                                <td>{{ $renewal->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
