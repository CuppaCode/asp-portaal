<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CertificateTestSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Forklift', 'duration' => 12],
            ['name' => 'Vrachtwagen', 'duration' => 24],
            ['name' => 'EHBO', 'duration' => 36],
            ['name' => 'BHV', 'duration' => 12],
            ['name' => 'CODE90', 'duration' => 24],
            ['name' => 'CODE95', 'duration' => 24],
        ];

        $driverIds = DB::table('drivers')->pluck('id')->toArray();
        if (empty($driverIds)) {
            // nothing we can attach to — stop
            $this->command->info('No drivers found in DB — skipping certificate creation.');
            return;
        }

        foreach ($categories as $cat) {
            $catId = DB::table('certificate_categories')->insertGetId([
                'name' => $cat['name'],
                'duration' => $cat['duration'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // create a number of certificates for this category
            for ($i = 0; $i < 8; $i++) {
                // pick a random driver
                $driverId = $driverIds[array_rand($driverIds)];

                // vary expiry: some expired, some within 30 days, some later
                $type = $i % 3;
                if ($type == 0) {
                    $expiry = Carbon::now()->subDays(rand(1, 60)); // expired
                } elseif ($type == 1) {
                    $expiry = Carbon::now()->addDays(rand(1, 30)); // within 30 days
                } else {
                    $expiry = Carbon::now()->addDays(rand(60, 400)); // later
                }

                DB::table('certificate')->insert([
                    'driver_id' => $driverId,
                    'category_id' => $catId,
                    'name' => $cat['name'] . ' certificaat ' . ($i + 1),
                    'notify_date' => $expiry->copy()->subDays(30)->toDateString(),
                    'expiry_date' => $expiry->toDateString(),
                    'team_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        $this->command->info('CertificateTestSeeder: categories and sample certificates created.');
    }
}
