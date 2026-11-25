<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PERMISSIONS = [
        107 => 'academy_access',
        108 => 'academy_manage',
    ];

    public function up(): void
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            // Academy code (M1, M2b, ...). Plain index: soft deleted rows may share a code.
            $table->string('code', 20)->nullable()->after('name')->index();
            $table->string('recipient_rule', 30)->nullable()->after('trigger_type');
            $table->string('cc_rule', 30)->nullable()->after('recipient_rule');
        });

        Schema::table('mailings', function (Blueprint $table) {
            $table->nullableMorphs('context');
        });

        $this->addPermissions();
    }

    public function down(): void
    {
        Schema::table('mailings', function (Blueprint $table) {
            $table->dropMorphs('context');
        });

        Schema::table('mail_templates', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropColumn(['code', 'recipient_rule', 'cc_rule']);
        });

        $ids = DB::table('permissions')->whereIn('title', self::PERMISSIONS)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();
    }

    /**
     * Existing installations: add the permissions and give them to the admin role.
     * Fresh installations get them from PermissionsTableSeeder.
     */
    private function addPermissions(): void
    {
        if (DB::table('permissions')->count() === 0) {
            return;
        }

        foreach (self::PERMISSIONS as $id => $title) {
            if (DB::table('permissions')->where('title', $title)->exists()) {
                continue;
            }

            $row = ['title' => $title, 'created_at' => now(), 'updated_at' => now()];
            if (!DB::table('permissions')->where('id', $id)->exists()) {
                $row['id'] = $id;
            }

            DB::table('permissions')->insert($row);
        }

        $permissionIds = DB::table('permissions')->whereIn('title', self::PERMISSIONS)->pluck('id');

        foreach ($permissionIds as $permissionId) {
            $exists = DB::table('permission_role')
                ->where('role_id', 1)
                ->where('permission_id', $permissionId)
                ->exists();

            if (!$exists && DB::table('roles')->where('id', 1)->exists()) {
                DB::table('permission_role')->insert(['role_id' => 1, 'permission_id' => $permissionId]);
            }
        }
    }
};
