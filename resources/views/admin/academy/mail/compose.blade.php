@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>{{ $template->code }} · {{ $template->name }}</strong>
        <a class="btn btn-default btn-sm" href="{{ $backUrl }}">Annuleren</a>
    </div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        @if($item)
            <div class="alert alert-light border small">
                Na verzenden wordt de regel afgevinkt: <em>{{ $item->definition()['label'] ?? $item->item_key }}</em>.
            </div>
        @endif

        @if(!empty($placeholders))
            <div class="alert alert-warning">
                Nog in te vullen: {{ implode(', ', $placeholders) }}.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.academy.mail.send') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="template" value="{{ $template->code }}">
            <input type="hidden" name="context" value="{{ $context }}">
            <input type="hidden" name="id" value="{{ $subjectModel->getKey() }}">
            <input type="hidden" name="item" value="{{ $item?->id }}">
            <input type="hidden" name="level" value="{{ $level }}">
            <input type="hidden" name="bulk" value="{{ $bulk ? 1 : 0 }}">

            <div class="small text-muted mb-3">Van en naar: {{ $sender }}. Antwoorden komen binnen op dat adres.</div>

            @if($bulk)
                <div class="form-group">
                    <label>Ontvangers (elke chauffeur krijgt een eigen mail; persoonlijke gegevens worden automatisch ingevuld)</label>
                    @if($targets->isEmpty())
                        <div class="alert alert-warning mb-0">Er zijn geen ontvangers (chauffeurs met een e-mailadres). Leg eerst de e-mailadressen vast op de chauffeurskaart.</div>
                    @else
                        <table class="table table-sm table-bordered">
                            <tbody>
                                @foreach($targets as $target)
                                    <tr>
                                        <td style="width: 30px;"><input type="checkbox" name="drivers[]" value="{{ $target['driver']->id }}" checked></td>
                                        <td>{{ $target['name'] }}</td>
                                        <td>{{ $target['email'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @else
                <div class="form-group">
                    <label class="required" for="to">Aan</label>
                    <input class="form-control" type="text" name="to" id="to" value="{{ $to }}" placeholder="adres, adres" required>
                </div>
            @endif

            <div class="form-group">
                <label for="cc">Cc</label>
                <input class="form-control" type="text" name="cc" id="cc" value="{{ $cc }}">
            </div>

            <div class="form-group">
                <label class="required" for="subject">Onderwerp</label>
                <input class="form-control" type="text" name="subject" id="subject" value="{{ $subjectLine }}" maxlength="255" required>
            </div>

            <div class="form-group">
                <label class="required" for="body">Tekst</label>
                <textarea class="form-control" name="body" id="body" rows="16" required>{{ $body }}</textarea>
                <small class="form-text text-muted">Vul alle [plaatshouders] in. Het portaal verstuurt niet zolang er nog een plaatshouder in staat.</small>
            </div>

            <div class="form-group">
                <label>Bijlagen</label>
                @if($templateAttachments->isNotEmpty())
                    <ul class="mb-2">
                        @foreach($templateAttachments as $media)
                            <li>{{ $media->file_name }} <span class="text-muted small">(uit het sjabloon)</span></li>
                        @endforeach
                    </ul>
                @endif
                <input class="form-control-file" type="file" name="attachments[]" multiple>
                @if($attachForm)
                    <div class="form-check mt-2">
                        <input type="hidden" name="attach_form" value="0">
                        <input class="form-check-input" type="checkbox" name="attach_form" id="attach_form" value="1" checked>
                        <label class="form-check-label" for="attach_form">
                            @if($attachForm === 'driver')
                                Formulier 'Check je papieren' als bijlage, ingevuld met de gegevens van de chauffeur (per chauffeur een eigen formulier)
                            @else
                                Formulier 'Check je papieren' als bijlage voor de chauffeurs zonder e-mail (een pagina per chauffeur)
                            @endif
                        </label>
                    </div>
                @endif
            </div>

            <div class="border rounded p-3 mb-3">
                <strong>Controle vóór verzending</strong>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="checks[]" value="placeholders" id="check_1" required><label class="form-check-label" for="check_1">Geen [plaatshouders] meer</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="checks[]" value="recipients" id="check_2" required><label class="form-check-label" for="check_2">Juiste ontvanger en cc</label></div>
                <div class="form-check"><input class="form-check-input" type="checkbox" name="checks[]" value="attachment" id="check_3" required><label class="form-check-label" for="check_3">Juiste bijlage</label></div>
                @if($trainingChecks)
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="checks[]" value="training" id="check_4" required><label class="form-check-label" for="check_4">Datum, tijd en adres gelijk aan de bevestiging van de opleider</label></div>
                @endif
            </div>

            <button class="btn btn-primary" type="submit">Versturen</button>
        </form>
    </div>
</div>

@endsection
