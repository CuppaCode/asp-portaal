@extends('layouts.admin')
@section('content')

@php
    $contact = $driver->contact;
    $company = $contact?->company;
    $fullName = trim(($contact?->first_name ?? '') . ' ' . ($contact?->last_name ?? ''));
    $initials = strtoupper(substr((string)($contact?->first_name ?? 'X'), 0, 1) . substr((string)($contact?->last_name ?? 'X'), 0, 1));
    $documents = $driver->certificates->values();

    $code95Certificates = $driver->certificates
        ->filter(function ($certificate) {
            $category = $certificate->category;
            $code = strtolower(trim((string) ($category?->code ?? '')));
            $name = strtolower(trim((string) ($category?->name ?? '')));

            return $code === 'code_95'
                || in_array($name, ['code 95', 'code95', 'code_95'], true);
        })
        ->sortByDesc(function ($certificate) {
            $end = $certificate->getMetaValue('code95_cycle_end');
            return $end ? $end->format('Y-m-d') : ($certificate->getRawOriginal('expiry_date') ?: '');
        });

    $code95Certificate = $code95Certificates
        ->first(function ($certificate) {
            $end = $certificate->getMetaValue('code95_cycle_end');
            if (!$end) {
                return true;
            }

            return $end->copy()->endOfDay()->gte(now());
        }) ?? $code95Certificates->first();

    $code95Summary = null;
    if ($code95Certificate) {
        $code95Module = app(\App\Certificates\CertificateCategoryModuleRegistry::class)
            ->resolveByCategory($code95Certificate->category);
        $code95Summary = method_exists($code95Module, 'buildSummary')
            ? $code95Module->buildSummary($code95Certificate)
            : null;
    }

    $timelineStartYear = \Carbon\Carbon::today()->year;
    $timelineYears = range($timelineStartYear, $timelineStartYear + 4);
    $timelineStart = \Carbon\Carbon::create($timelineStartYear, 1, 1)->startOfDay();
    $timelineEnd = \Carbon\Carbon::create($timelineStartYear + 4, 12, 31)->endOfDay();
    $timelineRangeDays = max(1, $timelineStart->diffInDays($timelineEnd));
    $todayPercentage = max(0, min(100, ($timelineStart->diffInDays(\Carbon\Carbon::today()->endOfDay()) / $timelineRangeDays) * 100));

    $expiringSoonCount = $documents->filter(function ($certificate) {
        $raw = $certificate->getRawOriginal('expiry_date');
        if (empty($raw)) {
            return false;
        }

        $days = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($raw), false);
        return $days >= 0 && $days <= 90;
    })->count();
@endphp

