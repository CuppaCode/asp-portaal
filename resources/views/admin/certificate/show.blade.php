@extends('layouts.admin')
@section('content')

<div class="content">
    <div class="row">
        <div class="col-md-8">
            @if($isNearExpiry)
                <div class="alert alert-warning">
                    <strong>Geldigheid in een oogopslag:</strong>
                    dit document verloopt binnen {{ $nearExpiryThreshold }} dagen.
                    @if(!is_null($daysUntilExpiry))
                        <span class="ml-1">(nog {{ $daysUntilExpiry }} dagen)</span>
                    @endif
                </div>
            @elseif(!is_null($daysUntilExpiry) && $daysUntilExpiry < 0)
                <div class="alert alert-danger">
                    <strong>Geldigheid in een oogopslag:</strong>
                    dit document is {{ abs($daysUntilExpiry) }} dagen verlopen.
                </div>
            @endif

            <div class="card">
                <div class="card-header">Certificaat details</div>
                <div class="card-body">
                    <h5>{{ $certificate->name }}</h5>
                    @if($certificate->category)
                        <p><strong>Categoriecode:</strong> {{ $certificate->category->module_key }}</p>
                    @endif
                    <p><strong>Categorie:</strong> {{ $certificate->category->name ?? 'Onbekend' }}</p>
                    <p><strong>Chauffeur:</strong>
                        @if($certificate->driver && !$certificate->driver->trashed())
                            <a href="{{ route('admin.drivers.show', $certificate->driver->id) }}">{{ $certificate->driver->driver_name ?? ($certificate->driver->contact?->first_name . ' ' . $certificate->driver->contact?->last_name ?? 'Niet gevonden') }}</a>
                        @elseif($certificate->driver?->trashed())
                            Verwijderde chauffeur
                        @else
                            Niet gevonden
                        @endif
                    </p>
                    <p><strong>Vervaldatum:</strong> {{ $certificate->expiry_date ?? '-' }}</p>
                    @if($certificate->isRenewed())
                        <p><strong>Originele vervaldatum:</strong> {{ $certificate->original_expiry_date ? \Carbon\Carbon::parse($certificate->original_expiry_date)->format('d-m-Y') : '-' }}</p>
                        <span class="badge bg-success">Verlengd</span>
                    @endif
                    <p><strong>Datum afgifte:</strong> {{ $certificate->issue_date ?? '-' }}</p>
                    @php $cardNumber = $certificate->getMetaValue('card_number'); @endphp
                    @if(!empty($cardNumber))
                        <p><strong>Kaartnummer:</strong> {{ $cardNumber }}</p>
                    @endif
                </div>
                <div class="card-footer text-end">
                    @can('certificate_access')
                        <a href="{{ route('admin.certificate.edit', $certificate->id) }}" class="btn btn-primary btn-sm me-2">Bewerk</a>
                    @endcan
                    <a href="javascript:history.back()" class="btn btn-secondary btn-sm">Terug</a>
                </div>
            </div>

            @php
                $attachments = $certificate->getMedia('attachments');
            @endphp
            @if($attachments->isNotEmpty())
                <div class="card mt-3">
                    <div class="card-header">Certificaat en scans</div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($attachments as $attachment)
                                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                   href="{{ $attachment->getUrl() }}" target="_blank" rel="noopener">
                                    <span>{{ $attachment->file_name }}</span>
                                    <span class="text-muted small">{{ strtoupper(pathinfo($attachment->file_name, PATHINFO_EXTENSION)) }} · {{ number_format($attachment->size / 1024, 0, ',', '.') }} KB</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif 

            @if($certificate->metaFields->isNotEmpty())
                <div class="card mt-3">
                    <div class="card-header">Overige dossiergegevens</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Sleutel</th>
                                        <th>Waarde</th>
                                        <th>Type</th>
                                        <th>Zichtbaarheid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($certificate->metaFields as $metaField)
                                        <tr>
                                            <td>{{ $metaField->field_key }}</td>
                                            <td>{{ $metaField->masked_display_value ?? $metaField->resolved_value ?? '-' }}</td>
                                            <td>{{ $metaField->value_type }}</td>
                                            <td>{{ $metaField->visibility_scope }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($moduleSections))
                <div class="card mt-3">
                    <div class="card-header">Categorie uitbreiding</div>
                    <div class="card-body">
                        @foreach($moduleSections as $section)
                            <h6>{{ $section['title'] ?? 'Sectie' }}</h6>

                            @if(!empty($section['rows']) && is_array($section['rows']))
                                <ul class="mb-3">
                                    @foreach($section['rows'] as $row)
                                        <li><strong>{{ $row['label'] ?? '-' }}:</strong> {{ $row['value'] ?? '-' }}</li>
                                    @endforeach
                                </ul>
                            @elseif(!empty($section['content']))
                                <p>{{ $section['content'] }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

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

            @php
                $isCode95 = strtolower(trim((string) ($certificate->category?->code ?? ''))) === 'code_95'
                    || in_array(strtolower(trim((string) ($certificate->category?->name ?? ''))), ['code 95', 'code95', 'code_95'], true);
            @endphp

            @if($isCode95)
                @php
                    $code95Courses = $certificate->code95Courses;
                    $code95Total = (int) $code95Courses->sum('hours');
                    $code95Practice = (int) $code95Courses->where('type', 'practice')->sum('hours');
                    $cycleStart = $code95Courses->isNotEmpty() ? \Carbon\Carbon::parse($code95Courses->min('course_date')) : null;
                    $cycleEnd = $cycleStart ? $cycleStart->copy()->addYears(5) : null;
                @endphp

                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Cursusgeschiedenis Code 95</strong>
                        <span class="text-muted small">{{ $code95Total }} van 35 uur · {{ $code95Practice }} praktijkuur</span>
                    </div>
                    <div class="card-body">
                        @if($cycleStart && $cycleEnd)
                            <div class="alert alert-light border py-2">
                                <strong>Cyclus:</strong>
                                {{ $cycleStart->format('d-m-Y') }} t/m {{ $cycleEnd->format('d-m-Y') }}
                                <span class="text-muted"> — afgeleid uit de oudste cursusdatum.</span>
                            </div>
                        @endif

                        @if($code95Courses->isEmpty())
                            <div class="text-muted mb-3">Nog geen gevolgde cursussen toegevoegd.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Gevolgd op</th>
                                            <th>Cursus</th>
                                            <th>Type</th>
                                            <th>Uren</th>
                                            <th>Opleider</th>
                                            @can('certificate_access')<th></th>@endcan
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($code95Courses as $course)
                                            <tr>
                                                <td>{{ $course->course_date->format('d-m-Y') }}</td>
                                                <td>{{ $course->name }} ({{ $course->code }})</td>
                                                <td>
                                                    @if($course->type === 'practice')
                                                        <span class="badge bg-success">Praktijk</span>
                                                    @else
                                                        <span class="badge bg-primary">Theorie</span>
                                                    @endif
                                                </td>
                                                <td>{{ $course->hours }}</td>
                                                <td>{{ $course->trainer ?: '-' }}</td>
                                                @can('certificate_access')
                                                    <td class="text-end">
                                                        <form action="{{ route('admin.certificate.code95-courses.destroy', $course->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link btn-sm text-danger p-0" onclick="return confirm('Deze cursus verwijderen?')" title="Verwijderen"><i class="fa fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                @endcan
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @can('certificate_access')
                            <form action="{{ route('admin.certificate.code95-courses.store', $certificate->id) }}" method="POST" class="border-top pt-3">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        <label class="form-label" for="course_date">Gevolgd op</label>
                                        <input type="date" class="form-control" id="course_date" name="course_date" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="course_name">Cursusnaam</label>
                                        <input type="text" class="form-control" id="course_name" name="name" required placeholder="Bijv. Het Nieuwe Rijden">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="course_code">Cursuscode</label>
                                        <input type="text" class="form-control" id="course_code" name="code" required placeholder="W01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="course_type">Type</label>
                                        <select class="form-control" id="course_type" name="type" required>
                                            <option value="theory">Theorie</option>
                                            <option value="practice">Praktijk</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label" for="course_hours">Uren</label>
                                        <input type="number" class="form-control" id="course_hours" name="hours" min="1" max="24" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label" for="course_trainer">Opleider</label>
                                        <input type="text" class="form-control" id="course_trainer" name="trainer" placeholder="Naam">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm mt-3"><i class="fa fa-plus"></i> Cursus toevoegen</button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
