<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use Illuminate\Database\Seeder;

class TestMailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        MailTemplate::create([
            'name' => 'Test - Status Wijziging Notificatie',
            'subject' => 'Status gewijzigd voor schade [dossiernr]',
            'body' => '<p>Beste [contact_naam],</p>

<p>De status van schade <strong>[dossiernr]</strong> is gewijzigd.</p>

<p><strong>Nieuwe status:</strong> [status]</p>

<p><strong>Details:</strong></p>
<ul>
    <li>Bedrijf: [bedrijf]</li>
    <li>Kenteken: [kenteken]</li>
    <li>Datum schade: [datumschade]</li>
</ul>

<p>Voor vragen kunt u contact met ons opnemen.</p>

<p>Met vriendelijke groet,<br>
[bedrijf]</p>',
            'trigger_type' => 'CLAIM_STATUS_CHANGED',
            'is_active' => true,
            'is_automatic' => true,
            'team_id' => null, // Global template
        ]);
    }
}
