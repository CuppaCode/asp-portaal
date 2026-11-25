<?php

return [
    'certificateCategory' => [
        'title' => 'Certificate types',
        'title_singular' => 'Certificate type',
        'fields' => [
            'id' => 'ID',
            'id_helper' => ' ',
            'name' => 'Name',
            'name_helper' => ' ',
            'created_at' => 'Created at',
            'created_at_helper' => ' ',
            'updated_at' => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at' => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'dashboard' => [
        'certificate_category_expiring' => ':name certificates expiring within 30 days',
    ],
];
