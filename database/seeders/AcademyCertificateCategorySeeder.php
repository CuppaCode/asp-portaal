<?php

namespace Database\Seeders;

use App\Models\CertificateCategory;
use Illuminate\Database\Seeder;

class AcademyCertificateCategorySeeder extends Seeder
{
    /**
     * Document types of the Academy driver card.
     *
     * signal_1_months / signal_2_days: signals before the expiry date (stap 7 "Bewaken").
     */
    public const CATEGORIES = [
        ['name' => 'Rijbewijs', 'code' => 'rijbewijs', 'allow_not_applicable' => false, 'signal_1_months' => 6, 'signal_2_days' => 90],
        ['name' => 'Code 95', 'code' => 'code_95', 'allow_not_applicable' => false, 'signal_1_months' => 12, 'signal_2_days' => 90, 'signal_1_only_if_hours_open' => true],
        ['name' => 'Chauffeurspas', 'code' => 'chauffeurspas', 'allow_not_applicable' => false, 'signal_1_months' => 3, 'signal_2_days' => 42],
        ['name' => 'REVI', 'code' => 'revi', 'signal_1_months' => 6, 'signal_2_days' => 90],
        ['name' => 'VCA', 'code' => 'vca', 'signal_1_months' => 6, 'signal_2_days' => 90],
        ['name' => 'EV NEN 9140', 'code' => 'ev_nen_9140', 'signal_1_months' => 6, 'signal_2_days' => 90],
        ['name' => 'EHBO', 'code' => 'ehbo', 'signal_1_months' => 3, 'signal_2_days' => 42],
        ['name' => 'BHV', 'code' => 'bhv', 'signal_1_months' => 3, 'signal_2_days' => 42],
        ['name' => 'Heftruck', 'code' => 'heftruck', 'signal_1_months' => 6, 'signal_2_days' => 90],
        ['name' => 'Lichte berging', 'code' => 'lichte_berging', 'one_off' => true],
        ['name' => 'Zware berging', 'code' => 'zware_berging', 'one_off' => true],
    ];

    public function run()
    {
        foreach (self::CATEGORIES as $index => $definition) {
            $category = CertificateCategory::withTrashed()->where('code', $definition['code'])->first()
                ?? CertificateCategory::withTrashed()->where('name', $definition['name'])->first()
                ?? new CertificateCategory();

            if ($category->trashed()) {
                $category->restore();
            }

            $category->fill(array_merge([
                'allow_not_applicable' => true,
                'one_off' => false,
                'signal_1_months' => null,
                'signal_2_days' => null,
                'signal_1_only_if_hours_open' => false,
            ], $definition, [
                'academy' => true,
                'sort_order' => ($index + 1) * 10,
                // Academy signals are sent manually (M11); the automatic driver reminders would duplicate them.
                'enable_notifications' => false,
            ]));

            $category->save();
        }
    }
}
