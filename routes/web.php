<?php

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes();

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

    Route::get('/', 'HomeController@index')->name('home');

    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Claim
    Route::delete('claims/destroy', 'ClaimController@massDestroy')->name('claims.massDestroy');
    Route::post('claims/media', 'ClaimController@storeMedia')->name('claims.storeMedia');
    Route::post('claims/ckmedia', 'ClaimController@storeCKEditorImages')->name('claims.storeCKEditorImages');
    Route::resource('claims', 'ClaimController');
    Route::post('claims/send-mail', 'ClaimController@sendMail')->name('claims.sendMail');

    Route::get('openclaims', 'ClaimController@open')->name('claims.open');
    Route::get('closedclaims', 'ClaimController@closed')->name('claims.closed');

    // Company
    Route::delete('companies/destroy', 'CompanyController@massDestroy')->name('companies.massDestroy');
    Route::post('companies/media', 'CompanyController@storeMedia')->name('companies.storeMedia');
    Route::post('companies/ckmedia', 'CompanyController@storeCKEditorImages')->name('companies.storeCKEditorImages');
    Route::resource('companies', 'CompanyController');

    // Task
    Route::delete('tasks/destroy', 'TaskController@massDestroy')->name('tasks.massDestroy');
    Route::post('tasks/media', 'TaskController@storeMedia')->name('tasks.storeMedia');
    Route::post('tasks/ckmedia', 'TaskController@storeCKEditorImages')->name('tasks.storeCKEditorImages');
    Route::resource('tasks', 'TaskController');

    // Contact
    Route::delete('contacts/destroy', 'ContactController@massDestroy')->name('contacts.massDestroy');
    Route::resource('contacts', 'ContactController');

    // Injury Office
    Route::delete('injury-offices/destroy', 'InjuryOfficeController@massDestroy')->name('injury-offices.massDestroy');
    Route::resource('injury-offices', 'InjuryOfficeController', ['except' => ['show']]);

    // Expertise Office
    Route::delete('expertise-offices/destroy', 'ExpertiseOfficeController@massDestroy')->name('expertise-offices.massDestroy');
    Route::resource('expertise-offices', 'ExpertiseOfficeController', ['except' => ['show']]);

    // Vehicle
    Route::delete('vehicles/destroy', 'VehicleController@massDestroy')->name('vehicles.massDestroy');
    Route::resource('vehicles', 'VehicleController');

    // Driver
    Route::delete('drivers/destroy', 'DriverController@massDestroy')->name('drivers.massDestroy');
    Route::resource('drivers', 'DriverController');

    // Vehicle Opposite
    Route::delete('vehicle-opposites/destroy', 'VehicleOppositeController@massDestroy')->name('vehicle-opposites.massDestroy');
    Route::resource('vehicle-opposites', 'VehicleOppositeController');

    // Recovery Office
    Route::delete('recovery-offices/destroy', 'RecoveryOfficeController@massDestroy')->name('recovery-offices.massDestroy');
    Route::resource('recovery-offices', 'RecoveryOfficeController', ['except' => ['show']]);

    // Team
    Route::delete('teams/destroy', 'TeamController@massDestroy')->name('teams.massDestroy');
    Route::resource('teams', 'TeamController');

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Note
    Route::delete('notes/destroy', 'NoteController@massDestroy')->name('notes.massDestroy');
    Route::post('notes/media', 'NoteController@storeMedia')->name('notes.storeMedia');
    Route::post('notes/ckmedia', 'NoteController@storeCKEditorImages')->name('notes.storeCKEditorImages');
    Route::resource('notes', 'NoteController');

    Route::get('team-members', 'TeamMembersController@index')->name('team-members.index');
    Route::post('team-members', 'TeamMembersController@invite')->name('team-members.invite');

    Route::get('analytics', 'AnalyticsController@index')->name('analytics');
    Route::get('invoices', 'AnalyticsController@invoices')->name('invoices');

    // Comment
    Route::delete('comments/destroy', 'CommentController@massDestroy')->name('comments.massDestroy');
    Route::resource('comments', 'CommentController');

    // Mail Templates
    Route::delete('mail-templates/destroy', 'MailTemplateController@massDestroy')->name('mail-templates.massDestroy');
    Route::resource('mail-templates', 'MailTemplateController');

    // SLA
    Route::resource('sla', 'SLAController');

    // Mail examples
    Route::get('preview-notification', function () {
        $markdown = new \Illuminate\Mail\Markdown(view(), config('mail.markdown'));   
        $data = 'Beste BezorgenZonderZorgen,

        Op 05-07-2023 gebeurde er iets.
        
        Bednakt voor het wachten voor Schade met Hek.
        
        Test123
        
        Groet,
        Jemoeder';
        return $markdown->render("emails.plain-email", ['body' => $data]);
    }); 

});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
