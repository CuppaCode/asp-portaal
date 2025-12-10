<?php

use Illuminate\Http\Request;
use App\Http\Controllers\TaskController;

Route::group([
    'prefix' => 'v1',
    'as' => 'api.',
    'namespace' => 'Admin',
    'middleware' => ['auth:sanctum']
], function () {
    Route::post('/task/{task}', [TaskController::class, 'updateInline'])->middleware('auth:sanctum');
    
    
});



