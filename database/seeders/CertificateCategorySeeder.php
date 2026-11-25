<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CertificateCategory;

class CertificateCategorySeeder extends Seeder
{
    public function run()
    {
        $defaults = [
            ['name' => 'Rijbewijs', 'code' => 'rijbewijs', 'duration' => 60],
            ['name' => 'Tachograaf', 'code' => 'tachograaf', 'duration' => 24],
            ['name' => 'EHBO', 'code' => 'ehbo', 'duration' => 36],
            ['name' => 'Vrachtwagen certificaat', 'code' => 'vrachtwagen_certificaat', 'duration' => 48],
            ['name' => 'Ladingzekering', 'code' => 'ladingzekering', 'duration' => 12],
            ['name' => 'Code 95', 'code' => 'code_95', 'duration' => 60],
        ];

        foreach ($defaults as $row) {
            CertificateCategory::updateOrCreate(['name' => $row['name']], $row);
        }
    }
}
