<?php

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes();

// Public certificate renewal routes (no auth required)
Route::get('/certificaat/verlengen/{token}', 'CertificateRenewalController@showRenewalForm')->name('certificate.renew.form');
Route::post('/certificaat/verlengen/{token}', 'CertificateRenewalController@processRenewal')->name('certificate.renew.process');
// Public Claim Form Routes (no authentication required)
Route::get('/claim-form/{token}', 'PublicClaimFormController@show')->name('public.claim-form.show');
Route::post('/claim-form/{token}', 'PublicClaimFormController@store')->name('public.claim-form.store');

// Draft Claim Actions (signed URLs for email links - no auth)
Route::get('/claim-draft/{claim}/approve', 'PublicDraftClaimController@approve')
    ->name('draft-claim.approve')
    ->middleware('signed');
Route::get('/claim-draft/{claim}/deny-form', 'PublicDraftClaimController@showDenyForm')
    ->name('draft-claim.deny-form')
    ->middleware('signed');
Route::post('/claim-draft/{claim}/deny', 'PublicDraftClaimController@deny')
    ->name('draft-claim.deny');

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
    Route::get('conceptclaims', 'ClaimController@concept')->name('claims.concept');
    Route::get('unassignedclaims', 'ClaimController@unassigned')->name('claims.unassigned');
    Route::get('closedclaims', 'ClaimController@closed')->name('claims.closed');

    // Company
    Route::delete('companies/destroy', 'CompanyController@massDestroy')->name('companies.massDestroy');
    Route::post('companies/media', 'CompanyController@storeMedia')->name('companies.storeMedia');
    Route::post('companies/ckmedia', 'CompanyController@storeCKEditorImages')->name('companies.storeCKEditorImages');
    Route::resource('companies', 'CompanyController');

    // Company Claim Forms
    Route::get('companies/{company}/claim-form', 'CompanyClaimFormController@index')->name('company-claim-forms.index');
    Route::post('companies/{company}/claim-form/config', 'CompanyClaimFormController@updateConfig')->name('company-claim-forms.update-config');
    Route::patch('companies/{company}/claim-form/standard-field/{fieldName}', 'CompanyClaimFormController@updateStandardField')->name('company-claim-forms.update-standard-field');
    Route::post('companies/{company}/claim-form/expiry', 'CompanyClaimFormController@updateExpirySettings')->name('company-claim-forms.update-expiry');
    Route::post('companies/{company}/claim-form/token', 'CompanyClaimFormController@createToken')->name('company-claim-forms.create-token');
    Route::patch('companies/{company}/claim-form/token/{token}', 'CompanyClaimFormController@toggleToken')->name('company-claim-forms.toggle-token');
    Route::delete('companies/{company}/claim-form/token/{token}', 'CompanyClaimFormController@deleteToken')->name('company-claim-forms.delete-token');
    Route::post('companies/{company}/claim-form/notification', 'CompanyClaimFormController@storeNotification')->name('company-claim-forms.store-notification');
    Route::delete('companies/{company}/claim-form/notification/{notification}', 'CompanyClaimFormController@deleteNotification')->name('company-claim-forms.delete-notification');
    Route::post('companies/{company}/claim-form/custom-field', 'CompanyClaimFormController@storeCustomField')->name('company-claim-forms.store-custom-field');
    Route::patch('companies/{company}/claim-form/custom-field/{customField}', 'CompanyClaimFormController@updateCustomField')->name('company-claim-forms.update-custom-field');
    Route::delete('companies/{company}/claim-form/custom-field/{customField}', 'CompanyClaimFormController@deleteCustomField')->name('company-claim-forms.delete-custom-field');
    Route::post('companies/{company}/claim-form/copy', 'CompanyClaimFormController@copyFromCompany')->name('company-claim-forms.copy-from-company');
    Route::post('companies/{company}/claim-form/bulk-update', 'CompanyClaimFormController@bulkUpdate')->name('company-claim-forms.bulk-update');

    // Draft Claims
    Route::post('claims/{claim}/approve', 'DraftClaimController@approve')->name('claims.approve');
    Route::post('claims/{claim}/deny', 'DraftClaimController@deny')->name('claims.deny');
    Route::post('claims/{claim}/resubmit', 'DraftClaimController@resubmit')->name('claims.resubmit');

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
    Route::get('certificate/create/{driver}', 'CertificateController@create')->name('certificate.create');
    Route::post('certificate/{driver}', 'CertificateController@store')->name('certificate.store');
    Route::post('certificate/{certificate}/renew', 'CertificateController@renew')->name('certificate.renew');
    Route::post('certificate/bulk-renew', 'CertificateController@bulkRenew')->name('certificate.bulk-renew');
    Route::resource('certificate', 'CertificateController')->except(['create', 'store']);
    // Certificate categories
    Route::get('certificate-categories/search', 'CertificateCategoryController@search')->name('certificate-categories.search');
    Route::post('certificate-categories/quick-store', 'CertificateCategoryController@quickStore')->name('certificate-categories.quickStore');
    Route::resource('certificate-categories', 'CertificateCategoryController');

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
    Route::delete('/comments/{comment}', 'CommentController@destroy')->name('comments.delete');

    // Mail Templates
    Route::get('mail-templates/triggers', 'MailTemplateController@triggers')->name('mail-templates.triggers');
    Route::delete('mail-templates/destroy', 'MailTemplateController@massDestroy')->name('mail-templates.massDestroy');
    Route::resource('mail-templates', 'MailTemplateController');

    // Mailings
    Route::delete('mailings/destroy', 'MailingController@massDestroy')->name('mailings.massDestroy');
    Route::post('mailings/{mailing}/send', 'MailingController@send')->name('mailings.send');
    Route::post('mailings/send-batch', 'MailingController@sendBatch')->name('mailings.sendBatch');
    Route::resource('mailings', 'MailingController');

    // SLA
    Route::resource('sla', 'SLAController');

    // SuperAdmin
    Route::get('super-admin', 'SuperAdminController@index')->name('super-admin');
    Route::post('super-admin/migrate-status', 'SuperAdminController@migrateStatus')->name('super-admin.migrate-status');
    Route::post('super-admin/migrate-opposite-type', 'SuperAdminController@migrateOppositeType')->name('super-admin.migrate-opposite-type');
    Route::post('super-admin/migrate-damaged-part', 'SuperAdminController@migrateDamagedPart')->name('super-admin.migrate-damaged-part');
    Route::post('super-admin/migrate-damaged-part-opposite', 'SuperAdminController@migrateDamagedPartOpposite')->name('super-admin.migrate-damaged-part-opposite');
    Route::post('super-admin/migrate-damage-origin', 'SuperAdminController@migrateDamageOrigin')->name('super-admin.migrate-damage-origin');
    Route::post('super-admin/migrate-damage-origin-opposite', 'SuperAdminController@migrateDamageOriginOpposite')->name('super-admin.migrate-damage-origin-opposite');
    Route::post('super-admin/migrate-damaged-area', 'SuperAdminController@migrateDamagedArea')->name('super-admin.migrate-damaged-area');
    Route::post('super-admin/migrate-damaged-area-opposite', 'SuperAdminController@migrateDamagedAreaOpposite')->name('super-admin.migrate-damaged-area-opposite');

    // Mail examples
    Route::get('preview-notification', function () {
        $markdown = new \Illuminate\Mail\Markdown(view(), config('mail.markdown'));   
        $data = 'Beste BezorgenZonderZorgen,

        Op 05-07-2023 gebeurde er iets.
        
        Bednakt voor het wachten voor Schade met Hek.
        
        Test123
        
        Groet,
        Tester';
        return $markdown->render("emails.plain-email", ['body' => $data]);
    });

    // Ajax routes
    Route::post('users/get-user-name', 'UsersController@getUserName');

    Route::post('claims/update-status', 'ClaimController@quickUpdateStatus');
    Route::post('tasks/update-status', 'TaskController@quickUpdateStatus');
    Route::post('claims/decline-claim', 'ClaimController@declineClaim');

    Route::post('companies/quick-store', 'CompanyController@quickStore');
    Route::post('comments/quick-store', 'CommentController@quickStore');
    Route::post('drivers/quick-store', 'DriverController@quickStore');
    
    Route::post('analytics/get-data', 'AnalyticsController@getData');

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
