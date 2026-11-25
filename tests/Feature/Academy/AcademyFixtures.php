<?php

namespace Tests\Feature\Academy;

use App\Models\CertificateCategory;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait AcademyFixtures
{
    protected function makeUser(string $name = 'Test Gebruiker'): User
    {
        // The User model attaches the default registration role when a user is created.
        $roleId = config('panel.registration_default_role');
        if ($roleId && !DB::table('roles')->where('id', $roleId)->exists()) {
            DB::table('roles')->insert(['id' => $roleId, 'title' => 'Test rol', 'created_at' => now(), 'updated_at' => now()]);
        }

        return User::create([
            'name' => $name,
            'email' => uniqid('academy-test-') . '@example.com',
            'password' => 'secret-password',
        ]);
    }

    protected function makeCompany(array $attributes = []): Company
    {
        return Company::create(array_merge([
            'name' => 'Academy Test BV',
            'street' => 'Teststraat 1',
            'zipcode' => '1234 AB',
            'city' => 'Teststad',
            'country' => 'NL',
            'phone' => '0123456789',
            'academy_enabled' => true,
            'academy_started_at' => '2026-10-02',
        ], $attributes));
    }

    protected function makeDriver(Company $company, array $contact = [], array $driver = []): Driver
    {
        $contactModel = Contact::create(array_merge([
            'first_name' => 'Jan',
            'last_name' => 'Jansen',
            'email' => '',
            'company_id' => $company->id,
            'is_driver' => true,
            'create_user' => 0,
        ], $contact));

        return Driver::create(array_merge([
            'contact_id' => $contactModel->id,
            'company_id' => $company->id,
        ], $driver));
    }

    protected function category(string $code): CertificateCategory
    {
        return CertificateCategory::where('code', $code)->firstOrFail();
    }
}
