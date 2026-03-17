<?php

namespace App\Services;

use App\Models\Mailing;
use App\Models\MailTemplate;
use App\Notifications\PlainMail;
use Illuminate\Support\Facades\Notification;

class MailTriggerService
{
    /**
     * Available trigger types in the system
     */
    const TRIGGERS = [
        'CLAIM_CREATED' => [
            'name' => 'Claim Created',
            'description' => 'Triggered when a new claim is created',
            'recipients' => 'Contact person of the claim / company',
            'available_tags' => ['bedrijf', 'dossiernr', 'contact_naam', 'kenteken', 'datumschade'],
        ],
        'CLAIM_STATUS_CHANGED' => [
            'name' => 'Claim Status Changed',
            'description' => 'Triggered when a claim status is updated',
            'recipients' => 'Contact person of the claim / company',
            'available_tags' => ['bedrijf', 'dossiernr', 'status', 'contact_naam'],
        ],
        'TASK_ASSIGNED' => [
            'name' => 'Task Assigned',
            'description' => 'Triggered when a task is assigned to a user',
            'recipients' => 'The user the task is assigned to',
            'available_tags' => ['taak_titel', 'taak_beschrijving', 'taak_deadline', 'toegewezen_aan', 'toegewezen_email', 'aangemaakt_door', 'plus_alle_schade_tags'],
        ],
        'CERTIFICATE_EXPIRING' => [
            'name' => 'Certificaat verloopt binnenkort',
            'description' => 'Triggered when a certificate is about to expire',
            'recipients' => 'Driver and/or company contact',
            'available_tags' => ['certificaat_naam', 'certificaat_categorie', 'certificaat_vervaldatum', 'chauffeur_naam', 'chauffeur_email', 'bedrijf', 'dagen_tot_verloop', 'verlenging_link'],
        ],
        'CERTIFICATE_RENEWED' => [
            'name' => 'Certificaat verlengd',
            'description' => 'Triggered when a certificate has been renewed',
            'recipients' => 'Driver and/or company contact',
            'available_tags' => ['certificaat_naam', 'certificaat_categorie', 'oude_vervaldatum', 'nieuwe_vervaldatum', 'verlengd_door', 'certificaat_link'],
        ],
        'CERTIFICATE_NOTIFICATION_ERROR' => [
            'name' => 'Certificaat notificatie fout',
            'description' => 'Triggered when certificate notification fails (super admin only)',
            'recipients' => 'Super admin',
            'available_tags' => ['error_bericht', 'certificaat_naam', 'certificaat_id'],
        ],
        'MANUAL_CLAIMS' => [
            'name' => 'Manual - Claims',
            'description' => 'Manually selected templates available in claims section',
            'recipients' => 'Selected manually by the user when sending',
            'available_tags' => ['all_claim_tags'],
        ],
        'MANUAL_GENERAL' => [
            'name' => 'Manual - General',
            'description' => 'Manually selected templates for general use',
            'recipients' => 'Selected manually by the user when sending',
            'available_tags' => [],
        ],
        'VERWIJTBAAR_SET' => [
            'name' => 'Verwijtbaar ingesteld',
            'description' => 'Triggered when a claim is marked as verwijtbaar (blameworthy)',
            'recipients' => 'Primary contact person of the company',
            'available_tags' => ['bedrijf', 'dossiernr', 'contact_naam', 'kenteken', 'datumschade', 'verwijtbaar'],
        ],
    ];

    /**
     * Prepare mailings for a specific trigger
     *
     * @param string $triggerType
     * @param mixed $model (Claim, Task, etc.)
     * @param array $options ['recipients' => [], 'cc' => [], 'bcc' => [], 'reply_to' => '']
     * @return array Array of created mailing IDs
     */
    public function dispatch($triggerType, $model, $options = [])
    {
        // Find all active automatic templates for this trigger
        $templates = MailTemplate::active()
            ->automatic()
            ->byTrigger($triggerType)
            ->get();

        if ($templates->isEmpty()) {
            return [];
        }

        $mailingIds = [];

        foreach ($templates as $template) {
            // Perform tag replacement
            $subject = $this->replaceTags($template->subject, $model);
            $body = $this->replaceTags($template->body, $model);

            // Create mailing record
            $mailing = Mailing::create([
                'subject' => $subject,
                'body' => $body,
                'recipients' => $options['recipients'] ?? [],
                'cc' => $options['cc'] ?? [],
                'bcc' => $options['bcc'] ?? [],
                'reply_to' => $options['reply_to'] ?? null,
                'status' => 'scheduled',
                'user_id' => auth()->id(),
                'mail_template_id' => $template->id,
                'team_id' => $model->team_id ?? auth()->user()->team_id ?? null,
            ]);

            // Attach to claim if applicable
            if (get_class($model) === 'App\Models\Claim') {
                $mailing->claims()->attach($model->id);
            }

            // Send the mailing immediately for automatic triggers
            $this->sendMailing($mailing->id);

            $mailingIds[] = $mailing->id;
        }

        return $mailingIds;
    }

