<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MailTemplate;

class CertificateMailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Template 1: Certificate Expiring
        MailTemplate::create([
            'name' => 'Certificaat verloopt binnenkort',
            'subject' => 'Uw certificaat [certificaat_naam] verloopt binnenkort',
            'body' => '<h2>Certificaat Verlenging Vereist</h2>

<p>Beste [chauffeur_naam],</p>

<p>Het certificaat <strong>[certificaat_naam]</strong> (categorie: [certificaat_categorie]) verloopt over <strong>[dagen_tot_verloop] dagen</strong> op <strong>[certificaat_vervaldatum]</strong>.</p>

<p>Gelieve dit certificaat tijdig te verlengen om problemen te voorkomen.</p>

<div style="margin: 30px 0; text-align: center;">
    <a href="[verlenging_link]" style="background-color: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Certificaat Nu Verlengen
    </a>
</div>

<p><strong>Certificaat Details:</strong></p>
<ul>
    <li>Naam: [certificaat_naam]</li>
    <li>Categorie: [certificaat_categorie]</li>
    <li>Vervaldatum: [certificaat_vervaldatum]</li>
    <li>Chauffeur: [chauffeur_naam]</li>
    <li>Bedrijf: [bedrijf]</li>
</ul>

<p><small>Deze link is 30 dagen geldig. U ontvangt automatisch herinneringen totdat het certificaat is verlengd.</small></p>

<p>Met vriendelijke groet,<br>[bedrijf]</p>',
            'trigger_type' => 'CERTIFICATE_EXPIRING',
            'is_active' => true,
            'is_automatic' => true,
            'is_certificate_template' => 1,
        ]);

        // Template 2: Certificate Renewed
        MailTemplate::create([
            'name' => 'Certificaat succesvol verlengd',
            'subject' => 'Certificaat [certificaat_naam] is verlengd',
            'body' => '<h2>Certificaat Verlengd</h2>

<p>Beste collega,</p>

<p>Het certificaat <strong>[certificaat_naam]</strong> is succesvol verlengd.</p>

<p><strong>Details:</strong></p>
<ul>
    <li>Certificaat: [certificaat_naam]</li>
    <li>Categorie: [certificaat_categorie]</li>
    <li>Oude vervaldatum: [oude_vervaldatum]</li>
    <li>Nieuwe vervaldatum: <strong>[nieuwe_vervaldatum]</strong></li>
    <li>Verlengd door: [verlengd_door]</li>
    <li>Chauffeur: [chauffeur_naam]</li>
</ul>

<div style="margin: 30px 0; text-align: center;">
    <a href="[certificaat_link]" style="background-color: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Bekijk Certificaat in Systeem
    </a>
</div>

<p>Bedankt voor het up-to-date houden van de certificaten!</p>

<p>Met vriendelijke groet,<br>Autoschadeplan</p>',
            'trigger_type' => 'CERTIFICATE_RENEWED',
            'is_active' => true,
            'is_automatic' => true,
            'is_certificate_template' => 1,
        ]);

        // Template 3: Certificate Notification Error (for super admin)
        MailTemplate::create([
            'name' => 'Certificaat notificatie fout (Super Admin)',
            'subject' => 'FOUT: Certificaat notificatie mislukt',
            'body' => '<h2 style="color: #dc3545;">Certificaat Notificatie Fout</h2>

<p>Er is een fout opgetreden bij het versturen van een certificaat notificatie.</p>

<p><strong>Fout Details:</strong></p>
<pre style="background: #f8f9fa; padding: 15px; border-radius: 5px;">[error_bericht]</pre>

<p><strong>Certificaat Informatie:</strong></p>
<ul>
    <li>ID: [certificaat_id]</li>
    <li>Naam: [certificaat_naam]</li>
</ul>

<p>Controleer de logs voor meer details en los het probleem zo spoedig mogelijk op.</p>

<p style="color: #666;"><small>Dit is een automatische melding voor de super admin.</small></p>',
            'trigger_type' => 'CERTIFICATE_NOTIFICATION_ERROR',
            'is_active' => true,
            'is_automatic' => true,
            'is_certificate_template' => 1,
        ]);
    }
}

