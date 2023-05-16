<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'claim_create',
            ],
            [
                'id'    => 18,
                'title' => 'claim_edit',
            ],
            [
                'id'    => 19,
                'title' => 'claim_show',
            ],
            [
                'id'    => 20,
                'title' => 'claim_delete',
            ],
            [
                'id'    => 21,
                'title' => 'claim_access',
            ],
            [
                'id'    => 22,
                'title' => 'company_create',
            ],
            [
                'id'    => 23,
                'title' => 'company_edit',
            ],
            [
                'id'    => 24,
                'title' => 'company_show',
            ],
            [
                'id'    => 25,
                'title' => 'company_delete',
            ],
            [
                'id'    => 26,
                'title' => 'company_access',
            ],
            [
                'id'    => 27,
                'title' => 'task_create',
            ],
            [
                'id'    => 28,
                'title' => 'task_edit',
            ],
            [
                'id'    => 29,
                'title' => 'task_show',
            ],
            [
                'id'    => 30,
                'title' => 'task_delete',
            ],
            [
                'id'    => 31,
                'title' => 'task_access',
            ],
            [
                'id'    => 32,
                'title' => 'contact_create',
            ],
            [
                'id'    => 33,
                'title' => 'contact_edit',
            ],
            [
                'id'    => 34,
                'title' => 'contact_show',
            ],
            [
                'id'    => 35,
                'title' => 'contact_delete',
            ],
            [
                'id'    => 36,
                'title' => 'contact_access',
            ],
            [
                'id'    => 37,
                'title' => 'injury_office_create',
            ],
            [
                'id'    => 38,
                'title' => 'injury_office_edit',
            ],
            [
                'id'    => 39,
                'title' => 'injury_office_delete',
            ],
            [
                'id'    => 40,
                'title' => 'injury_office_access',
            ],
            [
                'id'    => 41,
                'title' => 'business_access',
            ],
            [
                'id'    => 42,
                'title' => 'expertise_office_create',
            ],
            [
                'id'    => 43,
                'title' => 'expertise_office_edit',
            ],
            [
                'id'    => 44,
                'title' => 'expertise_office_delete',
            ],
            [
                'id'    => 45,
                'title' => 'expertise_office_access',
            ],
            [
                'id'    => 46,
                'title' => 'vehicle_create',
            ],
            [
                'id'    => 47,
                'title' => 'vehicle_edit',
            ],
            [
                'id'    => 48,
                'title' => 'vehicle_show',
            ],
            [
                'id'    => 49,
                'title' => 'vehicle_delete',
            ],
            [
                'id'    => 50,
                'title' => 'vehicle_access',
            ],
            [
                'id'    => 51,
                'title' => 'driver_create',
            ],
            [
                'id'    => 52,
                'title' => 'driver_edit',
            ],
            [
                'id'    => 53,
                'title' => 'driver_show',
            ],
            [
                'id'    => 54,
                'title' => 'driver_delete',
            ],
            [
                'id'    => 55,
                'title' => 'driver_access',
            ],
            [
                'id'    => 56,
                'title' => 'vehicle_information_access',
            ],
            [
                'id'    => 57,
                'title' => 'vehicle_opposite_create',
            ],
            [
                'id'    => 58,
                'title' => 'vehicle_opposite_edit',
            ],
            [
                'id'    => 59,
                'title' => 'vehicle_opposite_show',
            ],
            [
                'id'    => 60,
                'title' => 'vehicle_opposite_delete',
            ],
            [
                'id'    => 61,
                'title' => 'vehicle_opposite_access',
            ],
            [
                'id'    => 62,
                'title' => 'recovery_office_create',
            ],
            [
                'id'    => 63,
                'title' => 'recovery_office_edit',
            ],
            [
                'id'    => 64,
                'title' => 'recovery_office_delete',
            ],
            [
                'id'    => 65,
                'title' => 'recovery_office_access',
            ],
            [
                'id'    => 66,
                'title' => 'team_create',
            ],
            [
                'id'    => 67,
                'title' => 'team_edit',
            ],
            [
                'id'    => 68,
                'title' => 'team_show',
            ],
            [
                'id'    => 69,
                'title' => 'team_delete',
            ],
            [
                'id'    => 70,
                'title' => 'team_access',
            ],
            [
                'id'    => 71,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
