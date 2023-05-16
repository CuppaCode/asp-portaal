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
            'title'             => 'Title',
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
            'title'              => 'Title',
            'title_helper'       => ' ',
            'permissions'        => 'Permissions',
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
            'name'                     => 'Name',
            'name_helper'              => ' ',
            'email'                    => 'Email',
            'email_helper'             => ' ',
            'email_verified_at'        => 'Email verified at',
            'email_verified_at_helper' => ' ',
            'password'                 => 'Password',
            'password_helper'          => ' ',
            'roles'                    => 'Roles',
            'roles_helper'             => ' ',
            'remember_token'           => 'Remember Token',
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
        'title'          => 'Claim',
        'title_singular' => 'Claim',
        'fields'         => [
            'id'                               => 'ID',
            'id_helper'                        => ' ',
            'assign_self'                      => 'Assign Self',
            'assign_self_helper'               => 'If not checked, the claim cannot be edited by you once saved.',
            'subject'                          => 'Subject',
            'subject_helper'                   => ' ',
            'claim_number'                     => 'Claim Number',
            'claim_number_helper'              => ' ',
            'status'                           => 'Status',
            'status_helper'                    => ' ',
            'created_at'                       => 'Created at',
            'created_at_helper'                => ' ',
            'updated_at'                       => 'Updated at',
            'updated_at_helper'                => ' ',
            'deleted_at'                       => 'Deleted at',
            'deleted_at_helper'                => ' ',
            'injury'                           => 'Injury',
            'injury_helper'                    => ' ',
            'contact_lawyer'                   => 'Contact Lawyer',
            'contact_lawyer_helper'            => ' ',
            'injury_other'                     => 'Injury Other',
            'injury_other_helper'              => ' ',
            'company'                          => 'Company',
            'company_helper'                   => ' ',
            'injury_office'                    => 'Injury Office',
            'injury_office_helper'             => ' ',
            'vehicle'                          => 'Vehicle',
            'vehicle_helper'                   => ' ',
            'vehicle_opposite'                 => 'Vehicle Opposite',
            'vehicle_opposite_helper'          => ' ',
            'opposite_type'                    => 'Opposite Type',
            'opposite_type_helper'             => ' ',
            'damaged_part'                     => 'Damaged Part',
            'damaged_part_helper'              => ' ',
            'damage_origin'                    => 'Damage Origin',
            'damage_origin_helper'             => ' ',
            'damaged_area'                     => 'Damaged Area',
            'damaged_area_helper'              => ' ',
            'damaged_part_opposite'            => 'Damaged Part Opposite',
            'damaged_part_opposite_helper'     => ' ',
            'damage_origin_opposite'           => 'Damage Origin Opposite',
            'damage_origin_opposite_helper'    => ' ',
            'damaged_area_opposite'            => 'Damaged Area Opposite',
            'damaged_area_opposite_helper'     => ' ',
            'recovery_office'                  => 'Recovery Office',
            'recovery_office_helper'           => ' ',
            'damage_costs'                     => 'Damage Costs',
            'damage_costs_helper'              => ' ',
            'recovery_costs'                   => 'Recovery Costs',
            'recovery_costs_helper'            => ' ',
            'replacement_vehicle_costs'        => 'Replacement Vehicle Costs',
            'replacement_vehicle_costs_helper' => ' ',
            'expert_costs'                     => 'Expert Costs',
            'expert_costs_helper'              => ' ',
            'other_costs'                      => 'Other Costs',
            'other_costs_helper'               => ' ',
            'deductible_excess_costs'          => 'Deductible Excess Costs',
            'deductible_excess_costs_helper'   => ' ',
            'insurance_costs'                  => 'Insurance Costs',
            'insurance_costs_helper'           => ' ',
            'expertise_office'                 => 'Expertise Office',
            'expertise_office_helper'          => ' ',
            'expert_report_is_in'              => 'Expert Report Is In',
            'expert_report_is_in_helper'       => ' ',
            'requested_at'                     => 'Requested At',
            'requested_at_helper'              => ' ',
            'report_received_at'               => 'Report Received At',
            'report_received_at_helper'        => ' ',
            'files'                            => 'Files',
            'files_helper'                     => ' ',
            'team'                             => 'Team',
            'team_helper'                      => ' ',
        ],
    ],
    'company' => [
        'title'          => 'Company',
        'title_singular' => 'Company',
        'fields'         => [
            'id'                  => 'ID',
            'id_helper'           => ' ',
            'name'                => 'Name',
            'name_helper'         => ' ',
            'company_type'        => 'Company Type',
            'company_type_helper' => ' ',
            'street'              => 'Street',
            'street_helper'       => ' ',
            'zipcode'             => 'Zipcode',
            'zipcode_helper'      => ' ',
            'city'                => 'City',
            'city_helper'         => ' ',
            'country'             => 'Country',
            'country_helper'      => ' ',
            'phone'               => 'Phone',
            'phone_helper'        => ' ',
            'active'              => 'Active',
            'active_helper'       => ' ',
            'created_at'          => 'Created at',
            'created_at_helper'   => ' ',
            'updated_at'          => 'Updated at',
            'updated_at_helper'   => ' ',
            'deleted_at'          => 'Deleted at',
            'deleted_at_helper'   => ' ',
            'description'         => 'Description',
            'description_helper'  => ' ',
            'team'                => 'Team',
            'team_helper'         => ' ',
        ],
    ],
    'task' => [
        'title'          => 'Tasks',
        'title_singular' => 'Task',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => ' ',
            'description'        => 'Description',
            'description_helper' => ' ',
            'created_at'         => 'Created at',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => ' ',
            'user'               => 'User',
            'user_helper'        => ' ',
            'task_number'        => 'Task Number',
            'task_number_helper' => ' ',
            'claim'              => 'Claim',
            'claim_helper'       => ' ',
            'team'               => 'Team',
            'team_helper'        => ' ',
        ],
    ],
    'contact' => [
        'title'          => 'Contact',
        'title_singular' => 'Contact',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'user'              => 'User',
            'user_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'first_name'        => 'First Name',
            'first_name_helper' => ' ',
            'last_name'         => 'Last Name',
            'last_name_helper'  => ' ',
            'email'             => 'Email',
            'email_helper'      => ' ',
            'company'           => 'Company',
            'company_helper'    => ' ',
            'team'              => 'Team',
            'team_helper'       => ' ',
        ],
    ],
    'injuryOffice' => [
        'title'          => 'Injury Office',
        'title_singular' => 'Injury Office',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'company'           => 'Company',
            'company_helper'    => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'identifier'        => 'Identifier',
            'identifier_helper' => ' ',
            'team'              => 'Team',
            'team_helper'       => ' ',
        ],
    ],
    'business' => [
        'title'          => 'Businesses',
        'title_singular' => 'Business',
    ],
    'expertiseOffice' => [
        'title'          => 'Expertise Office',
        'title_singular' => 'Expertise Office',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'company'           => 'Company',
            'company_helper'    => ' ',
            'identifier'        => 'Identifier',
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
        'title'          => 'Vehicle',
        'title_singular' => 'Vehicle',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Name',
            'name_helper'       => ' ',
            'plates'            => 'Plates',
            'plates_helper'     => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'company'           => 'Company',
            'company_helper'    => ' ',
            'team'              => 'Team',
            'team_helper'       => ' ',
        ],
    ],
    'driver' => [
        'title'          => 'Driver',
        'title_singular' => 'Driver',
        'fields'         => [
            'id'                      => 'ID',
            'id_helper'               => ' ',
            'first_name'              => 'First Name',
            'first_name_helper'       => ' ',
            'last_name'               => 'Last Name',
            'last_name_helper'        => ' ',
            'vehicle'                 => 'Vehicle',
            'vehicle_helper'          => ' ',
            'created_at'              => 'Created at',
            'created_at_helper'       => ' ',
            'updated_at'              => 'Updated at',
            'updated_at_helper'       => ' ',
            'deleted_at'              => 'Deleted at',
            'deleted_at_helper'       => ' ',
            'vehicle_opposite'        => 'Vehicle Opposite',
            'vehicle_opposite_helper' => ' ',
            'email'                   => 'Email',
            'email_helper'            => ' ',
            'phone'                   => 'Phone',
            'phone_helper'            => ' ',
            'team'                    => 'Team',
            'team_helper'             => ' ',
        ],
    ],
    'vehicleInformation' => [
        'title'          => 'Vehicle Information',
        'title_singular' => 'Vehicle Information',
    ],
    'vehicleOpposite' => [
        'title'          => 'Vehicle Opposite',
        'title_singular' => 'Vehicle Opposite',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Name',
            'name_helper'       => ' ',
            'plates'            => 'Plates',
            'plates_helper'     => ' ',
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
    'recoveryOffice' => [
        'title'          => 'Recovery Office',
        'title_singular' => 'Recovery Office',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'company'           => 'Company',
            'company_helper'    => ' ',
            'identifier'        => 'Identifier',
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
            'name'              => 'Name',
            'name_helper'       => ' ',
            'owner'             => 'Owner',
            'owner_helper'      => ' ',
        ],
    ],

];
