<?php

return [
    'userManagement' => [
        'title'          => 'Gebruikersbeheer',
        'title_singular' => 'Gebruikersbeheer',
    ],
    'permission' => [
        'title'          => 'Permissies',
        'title_singular' => 'Permissie',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'title'             => 'Titel',
            'title_helper'      => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'role' => [
        'title'          => 'Rollen',
        'title_singular' => 'Rol',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => ' ',
            'title'              => 'Titel',
            'title_helper'       => ' ',
            'permissions'        => 'Rechten',
            'permissions_helper' => ' ',
            'created_at'         => 'Created at',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => ' ',
        ],
    ],
    'user' => [
        'title'          => 'Gebruikers',
        'title_singular' => 'Gebruiker',
        'fields'         => [
            'id'                       => 'ID',
            'id_helper'                => ' ',
            'name'                     => 'Naam',
            'name_helper'              => ' ',
            'email'                    => 'Email',
            'email_helper'             => ' ',
            'email_verified_at'        => 'Email geverifierd op',
            'email_verified_at_helper' => ' ',
            'password'                 => 'Wachtwoord',
            'password_helper'          => ' ',
            'roles'                    => 'Rollen',
            'roles_helper'             => ' ',
            'remember_token'           => 'Herrining Token',
            'remember_token_helper'    => ' ',
            'created_at'               => 'Created at',
            'created_at_helper'        => ' ',
            'updated_at'               => 'Updated at',
            'updated_at_helper'        => ' ',
            'deleted_at'               => 'Deleted at',
            'deleted_at_helper'        => ' ',
            'team'                     => 'Team',
            'team_helper'              => ' ',
        ],
    ],
    'claim' => [
        'title'          => 'Schadedossier',
        'title_singular' => 'Schadedossier',
        'fields'         => [
            'id'                               => 'ID',
            'id_helper'                        => ' ',
            'assign_self'                      => 'Zelf afhandelen',
            'assign_self_helper'               => 'Wanneer deze optie niet is aangevinkt, kunt u het schadedossier NIET meer aanpassen zodra deze is opgeslagen.',
            'subject'                          => 'Onderwerp',
            'subject_helper'                   => ' ',
            'claim_number'                     => 'Schadedossier nummer',
            'claim_number_helper'              => ' ',
            'status'                           => 'Status',
            'status_helper'                    => ' ',
            'created_at'                       => 'Created at',
            'created_at_helper'                => ' ',
            'updated_at'                       => 'Updated at',
            'updated_at_helper'                => ' ',
            'deleted_at'                       => 'Deleted at',
            'deleted_at_helper'                => ' ',
            'injury'                           => 'Letsel',
            'injury_helper'                    => ' ',
            'contact_lawyer'                   => 'Jurist inschakelen?',
            'contact_lawyer_helper'            => ' ',
            'injury_other'                     => 'Letsel anders',
            'injury_other_helper'              => ' ',
            'company'                          => 'Bedrijf',
            'company_helper'                   => ' ',
            'injury_office'                    => 'Letselbureau',
            'injury_office_helper'             => ' ',
            'vehicle'                          => 'Voertuig',
            'vehicle_helper'                   => ' ',
            'vehicle_plates'                   => 'Voertuig kenteken',
            'vehicle_plates_helper'            => ' ',
            'driver_vehicle'                   => 'Chauffeur voertuig',
            'driver_vehicle_helper'            => '',
            'vehicle_opposite'                 => 'Voertuig wederpartij',
            'vehicle_opposite_helper'          => ' ',
            'vehicle_plates_opposite'          => 'Voertuig kenteken wederpartij',
            'vehicle_plates_opposite_helper'   => ' ',
            'driver_vehicle_opposite'          => 'Chauffeur voertuig wederpartij',
            'driver_vehicle_opposite_helper'   => '',
            'opposite_type'                    => 'Wederpartij type',
            'opposite_type_helper'             => ' ',
            'obstacle'                         => 'Obstakel',
            'obstacle_helper'                  => '',
            'damage_origin'                    => 'Schade oorzaak',
            'damage_origin_helper'             => ' ',
            'damaged_area'                     => 'Schadeplaats',
            'damaged_area_helper'              => ' ',
            'damage_origin_opposite'           => 'Schade oorzaak wederpartij',
            'damage_origin_opposite_helper'    => ' ',
            'damaged_area_opposite'            => 'Schadeplaats wederpartij',
            'damaged_area_opposite_helper'     => ' ',
            'recovery_office'                  => 'Schadehersteller',
            'recovery_office_helper'           => ' ',
            'damage_kind'                      => 'Soort schade',
            'damage_costs'                     => 'Schade kosten',
            'damage_costs_helper'              => ' ',
            'recovery_costs'                   => 'Herstel kosten',
            'recovery_costs_helper'            => ' ',
            'replacement_vehicle_costs'        => 'Vervangend vervoer kosten',
            'replacement_vehicle_costs_helper' => ' ',
            'expert_costs'                     => 'Expert kosten',
            'expert_costs_helper'              => ' ',
            'other_costs'                      => 'Andere kosten',
            'other_costs_helper'               => ' ',
            'deductible_excess_costs'          => 'Eigen risico kosten',
            'deductible_excess_costs_helper'   => ' ',
            'insurance_costs'                  => 'Verzekering kosten',
            'insurance_costs_helper'           => ' ',
            'expertise_office'                 => 'Expertisebureau',
            'expertise_office_helper'          => ' ',
            'expert_report_is_in'              => 'Expert rapport is binnen',
            'expert_report_is_in_helper'       => ' ',
            'requested_at'                     => 'Aangevraagd op',
            'requested_at_helper'              => ' ',
            'report_received_at'               => 'Rapport ontvangen op',
            'report_received_at_helper'        => ' ',
            'team'                             => 'Team',
            'team_helper'                      => ' ',
            'damage_files'                     => 'Schade bestanden',
            'damage_files_helper'              => ' ',
            'report_files'                     => 'Rapport bestanden',
            'report_files_helper'              => ' ',
            'financial_files'                  => 'Financiele bestanden',
            'financial_files_helper'           => ' ',
            'other_files'                      => 'Andere bestanden',
            'other_files_helper'               => ' ',
            'damaged_part'                     => 'Schade aard',
            'damaged_part_helper'              => ' ',
            'damaged_part_opposite'            => 'Schade aard wederpartij',
            'damaged_part_opposite_helper'     => ' ',
            'date_accident'                    => 'Datum schade',
            'date_accident_helper'             => ' ',
            'recoverable_claim'                => 'Verhaalbaar',
            'recoverable_claim_helper'         => ' ',
            'invoice_settlement'               => 'Factuur afgewikkeld',
            'invoice_settlement_helper'        => ' ',
            'invoice_comment'                  => 'Factuur opmerking',
            'invoice_comment_helper'           => '',
            'invoice_amount'                   => 'Factuur bedrag',
            'invoice_amount_helper'            => '',
            'opposite_claim_no'                => 'Schadedossier nr. wederpartij',
            'opposite_claim_no_helper'         => '',
            'assignee'                         => 'Behandelaar dossier',
            'assignee_helper'                  => '',
        ],
    ],
    'company' => [
        'title'          => 'Bedrijf',
        'title_singular' => 'Bedrijf',
        'fields'         => [
            'id'                  => 'ID',
            'id_helper'           => ' ',
            'name'                => 'Naam',
            'name_helper'         => ' ',
            'company_type'        => 'Bedrijf Type',
            'company_type_helper' => ' ',
            'street'              => 'Straat',
            'street_helper'       => ' ',
            'zipcode'             => 'Postcode',
            'zipcode_helper'      => ' ',
            'city'                => 'Stad',
            'city_helper'         => ' ',
            'country'             => 'Land',
            'country_helper'      => ' ',
            'phone'               => 'Telefoon',
            'phone_helper'        => ' ',
            'active'              => 'Actief',
            'active_helper'       => ' ',
            'created_at'          => 'Created at',
            'created_at_helper'   => ' ',
            'updated_at'          => 'Updated at',
            'updated_at_helper'   => ' ',
            'deleted_at'          => 'Deleted at',
            'deleted_at_helper'   => ' ',
            'description'         => 'Beschrijving',
            'description_helper'  => ' ',
            'team'                => 'Team',
            'team_helper'         => ' ',
        ],
    ],
    'task' => [
        'title'          => 'Taken',
        'title_singular' => 'Taken',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => ' ',
            'description'        => 'Beschrijving',
            'description_helper' => ' ',
            'created_at'         => 'Created at',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => ' ',
            'user'               => 'Gebruiker',
            'user_helper'        => ' ',
            'task_number'        => 'Taak nummer',
            'task_number_helper' => ' ',
            'claim'              => 'Schadedossier',
            'claim_helper'       => ' ',
            'team'               => 'Team',
            'team_helper'        => ' ',
            'deadline_at'        => 'Deadline over',
            'deadline_at_helper' => ' ',
            'status'             => 'Status',
            'status_helper'      => ' ',
        ],
    ],
    'contact' => [
        'title'          => 'Contacten',
        'title_singular' => 'Contacten',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'user'              => 'Gebruiker',
            'user_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'first_name'        => 'Voornaam',
            'first_name_helper' => ' ',
            'last_name'         => 'Achternaam',
            'last_name_helper'  => ' ',
            'email'             => 'Email',
            'email_helper'      => ' ',
            'company'           => 'Bedrijf',
            'company_helper'    => ' ',
            'team'              => 'Team',
            'team_helper'       => ' ',
            'newsletter'        => 'Nieuwsbrief?',
            'newsletter_helper' => ' ',
            'create_user'       => 'Maak gebruiker aan voor deze contact',
            'create_user_helper'=> '',
            'is_driver'         => 'Is deze contact een chauffeur?',
            'is_driver_helper'  => ''
        ],
    ],
    'injuryOffice' => [
        'title'          => 'Letsel Bureau',
        'title_singular' => 'Letsel Bureau',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'company'           => 'Bedrijf',
            'company_helper'    => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'identifier'        => 'Identificatie',
            'identifier_helper' => ' ',
            'team'              => 'Team',
            'team_helper'       => ' ',
        ],
    ],
    'business' => [
        'title'          => 'Bedrijven',
        'title_singular' => 'Bedrijven',
    ],
    'expertiseOffice' => [
        'title'          => 'Expertise Bureau',
        'title_singular' => 'Expertise Bureau',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'company'           => 'Bedrijf',
            'company_helper'    => ' ',
            'identifier'        => 'Identificatie',
            'identifier_helper' => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'team'              => 'Team',
            'team_helper'       => ' ',
        ],
    ],
    'vehicle' => [
        'title'          => 'Voertuigen',
        'title_singular' => 'Voertuigen',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Naam',
            'name_helper'       => ' ',
            'plates'            => 'Kenteken',
            'plates_helper'     => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'company'           => 'Bedrijf',
            'company_helper'    => ' ',
            'team'              => 'Team',
            'team_helper'       => ' ',
            'driver'            => 'Chauffeur',
            'driver_helper'     => ' ',
        ],
    ],
    'driver' => [
        'title'          => 'Chauffeur',
        'title_singular' => 'Chauffeur',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'first_name'        => 'Voornaam',
            'first_name_helper' => ' ',
            'last_name'         => 'Achternaam',
            'last_name_helper'  => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'email'             => 'Email',
            'email_helper'      => ' ',
            'phone'             => 'Telefoon',
            'phone_helper'      => ' ',
            'team'              => 'Team',
            'team_helper'       => ' ',
        ],
    ],
    'vehicleInformation' => [
        'title'          => 'Voertuigen informatie',
        'title_singular' => 'Voertuigen informatie',
    ],
    'vehicleOpposite' => [
        'title'          => 'Voertuigen wederpartij',
        'title_singular' => 'Voertuigen wederpartij',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Naam',
            'name_helper'       => ' ',
            'plates'            => 'Kenteken',
            'plates_helper'     => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'team'              => 'Team',
            'team_helper'       => ' ',
            'driver'            => 'Chauffeur',
            'driver_helper'     => ' ',
        ],
    ],
    'recoveryOffice' => [
        'title'          => 'Herstel Bureau',
        'title_singular' => 'Herstel Bureau',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'company'           => 'Bedrijf',
            'company_helper'    => ' ',
            'identifier'        => 'Identificatie',
            'identifier_helper' => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'team'              => 'Team',
            'team_helper'       => ' ',
        ],
    ],
    'team' => [
        'title'          => 'Teams',
        'title_singular' => 'Team',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated At',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted At',
            'deleted_at_helper' => ' ',
            'name'              => 'Naam',
            'name_helper'       => ' ',
            'owner'             => 'Eigenaar',
            'owner_helper'      => ' ',
        ],
    ],
    'auditLog' => [
        'title'          => 'Audit logboeken',
        'title_singular' => 'Audit logboeken',
        'fields'         => [
            'id'                  => 'ID',
            'id_helper'           => ' ',
            'description'         => 'Description',
            'description_helper'  => ' ',
            'subject_id'          => 'Subject ID',
            'subject_id_helper'   => ' ',
            'subject_type'        => 'Subject Type',
            'subject_type_helper' => ' ',
            'user_id'             => 'User ID',
            'user_id_helper'      => ' ',
            'properties'          => 'Properties',
            'properties_helper'   => ' ',
            'host'                => 'Host',
            'host_helper'         => ' ',
            'created_at'          => 'Created at',
            'created_at_helper'   => ' ',
            'updated_at'          => 'Updated at',
            'updated_at_helper'   => ' ',
        ],
    ],
    'note' => [
        'title'          => 'Aantekeningen',
        'title_singular' => 'Aantekeningen',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => ' ',
            'title'              => 'Titel',
            'title_helper'       => ' ',
            'description'        => 'Beschrijving',
            'description_helper' => ' ',
            'claim'              => 'Schadedossier',
            'claim_helper'       => ' ',
            'user'               => 'Gebruiker',
            'user_helper'        => ' ',
            'created_at'         => 'Created at',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => ' ',
            'team'               => 'Team',
            'team_helper'        => ' ',
        ],
    ],
    'opposite' => [
        'fields'         => [
            'id'                  => 'ID',
            'id_helper'           => ' ',
            'name'                => 'Naam',
            'name_helper'         => ' ',
            'street'              => 'Straat',
            'street_helper'       => ' ',
            'zipcode'             => 'Postcode',
            'zipcode_helper'      => ' ',
            'city'                => 'Stad',
            'city_helper'         => ' ',
            'country'             => 'Land',
            'country_helper'      => ' ',
            'phone'               => 'Telefoon',
            'phone_helper'        => ' ',
            'email'              => 'Email',
            'email_helper'       => ' ',
            'created_at'          => 'Created at',
            'created_at_helper'   => ' ',
            'updated_at'          => 'Updated at',
            'updated_at_helper'   => ' ',
            'deleted_at'          => 'Deleted at',
            'deleted_at_helper'   => ' ',
        ],
    ],
    'analytics' => [
        'title'          => 'Statistieken',
        'title_report'   => 'Statistiek report',
        'title_singular' => 'Statistiek',
    ],
    'invoices' => [
        'title'          => 'Facturatie',
        'title_singular' => 'Facturatie',
    ],
    'comment' => [
        'title' => 'Opmerkingen',
        'title_singular' => 'Opmerking',
        'fields' => [
            'id'                => 'ID',
            'id_helper'         => '',
            'body'              => 'Opmerking',
            'body_helper'       => '',
            'commentable'       => 'Opmerking ID',
            'commentable_helper' => '',
            'commentable_type'  => 'Opmerking Type',
            'commentable_helper' => '',
        ]
    ],
    'mailTemplates' => [
        'title' => 'Mail Sjablonen',
        'title_singular' => 'Mail Sjabloon',
        'fields' => [
            'id' => 'ID',
            'name' => 'Naam',
            'name_helper' => '',
            'subject' => 'Onderwerp',
            'subject_helper' => '',
            'body' => 'Sjabloon',
            'body_helper' => ''
        ]
    ]
];
