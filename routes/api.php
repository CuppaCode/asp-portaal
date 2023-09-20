<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Claim API call, to be accessible via JS AKA AJAX calls
    Route::post('claims/{id}/update-status', 'Admin\ClaimController@quickUpdateStatus');

});


//Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

//});

