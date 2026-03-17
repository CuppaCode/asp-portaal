<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->date('bevestiging_kl_at')->nullable()->after('verwijtbaar');
            $table->date('saf_binnen_at')->nullable()->after('bevestiging_kl_at');
            $table->date('info_chf_at')->nullable()->after('saf_binnen_at');
            $table->date('info_kl_wp_at')->nullable()->after('info_chf_at');
            $table->date('beoordeling_at')->nullable()->after('info_kl_wp_at');
            $table->date('schadebedrag_bekend_at')->nullable()->after('beoordeling_at');
            $table->date('naar_vzk_at')->nullable()->after('schadebedrag_bekend_at');
            $table->date('naar_shb_gge_at')->nullable()->after('naar_vzk_at');
            $table->date('goedkeuring_og_at')->nullable()->after('naar_shb_gge_at');
            $table->date('factuur_ontvangen_at')->nullable()->after('goedkeuring_og_at');
            $table->date('factuur_adm_at')->nullable()->after('factuur_ontvangen_at');
            $table->date('brief_chf_at')->nullable()->after('factuur_adm_at');
            $table->date('dossier_controle_at')->nullable()->after('brief_chf_at');
            $table->date('dossier_heropend_at')->nullable()->after('dossier_controle_at');
            $table->json('dossier_nvt')->nullable()->after('dossier_heropend_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn([
                'bevestiging_kl_at', 'saf_binnen_at', 'info_chf_at', 'info_kl_wp_at',
                'beoordeling_at', 'schadebedrag_bekend_at', 'naar_vzk_at', 'naar_shb_gge_at',
                'goedkeuring_og_at', 'factuur_ontvangen_at', 'factuur_adm_at', 'brief_chf_at',
                'dossier_controle_at', 'dossier_heropend_at', 'dossier_nvt',
            ]);
        });
    }
};
