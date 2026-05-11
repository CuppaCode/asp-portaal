<?php

/**
 * File Upload Security Configuration
 * 
 * This configuration file contains all security settings for file uploads
 * across the application, particularly for claim form file uploads.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum size for individual files in MB.
    | Default: 10MB
    |
    */
    'max_file_size_mb' => env('FILE_UPLOAD_MAX_SIZE_MB', 10),

    /*
    |--------------------------------------------------------------------------
    | Maximum Files Per Collection
    |--------------------------------------------------------------------------
    |
    | Maximum number of files allowed per collection/field.
    | Default: 10 files per collection
    |
    */
    'max_files_per_collection' => env('FILE_UPLOAD_MAX_FILES', 10),

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    |
    | List of MIME types allowed for different file collection categories.
    |
    */
    'allowed_mime_types' => [
        'images' => [
            'image/jpeg',
            'image/png',
            'image/gif',
        ],
        'documents' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed File Extensions
    |--------------------------------------------------------------------------
    |
    | Whitelist of file extensions allowed for uploads (as additional check).
    | Uses lowercase comparison.
    |
    */
    'allowed_extensions' => [
        'jpg', 'jpeg', 'png', 'gif',        // Images
        'pdf',                               // Documents
        'doc', 'docx',                       // Microsoft Word
        'xls', 'xlsx',                       // Microsoft Excel
    ],

    /*
    |--------------------------------------------------------------------------
    | Scan for Viruses
    |--------------------------------------------------------------------------
    |
    | Enable virus scanning for uploaded files using ClamAV.
    | Requires clamav to be installed on the server.
    | Set to false to disable.
    |
    */
    'scan_for_viruses' => env('FILE_UPLOAD_VIRUS_SCAN', false),

    /*
    |--------------------------------------------------------------------------
    | Claim Form File Collections
    |--------------------------------------------------------------------------
    |
    | Configuration for each file collection in the claim form.
    |
    */
    'claim_form_collections' => [
        'damage_files' => [
            'label' => 'Schadefoto\'s',
            'allowed_types' => ['images'],
        ],
        'report_files' => [
            'label' => 'Rapportages',
            'allowed_types' => ['images', 'documents'],
        ],
        'financial_files' => [
            'label' => 'Financiële documenten',
            'allowed_types' => ['documents'],
        ],
        'other_files' => [
            'label' => 'Overige documenten',
            'allowed_types' => ['documents'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for where files are stored and how they're accessed.
    |
    */
    'storage' => [
        'disk' => env('FILE_UPLOAD_DISK', 'local'),
        'path' => env('FILE_UPLOAD_PATH', 'uploads'),
        'visibility' => 'private', // Ensure files are not publicly accessible
    ],

];
