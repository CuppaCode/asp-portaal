<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use Illuminate\Database\Seeder;

class AcademyMailTemplateSeeder extends Seeder
{
    public const TRIGGER = 'ACADEMY';

    /**
     * Creates the Academy mail templates (M1 - M13). The texts are maintained in the mail template
     * management; existing subject and body are never overwritten.
     */
    public function run()
    {
        foreach (config('academy.mail_templates', []) as $code => $definition) {
            $template = MailTemplate::where('code', $code)->first();

            if (!$template) {
                $template = new MailTemplate([
                    'code' => $code,
                    'name' => $code . ' - ' . $definition['name'],
                    'subject' => $definition['name'],
                    'body' => '<p>De tekst van ' . $code . ' wordt nog toegevoegd.</p>',
                ]);
            }

            $template->fill([
                'trigger_type' => self::TRIGGER,
                'recipient_rule' => $definition['recipient'] ?? null,
                'cc_rule' => $definition['cc'] ?? null,
                'is_active' => true,
                'is_automatic' => false,
            ]);

            $template->save();
        }
    }
}
