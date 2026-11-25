@extends('layouts.admin')
@section('content')

@php
    $contact = $driver->contact;
    $fullName = trim(($contact?->first_name ?? '') . ' ' . ($contact?->last_name ?? ''));
    $stateClasses = ['incomplete' => 'badge-warning', 'unchecked' => 'badge-info', 'checked' => 'badge-success'];
    $licenseCategories = optional($rows->first(fn ($row) => $row['category']->code === 'rijbewijs'))['certificate']?->getMetaValue('license_categories');
    $onlyB = $licenseCategories && !preg_match('/[CD]/i', $licenseCategories);
    $code95Row = $rows->first(fn ($row) => $row['category']->isCode95());
    $backTo = $backUrl;
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong>Chauffeurskaart: {{ $fullName !== '' ? $fullName : 'Chauffeur' }}</strong>
            @if($driver->company)
                <span class="text-muted">· {{ $driver->company->name }}</span>
            @endif
            <span class="badge {{ $stateClasses[$state] ?? 'badge-light' }} ml-2">{{ $stateLabels[$state] ?? $state }}</span>
            @unless($driver->in_service)
                <span class="badge badge-secondary ml-1">uit dienst</span>
            @endunless
        </div>
        <div>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.academy.driver-cards.form', $driver->id) }}" title="Formulier 'Check je papieren' met de bekende gegevens; de ontbrekende gegevens blijven open voor de chauffeur">Formulier (PDF)</a>
            @if($backTo && \Illuminate\Support\Str::startsWith($backTo, url('/')))
                <a class="btn btn-default btn-sm" href="{{ $backTo }}">{{ trans('global.back') }}</a>
            @elseif($driver->company && $driver->company->academy_enabled)
                <a class="btn btn-default btn-sm" href="{{ route('admin.academy.employers.show', $driver->company_id) }}">{{ trans('global.back') }}</a>
            @endif
        </div>
    </div>

    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!empty($missing))
            <div class="alert alert-warning">
                <strong>Onvolledig.</strong> Nog nodig: {{ implode(', ', $missing) }}.
            </div>
        @endif

        @if(!empty($toFillIn))
            <div class="alert alert-light border">
                Nog invullen of n.v.t. aanvinken: {{ implode(', ', $toFillIn) }}.
            </div>
        @endif

        @if($onlyB)
            <div class="alert alert-info">
                Alleen rijbewijs B: Code 95 en chauffeurspas staan op rood. Zet een opmerking op de kaart en meld het aan de eigenaar van de planner.
            </div>
        @endif

        @if($plannedTrainings->isNotEmpty())
            <div class="alert alert-light border">
                <strong>Ingeplande trainingen</strong>
                <ul class="mb-0">
                    @foreach($plannedTrainings as $participant)
                        <li>
                            <a href="{{ route('admin.academy.trainings.show', $participant->training_id) }}">{{ $participant->training->name }}</a>
                            op {{ $participant->training->training_date->format('d-m-Y') }}
                            <span class="badge badge-info">ingepland</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-danger py-2">
            <strong>STOP</strong> Een datum die je niet goed kunt lezen? Niet invullen, maar opnieuw opvragen. Een verkeerde vervaldatum is de grootste foutbron.
            Zet nooit BSN, pasfoto, nationaliteit of medische gegevens in het portaal, ook niet in opmerkingen.
        </div>

        @can('academy_manage')
        <form method="POST" action="{{ route('admin.academy.driver-cards.update', $driver->id) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="back_to" value="{{ $backTo }}">

            <h5 class="mt-2">Persoon</h5>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label class="required" for="first_name">Voornaam</label>
                    <input class="form-control" type="text" name="first_name" id="first_name" value="{{ old('first_name', $contact?->first_name) }}" required>
                </div>
                <div class="form-group col-md-3">
                    <label class="required" for="last_name">Achternaam</label>
                    <input class="form-control" type="text" name="last_name" id="last_name" value="{{ old('last_name', $contact?->last_name) }}" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="function">Functie</label>
                    <input class="form-control" type="text" name="function" id="function" value="{{ old('function', $driver->function) }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="birthdate">Geboortedatum</label>
                    <input class="form-control" type="date" name="birthdate" id="birthdate" value="{{ old('birthdate', $contact?->birthdate) }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="email">E-mailadres</label>
                    <input class="form-control" type="email" name="email" id="email" value="{{ old('email', $contact?->email) }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="phone">Telefoon</label>
                    <input class="form-control" type="text" name="phone" id="phone" value="{{ old('phone', $contact?->phone) }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Dienstverband</label>
                    <div class="form-check">
                        <input type="hidden" name="in_service" value="0">
                        <input class="form-check-input" type="checkbox" name="in_service" id="in_service" value="1" {{ old('in_service', $driver->in_service) ? 'checked' : '' }}>
                        <label class="form-check-label" for="in_service">In dienst (uit dienst: de chauffeur telt niet meer mee)</label>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="street">Adres</label>
                    <input class="form-control" type="text" name="street" id="street" value="{{ old('street', $driver->street) }}">
                </div>
                <div class="form-group col-md-2">
                    <label for="zipcode">Postcode</label>
                    <input class="form-control" type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $driver->zipcode) }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="city">Plaats</label>
                    <input class="form-control" type="text" name="city" id="city" value="{{ old('city', $driver->city) }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="cbr_customer_number">CBR-klantnummer</label>
                    <input class="form-control" type="text" name="cbr_customer_number" id="cbr_customer_number" value="{{ old('cbr_customer_number', $driver->cbr_customer_number) }}">
                </div>
            </div>

            <h5 class="mt-3">Rijbewijs en chauffeurspas</h5>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="driver_license_id">Rijbewijsnummer</label>
                    <input class="form-control" type="text" name="driver_license_id" id="driver_license_id" value="{{ old('driver_license_id', $contact?->driver_license_id) }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="license_categories">Rijbewijscategorieën</label>
                    <input class="form-control" type="text" name="license_categories" id="license_categories" placeholder="bijv. C, CE" value="{{ old('license_categories', $licenseCategories) }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="driver_pass_number">Pasnummer chauffeurspas</label>
                    <input class="form-control" type="text" name="driver_pass_number" id="driver_pass_number" value="{{ old('driver_pass_number', $driver->driver_pass_number) }}">
                </div>
            </div>
            <p class="text-muted small">Neem de datums over van het rijbewijs (kolom 11 en 12), de chauffeurspas (veld 4b) en Mijn CBR (Code 95-uren).</p>

            <h5 class="mt-3">Documenten</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Document</th>
                            <th style="width: 220px;">Geldig tot / behaald op</th>
                            <th style="width: 90px;">n.v.t.</th>
                            <th style="width: 160px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            @php
                                $category = $row['category'];
                                $certificate = $row['certificate'];
                                $field = $category->one_off ? 'issue_date' : 'expiry_date';
                                $current = old("certificates.{$category->id}.{$field}", $certificate?->getRawOriginal($field));
                                $isNa = old("certificates.{$category->id}.not_applicable", $certificate?->not_applicable);
                                $label = match ($category->code) {
                                    'rijbewijs' => 'Rijbewijs (kolom 11)',
                                    'code_95' => 'Code 95 (kolom 12)',
                                    'chauffeurspas' => 'Chauffeurspas (veld 4b)',
                                    default => $category->name,
                                };
                            @endphp
                            <tr>
                                <td>
                                    {{ $label }}
                                    @if($category->one_off)
                                        <span class="text-muted small">(eenmalig: datum van behalen)</span>
                                    @endif
                                </td>
                                <td>
                                    <input class="form-control form-control-sm js-card-date" type="date" name="certificates[{{ $category->id }}][{{ $field }}]" value="{{ $current }}" {{ $isNa ? 'disabled' : '' }}>
                                </td>
                                <td class="text-center">
                                    @if($category->allow_not_applicable)
                                        <input type="hidden" name="certificates[{{ $category->id }}][not_applicable]" value="0">
                                        <input class="js-card-na" type="checkbox" name="certificates[{{ $category->id }}][not_applicable]" value="1" {{ $isNa ? 'checked' : '' }}>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>@include('admin.academy.partials.status-badge', ['status' => $row['status']])</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @php $progress = $code95Row['code95'] ?? null; @endphp
            <h5 class="mt-3">Code 95-uren</h5>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="code95_hours">Behaalde uren volgens Mijn CBR</label>
                    <input class="form-control" type="number" min="0" name="code95_hours" id="code95_hours" value="{{ old('code95_hours', $progress['openingHours'] ?? '') }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="code95_practice_hours">Waarvan praktijkuren</label>
                    <input class="form-control" type="number" min="0" name="code95_practice_hours" id="code95_practice_hours" value="{{ old('code95_practice_hours', $progress['openingPractice'] ?? '') }}">
                </div>
                @if($progress)
                    <div class="form-group col-md-6">
                        <label>Stand huidige cyclus</label>
                        <div class="form-control-plaintext">
                            {{ $progress['completed'] }} van {{ $progress['required'] }} uur
                            (praktijk {{ $progress['practiceCompleted'] }} van {{ $progress['practiceRequired'] }}),
                            nog nodig: {{ $progress['remaining'] }} uur
                            @if($progress['practiceRemaining'] > 0), waarvan {{ $progress['practiceRemaining'] }} uur praktijk @endif
                        </div>
                    </div>
                @endif
            </div>
            <p class="text-muted small">Het startsaldo is wat Mijn CBR nu toont. Bevestigde trainingen hieronder worden erbij opgeteld.</p>

            <h5 class="mt-3">Scans</h5>
            <div class="form-check mb-3">
                <input type="hidden" name="scans_attached" value="0">
                <input class="form-check-input" type="checkbox" name="scans_attached" id="scans_attached" value="1" {{ old('scans_attached', $driver->scans_attached) ? 'checked' : '' }}>
                <label class="form-check-label" for="scans_attached">Scans als bijlage; BSN en pasfoto weggelakt</label>
            </div>

            <button class="btn btn-primary" type="submit">{{ trans('global.save') }}</button>
            <span class="text-muted small ml-2">Wijzig je later een datum, dan vervalt de vierogencontrole.</span>
        </form>
        @endcan

        <hr>

        <h5>Gevolgde trainingen (Code 95)</h5>
        @if($courses->isEmpty())
            <p class="text-muted">Nog geen trainingen vastgelegd.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Training</th>
                            <th>Type</th>
                            <th>Uren</th>
                            <th>Geldig tot</th>
                            <th>In Mijn CBR</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                            <tr>
                                <td>{{ $course->course_date?->format('d-m-Y') }}</td>
                                <td>{{ $course->name }} @if($course->code)<span class="text-muted">({{ $course->code }})</span>@endif</td>
                                <td>{{ $course->type === 'practice' ? 'Praktijk' : 'Theorie' }}</td>
                                <td>{{ $course->hours }}</td>
                                <td>{{ $course->valid_until?->format('d-m-Y') ?? '-' }}</td>
                                <td>
                                    @if($course->cbr_registered)
                                        <span class="badge badge-success">ja</span>
                                    @else
                                        <span class="badge badge-warning">nog niet: telt niet mee</span>
                                    @endif
                                </td>
                                <td>
                                    @can('academy_manage')
                                        <form method="POST" action="{{ route('admin.academy.driver-cards.courses.destroy', [$driver->id, $course->id]) }}" onsubmit="return confirm('Training verwijderen?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="back_to" value="{{ $backTo }}">
                                            <button class="btn btn-xs btn-outline-danger" type="submit">Verwijderen</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @can('academy_manage')
        <form method="POST" action="{{ route('admin.academy.driver-cards.courses.store', $driver->id) }}" class="border rounded p-3 mb-4">
            @csrf
            <input type="hidden" name="back_to" value="{{ $backTo }}">
            <strong>+ Training toevoegen</strong>
            <div class="form-row mt-2">
                <div class="form-group col-md-2">
                    <label for="course_date">Datum</label>
                    <input class="form-control form-control-sm" type="date" name="course_date" id="course_date" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="course_name">Naam</label>
                    <input class="form-control form-control-sm" type="text" name="name" id="course_name" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="course_code">CBR-code</label>
                    <input class="form-control form-control-sm" type="text" name="code" id="course_code">
                </div>
                <div class="form-group col-md-2">
                    <label for="course_type">Type</label>
                    <select class="form-control form-control-sm" name="type" id="course_type">
                        <option value="theory">Theorie</option>
                        <option value="practice">Praktijk</option>
                    </select>
                </div>
                <div class="form-group col-md-1">
                    <label for="course_hours">Uren</label>
                    <input class="form-control form-control-sm" type="number" min="1" max="35" name="hours" id="course_hours" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="course_valid_until">Geldig tot</label>
                    <input class="form-control form-control-sm" type="date" name="valid_until" id="course_valid_until">
                </div>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="cbr_registered" id="cbr_registered" value="1">
                <label class="form-check-label" for="cbr_registered">De uren staan in Mijn CBR (alleen dan tellen ze mee)</label>
            </div>
            <button class="btn btn-sm btn-primary" type="submit">Toevoegen</button>
        </form>
        @endcan

        <hr>

        <h5>Vierogencontrole</h5>
        @if($driver->checked_at)
            <p>
                Gecontroleerd door <strong>{{ $driver->checkedBy?->name ?? 'onbekend' }}</strong>
                op {{ $driver->checked_at->format('d-m-Y H:i') }}.
            </p>
        @else
            <p class="text-muted">
                Een collega legt de kaart naast de originele documenten en Mijn CBR en vergelijkt geboortedatum, rijbewijs (kolom 11 en 12),
                chauffeurspas (veld 4b), Code 95-uren en certificaten.
            </p>
        @endif

        @can('academy_manage')
        <form method="POST" action="{{ route('admin.academy.driver-cards.check', $driver->id) }}" class="border rounded p-3 mb-4">
            @csrf
            <input type="hidden" name="back_to" value="{{ $backTo }}">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label class="required" for="checked_by_user_id">Gecontroleerd door (tweede persoon)</label>
                    <select class="form-control" name="checked_by_user_id" id="checked_by_user_id" required>
                        <option value="">Kies een collega</option>
                        @foreach($checkers as $checker)
                            <option value="{{ $checker->id }}" {{ old('checked_by_user_id') == $checker->id ? 'selected' : '' }}>{{ $checker->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="dates_confirmed" id="dates_confirmed" value="1" required>
                <label class="form-check-label" for="dates_confirmed">Alle datums op de kaart komen overeen met de documenten</label>
            </div>
            <button class="btn btn-sm btn-success" type="submit" {{ empty($missing) ? '' : 'disabled' }}>Controle vastleggen</button>
            @if(!empty($missing))
                <span class="text-muted small ml-2">Eerst de kaart volledig maken.</span>
            @endif
        </form>
        @endcan

        <hr>

        <h5>Opmerkingen</h5>
        @forelse($comments as $comment)
            <div class="border-bottom py-2">
                <div class="small text-muted">{{ $comment->created_at->format('d-m-Y H:i') }} · {{ $comment->user?->name }}</div>
                <div>{{ $comment->body }}</div>
            </div>
        @empty
            <p class="text-muted">Nog geen opmerkingen.</p>
        @endforelse

        @can('academy_manage')
        <form method="POST" action="{{ route('admin.academy.driver-cards.comment', $driver->id) }}" class="mt-3">
            @csrf
            <input type="hidden" name="back_to" value="{{ $backTo }}">
            <div class="form-group">
                <textarea class="form-control" name="body" rows="2" maxlength="2000" placeholder="Opmerking, bijvoorbeeld wanneer en met wie je sprak" required></textarea>
            </div>
            <button class="btn btn-sm btn-default" type="submit">Opmerking toevoegen</button>
        </form>
        @endcan
    </div>
</div>

@endsection

@section('scripts')
@parent
<script>
    document.querySelectorAll('.js-card-na').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            var input = checkbox.closest('tr').querySelector('.js-card-date');
            input.disabled = checkbox.checked;
            if (checkbox.checked) { input.value = ''; }
        });
    });
</script>
@endsection
