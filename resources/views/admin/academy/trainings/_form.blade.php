@php
    $t = $training ?? null;
@endphp
<div class="form-row">
    @if(!$t)
        <div class="form-group col-md-4">
            <label class="required" for="company_id">Werkgever</label>
            <select class="form-control" name="company_id" id="company_id" required onchange="window.location = '{{ route('admin.academy.trainings.create') }}?company_id=' + this.value;">
                <option value="">Kies een werkgever</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ (string) old('company_id', $companyId) === (string) $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
    @else
        <input type="hidden" name="company_id" value="{{ $t->company_id }}">
    @endif
    <div class="form-group col-md-4">
        <label class="required" for="name">Training</label>
        <input class="form-control" type="text" name="name" id="name" value="{{ old('name', $t?->name) }}" required>
    </div>
    <div class="form-group col-md-4">
        <label for="cbr_code">CBR-code</label>
        <input class="form-control" type="text" name="cbr_code" id="cbr_code" value="{{ old('cbr_code', $t?->cbr_code) }}">
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-3">
        <label class="required" for="training_date">Datum training</label>
        <input class="form-control" type="date" name="training_date" id="training_date" value="{{ old('training_date', $t?->training_date?->format('Y-m-d')) }}" required>
    </div>
    <div class="form-group col-md-3">
        <label for="location">Locatie</label>
        <input class="form-control" type="text" name="location" id="location" value="{{ old('location', $t?->location) }}">
    </div>
    <div class="form-group col-md-2">
        <label class="required" for="provider_type">Opleider</label>
        <select class="form-control" name="provider_type" id="provider_type" required>
            @foreach(\App\Models\AcademyTraining::PROVIDER_SELECT as $value => $label)
                <option value="{{ $value }}" {{ old('provider_type', $t?->provider_type ?? 'asp') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-2">
        <label for="provider_name">Naam opleider</label>
        <input class="form-control" type="text" name="provider_name" id="provider_name" value="{{ old('provider_name', $t?->provider_name) }}">
    </div>
    <div class="form-group col-md-2">
        <label for="provider_email">E-mail opleider</label>
        <input class="form-control" type="email" name="provider_email" id="provider_email" value="{{ old('provider_email', $t?->provider_email) }}">
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-3">
        <label for="code95_hours">Code 95-uren (leeg = geen Code 95-module)</label>
        <input class="form-control" type="number" min="1" max="35" name="code95_hours" id="code95_hours" value="{{ old('code95_hours', $t?->code95_hours) }}">
    </div>
    <div class="form-group col-md-3">
        <label for="code95_type">Soort uren</label>
        <select class="form-control" name="code95_type" id="code95_type">
            <option value="">-</option>
            @foreach(\App\Models\AcademyTraining::CODE95_TYPE_SELECT as $value => $label)
                <option value="{{ $value }}" {{ old('code95_type', $t?->code95_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-6">
        <label>&nbsp;</label>
        <div class="form-check">
            <input type="hidden" name="is_prevention" value="0">
            <input class="form-check-input" type="checkbox" name="is_prevention" id="is_prevention" value="1" {{ old('is_prevention', $t?->is_prevention) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_prevention">Schadepreventietraining (dan komen M7b en formulier F2 in stap 5)</label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="notes">Opmerkingen</label>
    <textarea class="form-control" name="notes" id="notes" rows="2" maxlength="2000">{{ old('notes', $t?->notes) }}</textarea>
</div>
