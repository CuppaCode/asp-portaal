<?php

use Illuminate\Http\Request;

Route::group([
    'prefix' => 'v1',
    'as' => 'api.',
    'namespace' => 'Admin',
    'middleware' => ['auth:sanctum']
], function () {
    
    
});



