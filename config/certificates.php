<?php

use App\Certificates\Modules\Code95CertificateCategoryModule;
use App\Certificates\Modules\DefaultCertificateCategoryModule;

return [
    'near_expiry_days' => 90,

    'module_fallback' => DefaultCertificateCategoryModule::class,

    // Map category code => module class
    'modules' => [
        'default' => DefaultCertificateCategoryModule::class,
        'code_95' => Code95CertificateCategoryModule::class,
    ],
];