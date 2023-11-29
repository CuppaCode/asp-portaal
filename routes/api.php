<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    
    
});


//Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

//});

// Claim API call, to be accessible via JS AKA AJAX calls
Route::post('claims/update-status', 'Admin\ClaimController@quickUpdateStatus');
Route::post('companies/quick-store', 'Admin\CompanyController@quickStore');

Route::post('comments/quick-store', 'Admin\CommentController@quickStore');

