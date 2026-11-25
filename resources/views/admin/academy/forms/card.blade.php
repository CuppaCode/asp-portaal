<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <title>Check je papieren</title>
    <style>
        @page { margin: 28px 36px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #1b2a3a; }
        h1 { font-size: 20px; margin: 0 0 2px 0; color: #2f4b9a; }
        h2 { font-size: 12px; margin: 14px 0 4px 0; color: #2f4b9a; border-bottom: 2px solid #4cc437; padding-bottom: 2px; }
        .sub { color: #66707b; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 4px 5px; vertical-align: bottom; }
        .label { width: 32%; color: #66707b; }
        .box { border-bottom: 1px solid #1b2a3a; height: 15px; }
        .known { font-weight: bold; }
        .fill { background: #fff7d6; }
        th { text-align: left; font-size: 9px; text-transform: uppercase; color: #66707b; border-bottom: 1px solid #c9ced4; }
        .docs td { border-bottom: 1px solid #e3e6ea; height: 20px; }
        .check { font-size: 13px; }
        .stop { border: 1px solid #c0392b; color: #c0392b; padding: 5px 7px; margin-top: 10px; }
        .note { margin-top: 8px; }
        .sign td { padding-top: 22px; }
        .page-break { page-break-after: always; }
        .small { font-size: 9px; color: #66707b; }
    </style>
</head>
<body>
@foreach($pages as $page)
    @php
        $docs = $page['documents'];
        $field = function (string $label, ?string $value) {
            $class = $value ? 'known' : 'fill';
            return '<tr><td class="label">' . e($label) . '</td><td class="box ' . $class . '">' . e($value ?? '') . '</td></tr>';
        };
    @endphp
    <div class="{{ !$loop->last ? 'page-break' : '' }}">
        <h1>Check je papieren</h1>
        <div class="sub">
            ASP Academy · {{ $page['company'] }} · formulier van {{ $page['date'] }}
        </div>

        <p>
            Beste {{ $page['first_name'] ?: 'chauffeur' }},<br>
            Dit zijn de gegevens die wij van je hebben. Controleer ze en vul de gele vakjes in. Klopt iets niet?
            Streep het door en schrijf de juiste gegevens ernaast. Neem de datums over van je rijbewijs (kolom 11 en 12),
            je chauffeurspas (veld 4b) en Mijn CBR.
        </p>

        <h2>Persoon</h2>
        <table>
            {!! $field('Naam', $page['name']) !!}
            {!! $field('Functie', $page['function']) !!}
            {!! $field('Geboortedatum', $page['birthdate']) !!}
            {!! $field('E-mailadres', $page['email']) !!}
            {!! $field('Telefoon', $page['phone']) !!}
            {!! $field('Adres', $page['street']) !!}
            {!! $field('Postcode en plaats', trim(($page['zipcode'] ?? '') . ' ' . ($page['city'] ?? '')) ?: null) !!}
        </table>

        <h2>Rijbewijs en chauffeurspas</h2>
        <table>
            {!! $field('Rijbewijsnummer', $page['license_number']) !!}
            {!! $field('Rijbewijscategorieën', $page['license_categories']) !!}
            {!! $field('Rijbewijs geldig tot (kolom 11)', $docs['rijbewijs']['date'] ?? null) !!}
            {!! $field('Code 95 geldig tot (kolom 12)', $docs['code_95']['date'] ?? null) !!}
            {!! $field('Code 95-uren behaald (Mijn CBR)', $page['code95_hours']) !!}
            {!! $field('CBR-klantnummer', $page['cbr_customer_number']) !!}
            {!! $field('Pasnummer chauffeurspas', $page['pass_number']) !!}
            {!! $field('Chauffeurspas geldig tot (veld 4b)', $docs['chauffeurspas']['date'] ?? null) !!}
        </table>

        <h2>Certificaten</h2>
        <table class="docs">
            <thead>
                <tr>
                    <th style="width: 34%;">Certificaat</th>
                    <th style="width: 40%;">Geldig tot (dd-mm-jjjj) / behaald op</th>
                    <th>Heb ik niet</th>
                </tr>
            </thead>
            <tbody>
                @foreach($docs as $code => $document)
                    @continue(in_array($code, ['rijbewijs', 'code_95', 'chauffeurspas'], true))
                    <tr>
                        <td>{{ $document['name'] }} @if($document['one_off'])<span class="small">(eenmalig: datum van behalen)</span>@endif</td>
                        <td class="{{ $document['date'] || $document['not_applicable'] ? 'known' : 'fill' }}">
                            {{ $document['not_applicable'] ? '' : $document['date'] }}
                        </td>
                        <td class="check">{{ $document['not_applicable'] ? '☒' : '☐' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="note">
            Stuur bij dit formulier een foto of scan van je rijbewijs (voorkant en achterkant) en van je chauffeurspas mee.
            <strong>Lak je BSN en je pasfoto op het rijbewijs af</strong> voordat je het verstuurt.
        </div>

        <div class="stop">
            <strong>STOP</strong> Vul op dit formulier geen BSN, nationaliteit of medische gegevens in.
        </div>

        <table class="sign">
            <tr>
                <td style="width: 55%;"><div class="box"></div><div class="small">Handtekening</div></td>
                <td style="width: 5%;"></td>
                <td><div class="box"></div><div class="small">Datum</div></td>
            </tr>
        </table>

        <div class="note small">
            Stuur het ingevulde formulier terug naar {{ $page['reply_to'] }} of lever het in bij je werkgever.
            Vragen? Bel ASP Academy: 085 – 0160 670.
        </div>
    </div>
@endforeach
</body>
</html>
