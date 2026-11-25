@php
    $template = $mailTemplate ?? null;
    $recipientRules = [
        'werkgever' => 'Werkgever (contactpersoon)',
        'opleider' => 'Opleider',
        'chauffeur' => 'Chauffeur (per chauffeur een mail)',
        'deelnemers' => 'Deelnemers training (per deelnemer een mail)',
        'intern' => 'Intern (academy@)',
    ];
@endphp
<div class="border rounded p-3 mb-3">
    <strong>Academy</strong>
    <span class="text-muted small">Alleen invullen voor sjablonen van de Academy (trigger Academy).</span>
    <div class="form-row mt-2">
        <div class="form-group col-md-3">
            <label for="code">Code</label>
            <input class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" type="text" name="code" id="code" maxlength="20" placeholder="bijv. M2b" value="{{ old('code', $template?->code) }}">
            @if($errors->has('code'))<div class="invalid-feedback">{{ $errors->first('code') }}</div>@endif
        </div>
        <div class="form-group col-md-5">
            <label for="recipient_rule">Standaard ontvanger</label>
            <select class="form-control" name="recipient_rule" id="recipient_rule">
                <option value="">-</option>
                @foreach($recipientRules as $value => $label)
                    <option value="{{ $value }}" {{ old('recipient_rule', $template?->recipient_rule) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="cc_rule">Standaard cc</label>
            <select class="form-control" name="cc_rule" id="cc_rule">
                <option value="">-</option>
                <option value="werkgever" {{ old('cc_rule', $template?->cc_rule) === 'werkgever' ? 'selected' : '' }}>Werkgever (contactpersoon)</option>
            </select>
        </div>
    </div>
    <div class="form-group mb-0">
        <label for="attachments">Bijlagen (bijv. formulier F1 of F2)</label>
        <input class="form-control-file" type="file" name="attachments[]" id="attachments" multiple>
        @if($template && $template->exists)
            @foreach($template->getMedia('attachments') as $media)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remove_attachment_ids[]" value="{{ $media->id }}" id="remove_{{ $media->id }}">
                    <label class="form-check-label" for="remove_{{ $media->id }}">{{ $media->file_name }} verwijderen</label>
                </div>
            @endforeach
        @endif
    </div>
    <div class="small text-muted mt-2">
        Tags Academy: <code>[bedrijf] [contactpersoon] [voornaam contactpersoon] [maand] [startdatum] [datum eerste mail] [geldigheidsoverzicht]</code>
        <code>[training] [cbr_code] [datum training] [locatie] [opleider] [uren] [datum akkoord] [deelnemerslijst] [deelnemers zonder e-mail]</code>
        <code>[chauffeur] [voornaam] [achternaam] [ontbrekend item 1] [ontbrekend item 2] [ontbrekend item 3] [ontbrekende items]</code>
        <code>[document] [vervaldatum] [dagen tot verloop] [nieuwe vervaldatum]</code>
    </div>
</div>