    /**
     * Send a prepared mailing
     *
     * @param int $mailingId
     * @return bool
     */
    public function sendMailing($mailingId)
    {
        $mailing = Mailing::findOrFail($mailingId);

        if ($mailing->status === 'sent') {
            return false; // Already sent
        }

        try {
            // Prepare attachments from media library
            $attachments = [];
            foreach ($mailing->getMedia('attachments') as $media) {
                $attachments[] = [
                    'path' => $media->getPath(),
                    'name' => $media->file_name,
                ];
            }

            // Create notification
            $message = new PlainMail(
                $mailing->subject,
                $mailing->body,
                $attachments,
                $mailing->cc ?? [],
                $mailing->bcc ?? []
            );

            // Send to all recipients
            foreach ($mailing->recipients as $recipient) {
                Notification::route('mail', [$recipient => ''])
                    ->notify($message);
            }

            // Update mailing status
            $mailing->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            $mailing->update(['status' => 'failed']);
            \Log::error('Failed to send mailing: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send multiple mailings at once
     *
     * @param array $mailingIds
     * @return array ['sent' => count, 'failed' => count]
     */
    public function sendBatch(array $mailingIds)
    {
        $results = ['sent' => 0, 'failed' => 0];

        foreach ($mailingIds as $mailingId) {
            if ($this->sendMailing($mailingId)) {
                $results['sent']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Replace template tags with actual data from model
     *
     * @param string $content
     * @param mixed $model
     * @return string
     */
    protected function replaceTags($content, $model)
    {
        if (!$content) {
            return '';
        }

        // Handle Claim model tags
        if (get_class($model) === 'App\Models\Claim') {
            $replacements = [
                '[bedrijf]' => $model->company->name ?? '',
                '[telnr]' => $model->company->phone ?? '',
                '[onderwerp]' => $model->subject ?? '',
                '[dossiernr]' => $model->claim_number ?? '',
                '[status]' => \App\Models\Claim::STATUS_SELECT[$model->status] ?? $model->status ?? '',
                '[datumschade]' => $model->accident_date ? $model->accident_date->format('d-m-Y') : '',
                '[kenteken]' => $model->vehicle->plates ?? '',
                '[schade_aard]' => \App\Models\Claim::DAMAGED_PART_SELECT[$model->damaged_part] ?? $model->damaged_part ?? '',
                '[schade_plaats]' => \App\Models\Claim::DAMAGED_AREA_SELECT[$model->damaged_area] ?? $model->damaged_area ?? '',
                '[schade_oorzaak]' => \App\Models\Claim::DAMAGE_ORIGIN[$model->damage_origin] ?? $model->damage_origin ?? '',
                '[schade_bedrag]' => $model->damage_costs ?? '',
                '[kenteken_wederpartij]' => $model->opposite->vehicle_plates ?? '',
                '[verhaalbaar]' => $model->recoverable ? 'Ja' : 'Nee',
                '[schade_soort]' => $model->damage_kind ?? '',
                '[contact_naam]' => $model->contact->name ?? '',
                '[contact_email]' => $model->contact->email ?? '',
                '[herstel_adres]' => $model->recoveryOffice->address ?? '',
                '[herstel_postcode]' => $model->recoveryOffice->zipcode ?? '',
                '[herstel_telnr]' => $model->recoveryOffice->phone ?? '',
                '[herstel_contact_naam]' => $model->recoveryOffice->name ?? '',
                '[herstel_email]' => $model->recoveryOffice->email ?? '',
                '[chauffeur_naam]' => $model->driver->name ?? '',
                '[chauffeur_email]' => $model->driver->email ?? '',
                '[wederpartij_naam]' => $model->opposite->name ?? '',
                '[wederpartij_adres]' => $model->opposite->address ?? '',
                '[wederpartij_postcode_stad]' => ($model->opposite->zipcode ?? '') . ' ' . ($model->opposite->city ?? ''),
                '[wederpartij_telnr]' => $model->opposite->phone ?? '',
                '[wederpartij_email]' => $model->opposite->email ?? '',
                '[wederpartij_schade_aard]' => \App\Models\Claim::DAMAGED_PART_OPPOSITE_SELECT[$model->opposite->damaged_part] ?? $model->opposite->damaged_part ?? '',
                '[wederpartij_schade_plaats]' => \App\Models\Claim::DAMAGED_AREA_OPPOSITE_SELECT[$model->opposite->damaged_area] ?? $model->opposite->damaged_area ?? '',
                '[wederpartij_schade_oorzaak]' => \App\Models\Claim::DAMAGE_ORIGIN_OPPOSITE[$model->opposite->damage_origin] ?? $model->opposite->damage_origin ?? '',
                '[verwijtbaar]' => \App\Models\Claim::VERWIJTBAAR_SELECT[$model->verwijtbaar] ?? '',
            ];

            foreach ($replacements as $tag => $value) {
                $content = str_replace($tag, $value, $content);
            }
        }

        // Add support for Task model tags
        if (get_class($model) === 'App\Models\Task') {
            $replacements = [
                '[taak_titel]' => $model->name ?? '',
                '[taak_beschrijving]' => $model->description ?? '',
                '[taak_deadline]' => $model->due_date ? $model->due_date->format('d-m-Y') : '',
                '[toegewezen_aan]' => $model->user->name ?? '',
                '[toegewezen_email]' => $model->user->email ?? '',
                '[aangemaakt_door]' => $model->createdBy->name ?? '',
            ];

            foreach ($replacements as $tag => $value) {
                $content = str_replace($tag, $value, $content);
            }
            
            // If task has a claim, also replace claim tags
            if ($model->claim_id && $model->claim) {
                $content = $this->replaceTags($content, $model->claim);
            }
        }

        // Add support for Certificate model tags
        if (get_class($model) === 'App\Models\Certificate') {
            $replacements = [
                '[certificaat_naam]' => $model->name ?? '',
                '[certificaat_categorie]' => $model->category->name ?? '',
                '[certificaat_vervaldatum]' => $model->expiry_date ? $model->expiry_date->format('d-m-Y') : '',
                '[chauffeur_naam]' => $model->driver->name ?? '',
                '[chauffeur_email]' => $model->driver->email ?? '',
                '[bedrijf]' => $model->driver->company->name ?? 'N/A',
                '[dagen_tot_verloop]' => $model->expiry_date ? $model->expiry_date->diffInDays(\Carbon\Carbon::now()) : '',
                '[verlenging_link]' => $model->renewal_token ? route('certificate.renew.form', $model->renewal_token) : '',
                '[oude_vervaldatum]' => $model->original_expiry_date ? \Carbon\Carbon::parse($model->original_expiry_date)->format('d-m-Y') : '',
                '[nieuwe_vervaldatum]' => $model->expiry_date ? $model->expiry_date->format('d-m-Y') : '',
                '[verlengd_door]' => $model->renewed_by_email ?? ($model->renewedBy->name ?? 'Systeem'),
                '[certificaat_link]' => url('/admin/certificates/' . $model->id),
                '[error_bericht]' => $model->error_message ?? '',
                '[certificaat_id]' => $model->id ?? '',
            ];

            foreach ($replacements as $tag => $value) {
                $content = str_replace($tag, $value, $content);
            }
        }

        return $content;
    }

    /**
     * Get all available triggers with metadata
     *
     * @return array
     */
    public static function getAvailableTriggers()
    {
        return self::TRIGGERS;
    }

    /**
     * Get trigger configuration
     *
     * @param string $triggerType
     * @return array|null
     */
    public static function getTrigger($triggerType)
    {
        return self::TRIGGERS[$triggerType] ?? null;
    }
}
