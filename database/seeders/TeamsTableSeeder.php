<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{
    public function run()
    {
        $teams = [
            [
                'id' => 1,
                'name' => 'Default Team',
                'owner_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Team::insert($teams);
    }
}
