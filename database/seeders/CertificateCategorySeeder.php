<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CertificateCategory;

class CertificateCategorySeeder extends Seeder
{
    public function run()
    {
        $defaults = [
            ['name' => 'Rijbewijs', 'duration' => 60],
            ['name' => 'Tachograaf', 'duration' => 24],
            ['name' => 'EHBO', 'duration' => 36],
            ['name' => 'Vrachtwagen certificaat', 'duration' => 48],
            ['name' => 'Ladingzekering', 'duration' => 12],
        ];

        foreach ($defaults as $row) {
            CertificateCategory::updateOrCreate(['name' => $row['name']], $row);
        }
    }
}
