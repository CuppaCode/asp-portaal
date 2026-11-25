<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The hours entered on a Code 95 card are now the opening balance (Mijn CBR) and the registered
     * trainings are added to it. Before, the stored total already contained the trainings, so subtract
     * them to prevent counting the same hours twice.
     */
    public function up(): void
    {
        $categoryIds = DB::table('certificate_categories')->where('code', 'code_95')->pluck('id');

        $certificates = DB::table('certificate')->whereIn('category_id', $categoryIds)->pluck('id');

        foreach ($certificates as $certificateId) {
            $courses = DB::table('code95_courses')->where('certificate_id', $certificateId);

            $total = (int) (clone $courses)->sum('hours');
            $practice = (int) (clone $courses)->where('type', 'practice')->sum('hours');

            if ($total === 0) {
                continue;
            }

            $this->subtract($certificateId, 'code95_hours_completed', $total);
            $this->subtract($certificateId, 'code95_hours_practice_completed', $practice);
        }
    }

    public function down(): void
    {
        // The previous totals cannot be restored exactly; they are recalculated from the trainings.
    }

    private function subtract(int $certificateId, string $fieldKey, int $amount): void
    {
        $meta = DB::table('certificate_meta')
            ->where('certificate_id', $certificateId)
            ->where('field_key', $fieldKey)
            ->first();

        if (!$meta) {
            return;
        }

        DB::table('certificate_meta')->where('id', $meta->id)->update([
            'value_number' => max(0, (float) $meta->value_number - $amount),
            'updated_at' => now(),
        ]);
    }
};