<div class="driver-dossier card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
            <div class="d-flex align-items-center mb-2">
                <div class="driver-avatar mr-3">{{ $initials }}</div>
                <div>
                    <h4 class="mb-0">{{ $fullName !== '' ? $fullName : 'Chauffeur' }}</h4>
                    <div class="text-muted">
                        {{ $driver->function ?: 'Chauffeur' }}
                        @if(!empty($company?->name))
                            bij
                            @can('company_show')
                                <a href="{{ route('admin.companies.show', $company->id) }}">{{ $company->name }}</a>
                            @else
                                {{ $company->name }}
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex">
                <a class="btn btn-default mr-2" href="{{ url()->previous() ?: route('admin.drivers.index') }}" onclick="event.preventDefault(); history.back();" aria-label="{{ trans('global.back') }}">{{ trans('global.back') }}</a>
                @can('certificate_access')
                    <a class="btn btn-success" href="{{ route('admin.certificate.create', $driver->id) }}">{{ trans('cruds.certificate.title_singular') }} toevoegen</a>
                @endcan
            </div>
        </div>

        <div class="overview-strip mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Geldigheid in een oogopslag</h5>
                <span class="text-muted small">{{ $expiringSoonCount }} document(en) verlopen binnen 90 dagen.</span>
            </div>
            <div class="overview-grid-head">
                <div></div>
                <div class="overview-year-scale" aria-hidden="true">
                    @foreach($timelineYears as $index => $year)
                        <span class="{{ $index === 0 ? 'is-first' : '' }}" style="left: {{ $index * 20 }}%;">{{ $year }}</span>
                    @endforeach
                </div>
                <div></div>
            </div>
            <div class="overview-rows">
            @if($documents->isNotEmpty())
                <div class="overview-today" aria-hidden="true">
                    <span class="overview-today-line" style="left: {{ round($todayPercentage, 2) }}%;">
                        <span class="overview-today-label">vandaag</span>
                    </span>
                </div>
            @endif
            @forelse($documents as $certificate)
                @php
                    $expiryRaw = $certificate->getRawOriginal('expiry_date');
                    $daysUntilExpiry = !empty($expiryRaw) ? \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($expiryRaw), false) : null;
                    $fillPercentage = 0;
                    if (!empty($expiryRaw)) {
                        $expiryDate = \Carbon\Carbon::parse($expiryRaw)->endOfDay();
                        $boundedExpiry = $expiryDate->lt($timelineStart)
                            ? $timelineStart->copy()
                            : ($expiryDate->gt($timelineEnd) ? $timelineEnd->copy() : $expiryDate);
                        $fillPercentage = max(0, min(100, ($timelineStart->diffInDays($boundedExpiry) / $timelineRangeDays) * 100));
                    }

                    $barClass = 'is-ok';
                    if (!is_null($daysUntilExpiry) && $daysUntilExpiry < 0) {
                        $barClass = 'is-expired';
                    } elseif (!is_null($daysUntilExpiry) && $daysUntilExpiry <= 90) {
                        $barClass = 'is-warning';
                    }
                @endphp
                <div class="overview-row">
                    <div class="overview-name">{{ $certificate->category?->name ?: $certificate->name }}</div>
                    <div class="overview-bar-wrap">
                        <div class="overview-bar {{ $barClass }}" style="width: {{ round($fillPercentage, 2) }}%;"></div>
                    </div>
                    <div class="overview-date">{{ $certificate->expiry_date ?: '-' }}</div>
                </div>
            @empty
                <div class="text-muted">Geen documenten beschikbaar.</div>
            @endforelse
            </div>
        </div>

        @if($code95Certificate && $code95Summary)
            @php
                $totalPercentage = $code95Summary['required'] > 0
                    ? min(100, ($code95Summary['completedBounded'] / $code95Summary['required']) * 100)
                    : 0;
                $practicePercentage = $code95Summary['practiceRequired'] > 0
                    ? min(100, ($code95Summary['practiceCompleted'] / $code95Summary['practiceRequired']) * 100)
                    : 100;
            @endphp
            <div class="code95-card mb-4">
                <div class="code95-head">
                    <div>
                        <div class="code95-kicker">Code 95</div>
                        <h5 class="mb-0">Nascholingscyclus</h5>
                    </div>
                    <div class="code95-actions">
                        <span class="code95-status {{ $code95Summary['statusClass'] }}">{{ $code95Summary['status'] }}</span>
                        @can('certificate_access')
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.certificate.edit', $code95Certificate->id) }}">Bewerk</a>
                        @endcan
                    </div>
                </div>

                <div class="code95-summary-grid">
                    <div class="code95-summary-item">
                        <div class="code95-summary-label">Begin cyclus</div>
                        <div class="code95-summary-value">{{ $code95Summary['cycleStart'] ? $code95Summary['cycleStart']->format('d-m-Y') : '-' }}</div>
                    </div>
                    <div class="code95-summary-item">
                        <div class="code95-summary-label">Eind cyclus</div>
                        <div class="code95-summary-value">{{ $code95Summary['cycleEnd'] ? $code95Summary['cycleEnd']->format('d-m-Y') : '-' }}</div>
                    </div>
                    <div class="code95-summary-item">
                        <div class="code95-summary-label">Gevolgd</div>
                        <div class="code95-summary-value">{{ $code95Summary['completed'] }} van {{ $code95Summary['required'] }} uur</div>
                    </div>
                    <div class="code95-summary-item">
                        <div class="code95-summary-label">Praktijk</div>
                        <div class="code95-summary-value">{{ $code95Summary['practiceCompleted'] }} van minimaal {{ $code95Summary['practiceRequired'] }} uur</div>
                    </div>
                </div>

                <div class="code95-segmented-progress" role="progressbar" aria-label="Nascholingsvoortgang" aria-valuemin="0" aria-valuemax="{{ $code95Summary['required'] }}" aria-valuenow="{{ $code95Summary['completed'] }}">
                    @foreach($code95Certificate->code95Courses->sortBy('course_date') as $course)
                        @php
                            $segmentWidth = $code95Summary['required'] > 0
                                ? min(100, (($course->hours / $code95Summary['required']) * 100))
                                : 0;
                        @endphp
                        <div class="code95-progress-segment {{ $course->type === 'practice' ? 'is-practice' : 'is-theory' }}"
                             style="width: {{ round($segmentWidth, 2) }}%;"
                             title="{{ $course->code }} — {{ $course->hours }} uur {{ $course->type === 'practice' ? 'praktijk' : 'theorie' }}">
                        </div>
                    @endforeach
                    @if($code95Summary['remaining'] > 0)
                        <div class="code95-progress-segment is-open"
                             style="width: {{ round(min(100, (($code95Summary['remaining'] / $code95Summary['required']) * 100)), 2) }}%;"
                             title="{{ $code95Summary['remaining'] }} uur openstaand">
                        </div>
                    @endif
                </div>

                <div class="code95-legend">
                    @foreach($code95Certificate->code95Courses->sortBy('course_date') as $course)
                        <span><i class="code95-legend-dot {{ $course->type === 'practice' ? 'is-practice' : 'is-theory' }}"></i>{{ $course->code }}</span>
                    @endforeach
                    <span><i class="code95-legend-dot is-open"></i>Nog te plannen</span>
                </div>

                @if(!is_null($code95Summary['daysRemaining']))
                    <div class="code95-open-note">
                        {{ $code95Summary['remaining'] > 0
                            ? 'Nog ' . $code95Summary['remaining'] . ' uur te volgen vóór ' . ($code95Summary['cycleEnd'] ? $code95Summary['cycleEnd']->format('d-m-Y') : '-') . '.'
                            : 'Alle 35 nascholingsuren zijn behaald.' }}
                    </div>
                @endif

                <div class="code95-foot">
                    @if(!is_null($code95Summary['daysRemaining']) && $code95Summary['daysRemaining'] < 0)
                        <span>Cyclus verlopen sinds {{ $code95Summary['cycleEnd'] ? $code95Summary['cycleEnd']->format('d-m-Y') : '-' }}</span>
                    @elseif(!is_null($code95Summary['daysRemaining']))
                        <span>Nog {{ $code95Summary['daysRemaining'] }} dagen geldig tot {{ $code95Summary['cycleEnd'] ? $code95Summary['cycleEnd']->format('d-m-Y') : '-' }}</span>
                    @else
                        <span>Cyclusperiode niet volledig ingevuld</span>
                    @endif
                    <a href="{{ route('admin.certificate.show', $code95Certificate->id) }}">Bekijk certificaat <i class="fa fa-arrow-right"></i></a>
                </div>

                <div class="code95-course-history">
                    <div class="code95-course-history-head">
                        <h6 class="mb-0">Cursusgeschiedenis</h6>
                        @can('certificate_access')
                            <button type="button" class="btn btn-primary btn-sm code95-add-toggle">
                                <i class="fa fa-plus"></i> Cursus toevoegen
                            </button>
                        @endcan
                    </div>

                    @if($code95Certificate->code95Courses->isNotEmpty())
                        <div class="code95-course-table">
                            <div class="code95-course-row code95-course-head">
                                <span>Gevolgd op</span>
                                <span>Cursus</span>
                                <span>Type</span>
                                <span>Uren</span>
                                <span>Opleider</span>
                                @can('certificate_access')<span></span>@endcan
                            </div>
                            @foreach($code95Certificate->code95Courses->sortBy('course_date') as $course)
                                <div class="code95-course-row">
                                    <span>{{ $course->course_date->format('d-m-Y') }}</span>
                                    <span>{{ $course->name }} <small class="text-muted">({{ $course->code }})</small></span>
                                    <span>
                                        @if($course->type === 'practice')
                                            <span class="code95-type is-practice">Praktijk</span>
                                        @else
                                            <span class="code95-type is-theory">Theorie</span>
                                        @endif
                                    </span>
                                    <span>{{ $course->hours }}</span>
                                    <span>{{ $course->trainer ?: '-' }}</span>
                                    @can('certificate_access')
                                        <span class="text-end">
                                            <form action="{{ route('admin.certificate.code95-courses.destroy', $course->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-sm text-danger p-0" onclick="return confirm('Cursus verwijderen?')" title="Cursus verwijderen"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </span>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted small py-3">Nog geen cursussen toegevoegd.</div>
                    @endif

                    @can('certificate_access')
                        <div class="code95-course-add-panel" hidden>
                            <form action="{{ route('admin.certificate.code95-courses.store', $code95Certificate->id) }}" method="POST" class="code95-course-form">
                                @csrf
                                <input type="date" name="course_date" required title="Gevolgd op">
                                <input type="text" name="name" required maxlength="255" placeholder="Cursusnaam">
                                <input type="text" name="code" required maxlength="50" placeholder="Code">
                                <select name="type" required>
                                    <option value="theory">Theorie</option>
                                    <option value="practice">Praktijk</option>
                                </select>
                                <input type="number" name="hours" required min="1" max="24" placeholder="Uren">
                                <input type="text" name="trainer" maxlength="255" placeholder="Opleider">
                                <button type="submit" class="btn btn-primary btn-sm">Toevoegen</button>
                            </form>
                        </div>
                    @endcan
                </div>

                @if(!is_null($code95Summary['daysRemaining']))
                    <div class="code95-open-note">
                        {{ $code95Summary['remaining'] > 0
                            ? 'Nog ' . $code95Summary['remaining'] . ' uur te volgen vóór ' . ($code95Summary['cycleEnd'] ? $code95Summary['cycleEnd']->format('d-m-Y') : '-') . '.'
                            : 'Alle 35 nascholingsuren zijn behaald.' }}
                    </div>
                @endif
            </div>
        @endif

        <div class="row">
            <div class="col-lg-5 mb-3">
                <div class="card h-100">
                    <div class="card-header"><strong>NAW-gegevens</strong></div>
                    <div class="card-body naw">
                        @php
                            $phone = $contact?->phone ?? $company?->phone;
                            $addressLine = trim(($company?->zipcode ?? '') . ' ' . ($company?->city ?? ''));
                        @endphp

                        <div class="naw-group">
                            <div class="naw-group-title">Persoonlijk</div>
                            <div class="naw-grid">
                                <div class="naw-item">
                                    <i class="fa fa-user naw-icon"></i>
                                    <div>
                                        <div class="naw-label">Naam</div>
                                        <div class="naw-value {{ $fullName === '' ? 'is-empty' : '' }}">{{ $fullName !== '' ? $fullName : '-' }}</div>
                                    </div>
                                </div>
                                <div class="naw-item">
                                    <i class="fa fa-birthday-cake naw-icon"></i>
                                    <div>
                                        <div class="naw-label">Geboortedatum</div>
                                        <div class="naw-value {{ $contact?->birthdate ? '' : 'is-empty' }}">{{ $contact?->birthdate ? \Carbon\Carbon::parse($contact->birthdate)->format('d-m-Y') : '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="naw-group">
                            <div class="naw-group-title">Contact</div>
                            <div class="naw-grid">
                                <div class="naw-item">
                                    <i class="fa fa-phone naw-icon"></i>
                                    <div>
                                        <div class="naw-label">Telefoon</div>
                                        <div class="naw-value {{ $phone ? '' : 'is-empty' }}">
                                            @if($phone)
                                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="naw-item">
                                    <i class="fa fa-envelope naw-icon"></i>
                                    <div>
                                        <div class="naw-label">E-mail</div>
                                        <div class="naw-value {{ $contact?->email ? '' : 'is-empty' }}">
                                            @if($contact?->email)
                                                <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="naw-item">
                                    <i class="fa fa-map-marker naw-icon"></i>
                                    <div>
                                        <div class="naw-label">Adres</div>
                                        @if(!empty($company?->street) || $addressLine !== '')
                                            <div class="naw-value">{{ $company?->street }}</div>
                                            <div class="naw-value">{{ $addressLine }}</div>
                                        @else
                                            <div class="naw-value is-empty">-</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="naw-group mb-0">
                            <div class="naw-group-title">Werk</div>
                            <div class="naw-grid">
                                <div class="naw-item">
                                    <i class="fa fa-building naw-icon"></i>
                                    <div>
                                        <div class="naw-label">Werkgever</div>
                                        <div class="naw-value {{ $company ? '' : 'is-empty' }}">
                                            @if($company)
                                                @can('company_show')
                                                    <a href="{{ route('admin.companies.show', $company->id) }}">{{ $company->name }}</a>
                                                @else
                                                    {{ $company->name }}
                                                @endcan
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="naw-item">
                                    <i class="fa fa-briefcase naw-icon"></i>
                                    <div>
                                        <div class="naw-label">Functie</div>
                                        <div class="naw-value {{ $driver->function ? '' : 'is-empty' }}">{{ $driver->function ?: '-' }}</div>
                                    </div>
                                </div>
                                <div class="naw-item">
                                    <i class="fa fa-calendar naw-icon"></i>
                                    <div>
                                        <div class="naw-label">In dienst sinds</div>
                                        <div class="naw-value {{ $driver->created_at ? '' : 'is-empty' }}">{{ $driver->created_at ? $driver->created_at->format('d-m-Y') : '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer small text-muted">
                        BSN, pasfoto, nationaliteit en medische gegevens worden op deze kaart niet vastgelegd.
                    </div>
                </div>
            </div>

            <div class="col-lg-7 mb-3">
                <div class="card h-100">
                    <div class="card-header"><strong>Documenten</strong></div>
                    <div class="card-body">
                        @if($documents->isEmpty())
                            <div class="text-muted">Geen documenten gevonden.</div>
                        @else
                            @foreach($documents as $certificate)
                                @php
                                    $expiryRaw = $certificate->getRawOriginal('expiry_date');
                                    $daysUntilExpiry = !empty($expiryRaw) ? \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($expiryRaw), false) : null;
                                    $statusLabel = 'Geldig';
                                    $statusClass = 'is-ok';
                                    $daysText = !is_null($daysUntilExpiry) ? 'Nog ' . $daysUntilExpiry . ' dagen geldig' : null;
                                    if (!is_null($daysUntilExpiry) && $daysUntilExpiry < 0) {
                                        $statusLabel = 'Verlopen';
                                        $statusClass = 'is-expired';
                                        $daysText = abs($daysUntilExpiry) . ' dagen verlopen';
                                    } elseif (!is_null($daysUntilExpiry) && $daysUntilExpiry <= 90) {
                                        $statusLabel = 'Verloopt binnenkort';
                                        $statusClass = 'is-warning';
                                        $daysText = 'Nog ' . $daysUntilExpiry . ' dagen geldig';
                                    }
                                    $cardNumber = $certificate->getMetaValue('card_number');
                                @endphp
                                <div class="document-item {{ $statusClass }}">
                                    <div class="document-head">
                                        <h5 class="document-title">
                                            @can('certificate_access')
                                                <a class="document-link" href="{{ route('admin.certificate.show', $certificate->id) }}">{{ $certificate->category?->name ?: $certificate->name }}</a>
                                                <a class="document-edit" href="{{ route('admin.certificate.edit', $certificate->id) }}" title="{{ trans('global.edit') }}" aria-label="{{ trans('global.edit') }}"><i class="fa fa-pencil"></i></a>
                                            @else
                                                {{ $certificate->category?->name ?: $certificate->name }}
                                            @endcan
                                        </h5>
                                        <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </div>
                                    <div class="document-facts">
                                        <div class="document-fact">
                                            <div class="document-fact-label">Datum afgifte</div>
                                            @can('certificate_access')
                                                <button type="button" class="document-fact-value inline-date {{ $certificate->issue_date ? '' : 'is-empty' }}"
                                                    data-url="{{ route('admin.certificate.update-dates', $certificate->id) }}"
                                                    data-field="issue_date"
                                                    data-value="{{ $certificate->getRawOriginal('issue_date') }}"
                                                    title="Klik om de datum te wijzigen">{{ $certificate->issue_date ?: 'Invullen' }}</button>
                                            @else
                                                <div class="document-fact-value">{{ $certificate->issue_date ?: '-' }}</div>
                                            @endcan
                                        </div>
                                        <div class="document-fact">
                                            <div class="document-fact-label">Geldig tot</div>
                                            @can('certificate_access')
                                                <button type="button" class="document-fact-value inline-date"
                                                    data-url="{{ route('admin.certificate.update-dates', $certificate->id) }}"
                                                    data-field="expiry_date"
                                                    data-value="{{ $certificate->getRawOriginal('expiry_date') }}"
                                                    title="Klik om de datum te wijzigen">{{ $certificate->expiry_date ?: '-' }}</button>
                                            @else
                                                <div class="document-fact-value">{{ $certificate->expiry_date ?: '-' }}</div>
                                            @endcan
                                            @if($daysText)
                                                <div class="document-fact-note {{ $statusClass }}">{{ $daysText }}</div>
                                            @endif
                                        </div>
                                        @if(!empty($cardNumber))
                                            <div class="document-fact">
                                                <div class="document-fact-label">Kaartnummer</div>
                                                <div class="document-fact-value">{{ $cardNumber }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
@parent
<style>
    .driver-dossier .driver-avatar {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #1f4ca0;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .driver-dossier .overview-strip {
        border-top: 4px solid #45b649;
        border-radius: 8px;
        background: #f9fbff;
        padding: 12px;
    }

    .driver-dossier .overview-row {
        display: grid;
        grid-template-columns: 200px 1fr 120px;
        gap: 12px;
        align-items: center;
        margin-bottom: 8px;
    }

    .driver-dossier .overview-grid-head {
        display: grid;
        grid-template-columns: 200px 1fr 120px;
        gap: 12px;
        align-items: center;
        margin-bottom: 6px;
    }

    .driver-dossier .overview-rows {
        position: relative;
        padding-bottom: 30px;
    }

    .driver-dossier .overview-today {
        position: absolute;
        left: 212px;
        right: 132px;
        top: -8px;
        bottom: 22px;
        pointer-events: none;
        z-index: 3;
    }

    .driver-dossier .overview-today-line {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 3px;
        margin-left: -1.5px;
        background: #1a1f44;
    }

    .driver-dossier .overview-today-label {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-top: 6px;
        background: #1a1f44;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1;
        padding: 4px 8px;
        border-radius: 4px;
        white-space: nowrap;
    }

    .driver-dossier .overview-year-scale {
        position: relative;
        height: 22px;
        font-size: 0.85rem;
        color: #41587a;
    }

    .driver-dossier .overview-year-scale span {
        position: absolute;
        top: 0;
        transform: translateX(-50%);
    }

    .driver-dossier .overview-year-scale span.is-first {
        transform: none;
    }

    .driver-dossier .overview-name {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .driver-dossier .overview-date {
        text-align: right;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .driver-dossier .overview-bar-wrap {
        height: 26px;
        border-radius: 4px;
        background-color: #eff0f7;
        background-image: repeating-linear-gradient(to right, transparent 0, transparent calc(20% - 1px), #d5d9e6 calc(20% - 1px), #d5d9e6 20%);
        position: relative;
        z-index: 2;
    }

    .driver-dossier .overview-bar {
        position: absolute;
        top: 6px;
        left: 0;
        height: 14px;
        border-radius: 999px;
    }

    .driver-dossier .overview-bar.is-ok {
        background: #74c05a;
    }

    .driver-dossier .overview-bar.is-warning {
        background: #e0a43a;
    }

    .driver-dossier .overview-bar.is-expired {
        background: #dc3545;
    }

    .driver-dossier .document-item {
        position: relative;
        border: 1px solid #e4e8ef;
        border-left: 5px solid #74c05a;
        border-radius: 8px;
        padding: 16px 18px;
        margin-bottom: 14px;
        background: #fff;
        transition: box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .driver-dossier .document-item:hover {
        box-shadow: 0 4px 14px rgba(26, 31, 68, 0.1);
    }

    .driver-dossier .document-link {
        color: inherit;
        text-decoration: none;
    }

    .driver-dossier .document-link::after {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    .driver-dossier .document-link:hover {
        color: #1f4ca0;
    }

    .driver-dossier .document-edit {
        position: relative;
        z-index: 2;
        margin-left: 8px;
        font-size: 0.85rem;
        color: #7a869a;
    }

    .driver-dossier .document-edit:hover {
        color: #1f4ca0;
    }

    .driver-dossier .inline-date {
        position: relative;
        z-index: 2;
        display: inline-block;
        background: transparent;
        border: 0;
        border-bottom: 1px dashed transparent;
        padding: 0;
        cursor: pointer;
        text-align: left;
    }

    .driver-dossier .inline-date:hover {
        border-bottom-color: #7a869a;
    }

    .driver-dossier .inline-date.is-empty {
        color: #1f4ca0;
        font-weight: 500;
    }

    .driver-dossier .inline-date-input {
        position: relative;
        z-index: 2;
        font-size: 0.95rem;
        padding: 2px 6px;
        border: 1px solid #1f4ca0;
        border-radius: 4px;
    }

    .driver-dossier .document-item.is-warning {
        border-left-color: #e0a43a;
    }

    .driver-dossier .document-item.is-expired {
        border-left-color: #dc3545;
    }

    .driver-dossier .document-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .driver-dossier .document-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1f44;
    }

    .driver-dossier .document-facts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
    }

    .driver-dossier .document-fact-label {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #7a869a;
        margin-bottom: 2px;
    }

    .driver-dossier .document-fact-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1f44;
    }

    .driver-dossier .document-fact-note {
        font-size: 0.8rem;
        margin-top: 2px;
    }

    .driver-dossier .document-fact-note.is-ok {
        color: #2f7a1f;
    }

    .driver-dossier .document-fact-note.is-warning {
        color: #9a6a0a;
    }

    .driver-dossier .document-fact-note.is-expired {
        color: #b02a37;
    }

    .driver-dossier .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        line-height: 1.4;
        white-space: nowrap;
    }

    .driver-dossier .status-pill::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .driver-dossier .status-pill.is-ok {
        background: #e3f4dc;
        color: #2f7a1f;
    }

    .driver-dossier .status-pill.is-warning {
        background: #fbefd5;
        color: #9a6a0a;
    }

    .driver-dossier .status-pill.is-expired {
        background: #fbe0e3;
        color: #b02a37;
    }

    .driver-dossier .naw {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 32px;
        row-gap: 14px;
        align-items: center;
    }

    .driver-dossier .naw-group,
    .driver-dossier .naw-grid {
        display: contents;
    }

    .driver-dossier .naw-group-title {
        grid-column: 1 / -1;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #7a869a;
        padding-bottom: 8px;
        margin-top: 8px;
        border-bottom: 1px solid #eef0f5;
    }

    .driver-dossier .naw > .naw-group:first-of-type .naw-group-title {
        margin-top: 0;
    }

    .driver-dossier .naw-item {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .driver-dossier .naw-icon {
        flex: 0 0 32px;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #eaf0fb;
        color: #1f4ca0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }

    .driver-dossier .naw-label {
        font-size: 0.72rem;
        color: #7a869a;
        line-height: 1.2;
        margin-bottom: 2px;
    }

    .driver-dossier .naw-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1a1f44;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .driver-dossier .naw-value.is-empty {
        color: #aab2c0;
        font-weight: 400;
    }

    .driver-dossier .naw-value a {
        color: #1f4ca0;
    }

    .driver-dossier .code95-card {
        border: 1px solid #dfe5ef;
        border-top: 4px solid #1f4ca0;
        border-radius: 8px;
        background: #fff;
        padding: 18px;
    }

    .driver-dossier .code95-head,
    .driver-dossier .code95-foot,
    .driver-dossier .code95-progress-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .driver-dossier .code95-kicker {
        color: #1f4ca0;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .driver-dossier .code95-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .driver-dossier .code95-status {
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 0.78rem;
        font-weight: 700;
        background: #fff3cd;
        color: #806000;
    }

    .driver-dossier .code95-status.is-ok { background: #dff3d8; color: #36742e; }
    .driver-dossier .code95-status.is-expired { background: #f8d7da; color: #842029; }

    .driver-dossier .code95-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin: 16px 0;
    }

    .driver-dossier .code95-summary-item {
        background: #f7f9fc;
        border-radius: 7px;
        padding: 11px 12px;
    }

    .driver-dossier .code95-summary-label {
        color: #687793;
        font-size: 0.78rem;
    }

    .driver-dossier .code95-summary-value {
        margin-top: 3px;
        font-weight: 700;
        color: #1a1f44;
    }

    .driver-dossier .code95-progress-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
    }

    .driver-dossier .code95-progress-head {
        font-size: 0.85rem;
        margin-bottom: 7px;
    }

    .driver-dossier .code95-progress {
        height: 12px;
        overflow: hidden;
        border-radius: 999px;
        background: #edf0f5;
    }

    .driver-dossier .code95-progress-fill {
        height: 100%;
        border-radius: 999px;
    }

    .driver-dossier .code95-progress-fill.is-theory { background: #1f4ca0; }
    .driver-dossier .code95-progress-fill.is-practice { background: #74c05a; }

    .driver-dossier .code95-foot {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid #edf0f5;
        color: #687793;
        font-size: 0.82rem;
    }

    .driver-dossier .code95-foot a {
        color: #1f4ca0;
        font-weight: 700;
        text-decoration: none;
    }

    .driver-dossier .code95-course-details {
        margin-top: 14px;
        border-top: 1px solid #edf0f5;
        padding-top: 10px;
    }

    .driver-dossier .code95-course-details summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-weight: 700;
        color: #1f4ca0;
    }

    .driver-dossier .code95-course-table {
        margin-top: 10px;
    }

    .driver-dossier .code95-course-row {
        display: grid;
        grid-template-columns: 110px 1.5fr 90px 55px 1fr 32px;
        gap: 8px;
        align-items: center;
        min-height: 42px;
        border-bottom: 1px solid #edf0f5;
        font-size: 0.85rem;
    }

    .driver-dossier .code95-course-head {
        min-height: 34px;
        color: #687793;
        font-size: 0.76rem;
    }

    .driver-dossier .code95-type {
        display: inline-block;
        border-radius: 999px;
        padding: 3px 8px;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .driver-dossier .code95-type.is-theory { background: #dfe8fb; color: #1f4ca0; }
    .driver-dossier .code95-type.is-practice { background: #e0f2da; color: #36742e; }

    .driver-dossier .code95-course-form {
        display: grid;
        grid-template-columns: 130px 1.4fr 90px 110px 70px 1fr auto;
        gap: 8px;
        margin-top: 12px;
        align-items: center;
    }

    @media (max-width: 768px) {
        .driver-dossier .code95-summary-grid,
        .driver-dossier .code95-progress-grid {
            grid-template-columns: 1fr;
        }

        .driver-dossier .code95-head,
        .driver-dossier .code95-foot {
            align-items: flex-start;
            flex-direction: column;
        }

        .driver-dossier .code95-course-row {
            grid-template-columns: 92px 1.4fr 78px 42px 1fr 28px;
            gap: 5px;
            font-size: 0.78rem;
        }

        .driver-dossier .code95-course-form {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 992px) {
        .driver-dossier .overview-grid-head {
            display: none;
        }

        .driver-dossier .overview-today {
            display: none;
        }

        .driver-dossier .overview-rows {
            padding-bottom: 0;
        }

        .driver-dossier .overview-row {
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .driver-dossier .overview-date {
            text-align: left;
        }
    }

    .driver-dossier .code95-course-summary {
        min-height: 48px;
        width: 100%;
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        padding: 8px 10px;
        border: 1px solid #cfd8e6;
        border-radius: 7px;
        background: #f7f9fc;
        cursor: pointer;
        list-style: none;
    }

    .driver-dossier .code95-course-summary::-webkit-details-marker { display: none; }

    .driver-dossier .code95-course-summary-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #1f4ca0;
        font-size: 0.92rem;
        font-weight: 800;
    }

    .driver-dossier .code95-course-summary-actions {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .driver-dossier .code95-course-count {
        color: #687793;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .driver-dossier .code95-course-toggle {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: #1f4ca0;
        color: #fff;
    }

    .driver-dossier .code95-course-details[open] .code95-course-toggle i {
        transform: rotate(45deg);
    }

    .driver-dossier .code95-course-toggle i {
        transition: transform 0.15s ease;
    }

    .driver-dossier .code95-segmented-progress {
        display: flex;
        height: 36px;
        overflow: hidden;
        border: 1px solid #dfe5ef;
        border-radius: 6px;
        background: #f3f5f9;
        margin-top: 4px;
    }

    .driver-dossier .code95-progress-segment {
        height: 22px;
        margin: 7px 3px 7px 0;
        border-radius: 4px;
    }

    .driver-dossier .code95-progress-segment.is-theory { background: #1f4ca0; }
    .driver-dossier .code95-progress-segment.is-practice { background: #74c05a; }

    .driver-dossier .code95-progress-segment.is-open {
        background: repeating-linear-gradient(135deg, #fff, #fff 5px, #edf0f5 5px, #edf0f5 9px);
        border: 1px dashed #bac3d2;
    }

    .driver-dossier .code95-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 10px;
        color: #687793;
        font-size: 0.78rem;
    }

    .driver-dossier .code95-legend span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .driver-dossier .code95-legend-dot {
        width: 10px;
        height: 10px;
        display: inline-block;
        border-radius: 2px;
    }

    .driver-dossier .code95-legend-dot.is-theory { background: #1f4ca0; }
    .driver-dossier .code95-legend-dot.is-practice { background: #74c05a; }
    .driver-dossier .code95-legend-dot.is-open {
        border: 1px dashed #bac3d2;
        background: #fff;
    }

    .driver-dossier .code95-open-note {
        margin-top: 8px;
        color: #687793;
        font-size: 0.82rem;
    }

    .driver-dossier .code95-course-history {
        margin-top: 14px;
        border-top: 1px solid #edf0f5;
        padding-top: 12px;
    }

    .driver-dossier .code95-course-history-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .driver-dossier .code95-course-history-head h6 {
        color: #687793;
        font-weight: 700;
    }

    .driver-dossier .code95-course-add-panel {
        margin-top: 10px;
        padding: 12px;
        border: 1px solid #dfe5ef;
        border-radius: 6px;
        background: #f8fafc;
    }
</style>
@endsection

@section('scripts')
@parent
<script>
    (function () {
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function notify(message) {
            if (typeof sendFlashMessage === 'function') {
                sendFlashMessage(message, 'alert-danger');
            } else {
                alert(message);
            }
        }

        function startEdit(button) {
            var input = document.createElement('input');
            input.type = 'date';
            input.className = 'inline-date-input';
            input.value = button.dataset.value || '';

            var saving = false;

            function cancel() {
                if (saving || !input.parentNode) return;
                input.parentNode.replaceChild(button, input);
            }

            function save() {
                if (!input.value || input.value === button.dataset.value) {
                    cancel();
                    return;
                }

                saving = true;
                input.disabled = true;

                var payload = {};
                payload[button.dataset.field] = input.value;

                fetch(button.dataset.url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                }).then(function (response) {
                    return response.json().then(function (body) {
                        return { ok: response.ok, body: body };
                    });
                }).then(function (result) {
                    if (!result.ok) {
                        var errors = result.body.errors ? Object.values(result.body.errors)[0][0] : null;
                        throw new Error(errors || result.body.message || 'Opslaan mislukt');
                    }
                    // Status, bars and timeline depend on the dates, so refresh the page.
                    window.location.reload();
                }).catch(function (error) {
                    saving = false;
                    notify(error.message || 'Opslaan mislukt');
                    cancel();
                });
            }

            input.addEventListener('change', save);
            input.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    cancel();
                }
            });
            input.addEventListener('blur', function () {
                setTimeout(function () {
                    if (!saving) cancel();
                }, 200);
            });

            button.parentNode.replaceChild(input, button);
            input.focus();
            if (typeof input.showPicker === 'function') {
                try { input.showPicker(); } catch (e) { /* needs user gesture in some browsers */ }
            }
        }

        document.addEventListener('click', function (event) {
            var button = event.target.closest('.inline-date');
            if (button) {
                event.preventDefault();
                startEdit(button);
            }
        });

        document.querySelectorAll('.code95-add-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                const card = button.closest('.code95-course-history');
                const panel = card ? card.querySelector('.code95-course-add-panel') : null;
                if (!panel) return;

                panel.hidden = !panel.hidden;
                button.innerHTML = panel.hidden
                    ? '<i class="fa fa-plus"></i> Cursus toevoegen'
                    : '<i class="fa fa-minus"></i> Sluiten';
            });
        });
    })();
</script>
@endsection