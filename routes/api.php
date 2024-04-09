<?php

use Illuminate\Http\Request;

Route::group([
    'prefix' => 'v1',
    'as' => 'api.',
    'namespace' => 'Admin',
    'middleware' => ['auth:sanctum']
], function () {
    
    Route::post('users/get-user-name', 'UsersController@getUserName');
    
});

// Route::post('tokens/create', function (Request $request) {

//     $token = $request->user()->createToken($request->token_name);
 
//     return ['token' => $token->plainTextToken];
// })->middleware('auth');

// Claim API call, to be accessible via JS AKA AJAX calls
Route::post('claims/update-status', 'Admin\ClaimController@quickUpdateStatus');
Route::post('tasks/update-status', 'Admin\TaskController@quickUpdateStatus');

Route::post('companies/quick-store', 'Admin\CompanyController@quickStore');
Route::post('comments/quick-store', 'Admin\CommentController@quickStore');

Route::post('users/get-user-name', 'Admin\UsersController@getUserName');

Route::post('analytics/get-data', 'Admin\AnalyticsController@getData');

