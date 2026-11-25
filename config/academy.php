<?php

/*
|--------------------------------------------------------------------------
| ASP Academy
|--------------------------------------------------------------------------
|
| Step definitions, status thresholds and signal rules for the Academy module.
|
| Item definition keys:
|   step          Step number (1-3 per employer, 4-6 per training).
|   label         Text shown to the user.
|   mail          Mail template code (config via mail_templates.code) or null.
|   form          Form code (F1, F2) or null.
|   anchor        Where the deadline starts: 'company_start', 'training_date' or the key of another item
|                 (its done date is used). null = no deadline.
|   offset        Number of days (or working days).
|   unit          'workdays' or 'days'.
|   direction     'after' (default) or 'before' the anchor.
|   when          Short deadline description shown with the item.
|   can_na        Whether the user may mark the item as not applicable.
|   derived_na    Name of a rule in AcademyChecklistService that sets "n.v.t." automatically.
|   requires      Name of a rule that must be satisfied before the item can be ticked.
|
*/

return [

    'mail' => [
        'from_address' => env('ACADEMY_MAIL_FROM', 'academy@autoschadeplan.nl'),
        'from_name' => env('ACADEMY_MAIL_FROM_NAME', 'ASP Academy'),
        'reply_to' => env('ACADEMY_MAIL_REPLY_TO', 'academy@autoschadeplan.nl'),
    ],

    'workdays' => [
        // Dutch national holidays are skipped when counting working days.
        'skip_holidays' => true,
    ],

    'status' => [
        // green: valid longer than this many months
        'green_months' => 6,
        // red: expired, missing or within this many days
        'red_days' => 90,
    ],

    'code95' => [
        'hours_required' => 35,
        'theory_required' => 28,
        'practice_required' => 7,
        'cycle_years' => 5,
    ],

    'todo' => [
        // Tasks that are late or due within this many days appear in "Nu te doen".
        'days_ahead' => 7,
        // Due within this many days: shown in orange.
        'warn_days' => 2,
    ],

    // Advice per document type on the "Bewaken" tab.
    'monitoring' => [
        'actions' => [
            'rijbewijs' => 'Chauffeur start keuring en verlenging',
            'chauffeurspas' => 'Chauffeur vraagt nieuwe pas aan bij Kiwa',
            'code_95' => 'Ontbrekende uren plannen (stap 4)',
        ],
    ],

    /*
    | Routine checks. 'daily' appears every working day; 'monday' and 'first_workday'
    | are filled in later (not yet defined by the work instruction).
    */
    'routine' => [
        'daily' => [
            'inbox' => 'Inbox academy@ leeg: elk bericht beantwoord of als taak vastgelegd',
            'scans' => 'Nieuwe scans en certificaten verwerkt op de kaart',
        ],
        'monday' => [],
        'first_workday' => [],
    ],

    'steps' => [
        1 => [
            'title' => 'Werkgever vastleggen',
            'start' => 'De werkgever heeft opdracht gegeven en de verwerkersovereenkomst is getekend.',
            'done' => 'Van elke chauffeur op de namenlijst zijn alle gegevens en documenten binnen, of de ontbrekende namen zijn aan de werkgever gemeld.',
            'stop' => 'Vraag nooit om BSN, pasfoto, nationaliteit of medische gegevens. Komen ze toch binnen: weglakken of verwijderen voordat je opslaat.',
        ],
        2 => [
            'title' => 'Chauffeurskaart vastleggen',
            'start' => 'Stap 1 is gereed voor deze werkgever.',
            'done' => 'Elke kaart is volledig en gecontroleerd door een tweede persoon.',
            'stop' => 'Een datum die je niet goed kunt lezen? Niet invullen, maar opnieuw opvragen. Een verkeerde vervaldatum is de grootste foutbron.',
        ],
        3 => [
            'title' => 'Geldigheidsoverzicht naar de werkgever',
            'start' => 'Stap 2 is gereed.',
            'done' => 'Elke 1e werkdag van de maand is het overzicht (M4) verstuurd.',
            'stop' => null,
        ],
        4 => [
            'title' => 'Advies en schriftelijk akkoord',
            'start' => 'Het trainingsdossier is aangemaakt.',
            'done' => 'Het akkoord van de werkgever is per mail binnen.',
            'stop' => 'Een mondeling akkoord is geen akkoord. Vink stap 4 pas af als het akkoord per mail binnen is.',
        ],
        5 => [
            'title' => 'Opleiding plannen en bevestigen',
            'start' => 'Stap 4 is gereed.',
            'done' => 'Alle bevestigingen zijn verstuurd en de definitieve namen staan bij de opleider.',
            'stop' => 'Controleer in M7 en M8 datum, tijd, adres en CBR-code tegen de bevestiging van de opleider, niet tegen je eigen planning. Gaat het niet om een Code 95-module, vermeld dan "geen Code 95-uren".',
        ],
        6 => [
            'title' => 'Training uitvoeren en afronden',
            'start' => 'Het is de trainingsdag.',
            'done' => 'Aanwezigheid, CBR-uren, certificaat, kaart, M9 en factuur zijn afgehandeld.',
            'stop' => 'Zet geen uren op de kaart die nog niet in het CBR-register staan.',
        ],
    ],

    'items' => [

        // Step 7: after a Code 95 renewal (one item per new end date)
        'certificate' => [
            'm12' => [
                'step' => 7,
                'label' => 'Code 95 verlengd: M12 en jaarplanning verstuurd',
                'mail' => 'M12',
                'anchor' => null,
                'can_na' => false,
            ],
        ],

        'company' => [
            'verwerkersovereenkomst' => [
                'step' => 1,
                'label' => 'Verwerkersovereenkomst getekend retour',
                'mail' => 'M1',
                'anchor' => 'company_start',
                'offset' => 0,
                'unit' => 'days',
                'when' => 'Vóór start',
                'can_na' => false,
            ],
            'startmail' => [
                'step' => 1,
                'label' => 'Startmail met werkwijze (A4) en verzoek om namenlijst verstuurd',
                'mail' => 'M1',
                'anchor' => 'company_start',
                'offset' => 0,
                'unit' => 'workdays',
                'when' => 'Dag 0',
                'can_na' => false,
            ],
            'namenlijst' => [
                'step' => 1,
                'label' => 'Namenlijst ontvangen: naam, functie, e-mail (indien bekend)',
                'anchor' => 'company_start',
                'offset' => 5,
                'unit' => 'workdays',
                'when' => 'Binnen 5 werkdagen',
                'can_na' => false,
            ],
            'gegevensverzoek' => [
                'step' => 1,
                'label' => 'Gegevensverzoek naar elke chauffeur met e-mailadres',
                'mail' => 'M2',
                'anchor' => 'namenlijst',
                'offset' => 2,
                'unit' => 'workdays',
                'when' => 'Binnen 2 werkdagen',
                'can_na' => false,
                'bulk' => 'drivers_with_email',
                'attach_form' => 'driver',
            ],
            'chauffeurs_zonder_mail' => [
                'step' => 1,
                'label' => 'Chauffeurs zonder e-mail: M2b met formulier Check je papieren (F1) naar de werkgever; namen en inleverdatum ingevuld',
                'mail' => 'M2b',
                'form' => 'F1',
                'anchor' => 'namenlijst',
                'offset' => 0,
                'unit' => 'workdays',
                'when' => 'Zelfde dag',
                'can_na' => true,
                'derived_na' => 'all_drivers_have_email',
                'attach_form' => 'no_email',
            ],
            'herinnering_1' => [
                'step' => 1,
                'label' => 'Eerste herinnering aan wie niet reageerde',
                'mail' => 'M3',
                'anchor' => 'gegevensverzoek',
                'offset' => 5,
                'unit' => 'workdays',
                'when' => 'Na 5 werkdagen',
                'can_na' => true,
                'bulk' => 'drivers_missing_data',
                'attach_form' => 'driver',
            ],
            'herinnering_2' => [
                'step' => 1,
                'label' => 'Tweede herinnering',
                'mail' => 'M3',
                'anchor' => 'gegevensverzoek',
                'offset' => 10,
                'unit' => 'workdays',
                'when' => 'Na 10 werkdagen',
                'can_na' => true,
                'bulk' => 'drivers_missing_data',
                'attach_form' => 'driver',
            ],
            'f1_gecontroleerd' => [
                'step' => 1,
                'label' => 'Ingeleverde F1-formulieren gecontroleerd tegen Mijn CBR; e-mailadres vastgelegd als het is ingevuld',
                'form' => 'F1',
                'anchor' => null,
                'when' => 'Zelfde dag',
                'can_na' => true,
                'derived_na' => 'all_drivers_have_email',
            ],
            'namen_gemeld' => [
                'step' => 1,
                'label' => 'Ontbrekende namen gemeld aan contactpersoon werkgever',
                'anchor' => 'gegevensverzoek',
                'offset' => 15,
                'unit' => 'workdays',
                'when' => 'Na 15 werkdagen',
                'can_na' => true,
            ],

            // Step 3: recurring, one item per month (period YYYY-MM).
            'monthly_overview' => [
                'step' => 3,
                'label' => 'Maandelijks geldigheidsoverzicht (M4) verstuurd',
                'mail' => 'M4',
                'recurring' => 'monthly',
                'can_na' => false,
            ],
        ],

        'training' => [
            // Step 4
            'advies' => [
                'step' => 4,
                'label' => 'Advies opleiding naar de werkgever verstuurd',
                'mail' => 'M5',
                'anchor' => null,
                'can_na' => false,
            ],
            'akkoord' => [
                'step' => 4,
                'label' => 'Schriftelijk akkoord van de werkgever per mail ontvangen',
                'anchor' => null,
                'can_na' => false,
            ],

            // Step 5
            'opdrachtbevestiging' => [
                'step' => 5,
                'label' => 'Opdrachtbevestiging naar opleider: cursus, datum, tijd, locatie, naam + geboortedatum cursist, bedrijf, uren, prijs',
                'mail' => 'M6',
                'anchor' => 'akkoord',
                'offset' => 5,
                'unit' => 'workdays',
                'when' => 'Binnen 5 werkdagen na akkoord',
                'can_na' => false,
            ],
            'inschrijving_bevestigd' => [
                'step' => 5,
                'label' => 'Inschrijving bevestigd door opleider',
                'anchor' => null,
                'when' => 'Vóór datum in M6',
                'can_na' => false,
            ],
            'kaart_ingepland' => [
                'step' => 5,
                'label' => 'Training op chauffeurskaart (status: ingepland) en in maandoverzicht trainingen',
                'anchor' => 'inschrijving_bevestigd',
                'offset' => 0,
                'unit' => 'workdays',
                'when' => 'Zelfde dag',
                'can_na' => true,
            ],
            'deelnemerslijst' => [
                'step' => 5,
                'label' => 'Bevestiging naar werkgever met deelnemerslijst en pdf',
                'mail' => 'M7',
                'anchor' => 'training_date',
                'offset' => 21,
                'unit' => 'days',
                'direction' => 'before',
                'when' => 'Uiterlijk 3 weken vooraf',
                'can_na' => false,
            ],
            'schadepreventie_f2' => [
                'step' => 5,
                'label' => 'Schadepreventietraining: M7b met formulier Aanvullende informatie training (F2) naar de werkgever',
                'mail' => 'M7b',
                'form' => 'F2',
                'anchor' => 'training_date',
                'offset' => 21,
                'unit' => 'days',
                'direction' => 'before',
                'when' => 'Met M7',
                'can_na' => true,
                'derived_na' => 'not_prevention',
            ],
            'f2_retour' => [
                'step' => 5,
                'label' => 'F2 retour ontvangen en ingevuld naar de trainer',
                'form' => 'F2',
                'anchor' => 'training_date',
                'offset' => 10,
                'unit' => 'workdays',
                'direction' => 'before',
                'when' => '10 werkdagen vooraf',
                'can_na' => true,
                'derived_na' => 'not_prevention',
            ],
            'bevestiging_chauffeurs' => [
                'step' => 5,
                'label' => 'Bevestiging naar elke chauffeur met e-mail, cc werkgever, met pdf',
                'mail' => 'M8',
                'anchor' => 'training_date',
                'offset' => 7,
                'unit' => 'days',
                'direction' => 'before',
                'when' => '1 week vooraf',
                'can_na' => true,
                'bulk' => 'participants_with_email',
            ],
            'm7_uitdelen' => [
                'step' => 5,
                'label' => 'Chauffeurs zonder e-mail: werkgever gevraagd pagina 2 van M7 uit te delen',
                'mail' => 'M7',
                'anchor' => 'training_date',
                'offset' => 7,
                'unit' => 'days',
                'direction' => 'before',
                'when' => '1 week vooraf',
                'can_na' => true,
                'derived_na' => 'all_participants_have_email',
            ],
            'wijzigingen_verwerkt' => [
                'step' => 5,
                'label' => 'Wijzigingen verwerkt (deadline werkgever: 2 werkdagen vooraf)',
                'mail' => 'M13',
                'anchor' => 'training_date',
                'offset' => 2,
                'unit' => 'workdays',
                'direction' => 'before',
                'when' => '2 werkdagen vooraf',
                'can_na' => true,
            ],
            'definitieve_namen' => [
                'step' => 5,
                'label' => 'Definitieve namen + geboortedata naar opleider; bij ASP-training presentielijst klaargezet',
                'anchor' => 'training_date',
                'offset' => 1,
                'unit' => 'workdays',
                'direction' => 'before',
                'when' => '1 werkdag vooraf',
                'can_na' => false,
            ],

            // Step 6
            'aanwezigheid' => [
                'step' => 6,
                'label' => 'Aanwezigheid vastgelegd',
                'anchor' => 'training_date',
                'offset' => 0,
                'unit' => 'days',
                'when' => 'Op de trainingsdag',
                'can_na' => false,
                'requires' => 'attendance_recorded',
            ],
            'cbr_uren' => [
                'step' => 6,
                'label' => 'Uren in het CBR-register bevestigd en op de kaart gezet',
                'anchor' => null,
                'can_na' => true,
                'derived_na' => 'no_code95_module',
                'requires' => 'cbr_confirmed',
            ],
            'certificaat' => [
                'step' => 6,
                'label' => 'Certificaat ontvangen',
                'anchor' => null,
                'can_na' => true,
            ],
            'kaart_bijgewerkt' => [
                'step' => 6,
                'label' => 'Chauffeurskaart bijgewerkt',
                'anchor' => null,
                'can_na' => false,
            ],
            'm9' => [
                'step' => 6,
                'label' => 'Afronding naar de werkgever verstuurd',
                'mail' => 'M9',
                'anchor' => null,
                'can_na' => false,
            ],
            'factuur' => [
                'step' => 6,
                'label' => 'Factuur verwerkt',
                'anchor' => null,
                'can_na' => false,
            ],
        ],
    ],

    /*
    | Mail templates seeded by AcademyMailTemplateSeeder.
    | recipient: chauffeur | werkgever | opleider | deelnemers | intern
    | The texts are filled in later by the Academy team.
    */
    'mail_templates' => [
        'M1' => ['name' => 'Startmail werkgever', 'recipient' => 'werkgever'],
        'M2' => ['name' => 'Gegevensverzoek chauffeur', 'recipient' => 'chauffeur', 'cc' => 'werkgever'],
        'M2b' => ['name' => 'Chauffeurs zonder e-mail (formulier F1)', 'recipient' => 'werkgever'],
        'M3' => ['name' => 'Herinnering gegevens', 'recipient' => 'chauffeur', 'cc' => 'werkgever'],
        'M4' => ['name' => 'Maandelijks geldigheidsoverzicht', 'recipient' => 'werkgever'],
        'M5' => ['name' => 'Advies opleiding', 'recipient' => 'werkgever'],
        'M6' => ['name' => 'Opdrachtbevestiging opleider', 'recipient' => 'opleider'],
        'M7' => ['name' => 'Bevestiging deelnemerslijst werkgever', 'recipient' => 'werkgever'],
        'M7b' => ['name' => 'Schadepreventietraining (formulier F2)', 'recipient' => 'werkgever'],
        'M8' => ['name' => 'Bevestiging training chauffeur', 'recipient' => 'deelnemers', 'cc' => 'werkgever'],
        'M9' => ['name' => 'Afronding training', 'recipient' => 'werkgever'],
        'M10' => ['name' => 'M10', 'recipient' => 'werkgever'],
        'M11' => ['name' => 'Signaal vervaldatum document', 'recipient' => 'werkgever'],
        'M12' => ['name' => 'Code 95 verlengd en jaarplanning', 'recipient' => 'werkgever'],
        'M13' => ['name' => 'Wijziging deelnemers training', 'recipient' => 'opleider', 'cc' => 'werkgever'],
    ],
];
