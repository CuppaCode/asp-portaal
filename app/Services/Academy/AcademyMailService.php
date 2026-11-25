<?php

namespace App\Services\Academy;

use App\Models\AcademyChecklistItem;
use App\Models\AcademyTraining;
use App\Models\Certificate;
use App\Models\CertificateSignal;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Mailing;
use App\Models\MailTemplate;
use App\Models\User;
use App\Notifications\PlainMail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

/**
 * Mails M1 - M13 of the Academy: fill in the tags, find the recipients, send and keep the proof.
 */
class AcademyMailService
{
    /** Tags that differ per driver when a mail is sent to many drivers. */
    public const DRIVER_TAGS = [
        '[voornaam]',
        '[achternaam]',
        '[chauffeur]',
        '[ontbrekend item 1]',
        '[ontbrekend item 2]',
        '[ontbrekend item 3]',
        '[ontbrekende items]',
    ];

    public function __construct(
        protected DriverCardService $cards,
        protected AcademyChecklistService $checklist,
        protected CertificateStatusService $statusService,
        protected DriverCardFormService $forms
    ) {
    }

    /* --------------------------------------------------------------------
     | Context
     | ------------------------------------------------------------------ */

    public function resolveSubject(string $context, int $id): Model
    {
        return match ($context) {
            'employer' => Company::academy()->findOrFail($id),
            'training' => AcademyTraining::findOrFail($id),
            'certificate' => Certificate::with('driver.contact', 'driver.company.contactPerson', 'category')->findOrFail($id),
            default => abort(404),
        };
    }

    public function template(string $code): ?MailTemplate
    {
        return MailTemplate::active()
            ->where('trigger_type', 'ACADEMY')
            ->where('code', $code)
            ->first();
    }

    public function company(Model $subject): ?Company
    {
        return match (true) {
            $subject instanceof Company => $subject,
            $subject instanceof AcademyTraining => $subject->company,
            $subject instanceof Certificate => $subject->driver?->company,
            default => null,
        };
    }

    /* --------------------------------------------------------------------
     | Tags
     | ------------------------------------------------------------------ */

    /**
     * Tags that are the same for every recipient.
     *
     * @return array<string, string>
     */
    public function commonTags(Model $subject, array $extra = []): array
    {
        $company = $this->company($subject);
        $contact = $company?->contactPerson;
        $contactName = $contact ? trim($contact->first_name . ' ' . $contact->last_name) : '';

        $tags = [
            '[bedrijf]' => $company?->name ?? '',
            '[contactpersoon]' => $contactName,
            '[voornaam contactpersoon]' => $contact?->first_name ?? '',
            '[maand]' => Carbon::today()->locale('nl')->translatedFormat('F Y'),
        ];

        if ($subject instanceof Company) {
            $firstMail = $this->checklist->items($subject)->firstWhere('item_key', 'gegevensverzoek');

            $tags += [
                '[startdatum]' => $subject->academy_started_at?->format('d-m-Y') ?? '',
                '[datum eerste mail]' => $firstMail?->done_at?->format('d-m-Y') ?? '',
                '[geldigheidsoverzicht]' => $this->validityOverviewHtml($subject),
            ];
        }

        if ($subject instanceof AcademyTraining) {
            $participants = $subject->participants()->with('driver.contact')->get();
            $akkoord = $this->checklist->items($subject)->firstWhere('item_key', 'akkoord');

            $tags += [
                '[training]' => $subject->name,
                '[cbr_code]' => $subject->cbr_code ?? '',
                '[datum training]' => $subject->training_date->format('d-m-Y'),
                '[locatie]' => $subject->location ?? '',
                '[opleider]' => $subject->provider_name ?? '',
                '[uren]' => (string) ($subject->code95_hours ?? ''),
                '[datum akkoord]' => $akkoord?->done_at?->format('d-m-Y') ?? '',
                '[deelnemerslijst]' => $this->listHtml($participants->map(fn ($participant) => $this->driverName($participant->driver))),
                '[deelnemers met geboortedatum]' => $this->listHtml($participants->map(function ($participant) {
                    $birthdate = $participant->driver?->contact?->birthdate;

                    return $this->driverName($participant->driver) . ($birthdate ? ' (' . Carbon::parse($birthdate)->format('d-m-Y') . ')' : '');
                })),
                '[deelnemers zonder e-mail]' => $participants
                    ->filter(fn ($participant) => empty(trim((string) $participant->driver?->contact?->email)))
                    ->map(fn ($participant) => $this->driverName($participant->driver))
                    ->implode(', '),
            ];
        }

        if ($subject instanceof Certificate) {
            $expiry = $subject->expiryCarbon();

            $tags += [
                '[chauffeur]' => $this->driverName($subject->driver),
                '[voornaam]' => $subject->driver?->contact?->first_name ?? '',
                '[document]' => $subject->category?->name ?? $subject->name,
                '[vervaldatum]' => $expiry?->format('d-m-Y') ?? '',
                '[dagen tot verloop]' => $expiry ? (string) Carbon::today()->diffInDays($expiry, false) : '',
                '[nieuwe vervaldatum]' => $expiry?->format('d-m-Y') ?? '',
            ];
        }

        return array_merge($tags, $extra);
    }

    /**
     * Tags that differ per driver.
     *
     * @return array<string, string>
     */
    public function driverTags(Driver $driver): array
    {
        $contact = $driver->contact;
        $items = $this->missingForDriver($driver);

        return [
            '[voornaam]' => $contact?->first_name ?? '',
            '[achternaam]' => $contact?->last_name ?? '',
            '[chauffeur]' => $this->driverName($driver),
            '[ontbrekend item 1]' => $items[0] ?? '',
            '[ontbrekend item 2]' => $items[1] ?? '',
            '[ontbrekend item 3]' => $items[2] ?? '',
            '[ontbrekende items]' => $this->listHtml(collect($items)),
        ];
    }

    /**
     * What we still miss from the driver (internal checks like the scans tick are left out).
     *
     * @return string[]
     */
    public function missingForDriver(Driver $driver): array
    {
        $missing = collect($this->cards->missing($driver))
            ->reject(fn ($item) => str_starts_with($item, 'vinkje'))
            ->all();

        return array_values(array_unique(array_merge($missing, $this->cards->toFillIn($driver))));
    }

    public function replaceTags(string $text, array $tags): string
    {
        return str_replace(array_keys($tags), array_values($tags), $text);
    }

    /**
     * Placeholders like [datum] that still have to be filled in by hand.
     *
     * @return string[]
     */
    public function placeholders(string $text, array $ignore = []): array
    {
        preg_match_all('/\[[^\[\]\r\n]{1,60}\]/u', $text, $matches);

        return array_values(array_diff(array_unique($matches[0] ?? []), $ignore));
    }

    /* --------------------------------------------------------------------
     | Recipients
     | ------------------------------------------------------------------ */

    /**
     * @return array{to: string[], cc: string[]}
     */
    public function defaultRecipients(MailTemplate $template, Model $subject): array
    {
        return [
            'to' => $this->emailsFor($template->recipient_rule, $subject),
            'cc' => $this->emailsFor($template->cc_rule, $subject),
        ];
    }

    protected function emailsFor(?string $rule, Model $subject): array
    {
        $email = match ($rule) {
            'werkgever' => $this->company($subject)?->contactPerson?->email,
            'opleider' => $subject instanceof AcademyTraining ? $subject->provider_email : null,
            'intern' => config('academy.mail.reply_to'),
            default => null,
        };

        return $email ? [trim($email)] : [];
    }

    /**
     * One mail per driver (bulk): who gets it.
     *
     * @return Collection<int, array{driver: Driver, email: string, name: string}>
     */
    public function bulkTargets(?string $rule, Model $subject): Collection
    {
        $drivers = match ($rule) {
            'drivers_with_email', 'drivers_missing_data' => $subject instanceof Company
                ? $this->checklist->companyDrivers($subject)
                : collect(),
            'participants_with_email' => $subject instanceof AcademyTraining
                ? $subject->participants()->with('driver.contact')->get()->map(fn ($participant) => $participant->driver)->filter()
                : collect(),
            default => collect(),
        };

        return $drivers
            ->filter(fn (Driver $driver) => !empty(trim((string) $driver->contact?->email)))
            ->when($rule === 'drivers_missing_data', fn ($collection) => $collection->filter(fn (Driver $driver) => !empty($this->missingForDriver($driver))))
            ->map(fn (Driver $driver) => [
                'driver' => $driver,
                'email' => trim($driver->contact->email),
                'name' => $this->driverName($driver),
            ])
            ->values();
    }

    /* --------------------------------------------------------------------
     | Sending
     | ------------------------------------------------------------------ */

    /**
     * Send one mail (or one to several addresses) and keep the proof.
     *
     * @param array $uploads Uploaded files (UploadedFile[]).
     * @throws ValidationException
     */
    public function sendSingle(
        MailTemplate $template,
        Model $subject,
        array $to,
        array $cc,
        string $subjectLine,
        string $body,
        User $user,
        array $uploads = [],
        ?AcademyChecklistItem $item = null,
        ?int $signalLevel = null,
        bool $attachForm = false
    ): Mailing {
        $this->assertNoPlaceholders($subjectLine, $body);

        $files = [];
        if ($attachForm && $subject instanceof Company) {
            // M2b: the form for the drivers without e-mail, one page per driver.
            $drivers = $this->checklist->companyDrivers($subject)
                ->filter(fn (Driver $driver) => empty(trim((string) $driver->contact?->email)))
                ->values();

            if ($drivers->isNotEmpty()) {
                $files[] = $this->formFile($drivers);
            }
        }

        try {
            $mailing = $this->deliver($template, $subject, $to, $cc, $subjectLine, $body, $user, $uploads, $files);
        } finally {
            $this->cleanup($files);
        }

        $this->registerProof($subject, $mailing, $user, $item, $signalLevel);

        return $mailing;
    }

    /**
     * Send one personalised mail per driver.
     *
     * @param Collection<int, array{driver: Driver, email: string, name: string}> $targets
     * @return Collection<int, Mailing>
     * @throws ValidationException
     */
    public function sendBulk(
        MailTemplate $template,
        Model $subject,
        Collection $targets,
        array $cc,
        string $subjectTemplate,
        string $bodyTemplate,
        User $user,
        array $uploads = [],
        ?AcademyChecklistItem $item = null,
        bool $attachForm = false
    ): Collection {
        if ($targets->isEmpty()) {
            throw ValidationException::withMessages(['recipients' => 'Kies minstens één ontvanger.']);
        }

        $this->assertNoPlaceholders($subjectTemplate, $bodyTemplate, self::DRIVER_TAGS);

        $mailings = collect();
        $failed = [];

        foreach ($targets as $target) {
            $tags = $this->driverTags($target['driver']);
            $files = $attachForm ? [$this->formFile($target['driver'])] : [];

            try {
                $mailings->push($this->deliver(
                    $template,
                    $subject,
                    [$target['email']],
                    $cc,
                    $this->replaceTags($subjectTemplate, $tags),
                    $this->replaceTags($bodyTemplate, $tags),
                    $user,
                    $uploads,
                    $files
                ));
            } catch (ValidationException $exception) {
                $failed[] = $target['name'];
            } finally {
                $this->cleanup($files);
            }
        }

        if (!empty($failed)) {
            throw ValidationException::withMessages([
                'recipients' => 'Verzenden mislukt voor: ' . implode(', ', $failed) . '. De rest is wel verstuurd; vink de regel pas af als iedereen de mail heeft.',
            ]);
        }

        $this->registerProof($subject, $mailings->first(), $user, $item);

        return $mailings;
    }

    protected function assertNoPlaceholders(string $subjectLine, string $body, array $ignore = []): void
    {
        $left = $this->placeholders($subjectLine . "\n" . $body, $ignore);

        if (!empty($left)) {
            throw ValidationException::withMessages([
                'body' => 'Vul eerst de plaatshouders in: ' . implode(', ', $left) . '.',
            ]);
        }
    }

    /**
     * The form "Check je papieren" as a temporary file, to attach to a mail.
     *
     * @return array{path: string, name: string}
     */
    protected function formFile(Collection|Driver $drivers): array
    {
        $path = tempnam(sys_get_temp_dir(), 'academy-form-');
        file_put_contents($path, $this->forms->pdf($drivers));

        return ['path' => $path, 'name' => $this->forms->fileName($drivers)];
    }

    protected function cleanup(array $files): void
    {
        foreach ($files as $file) {
            @unlink($file['path']);
        }
    }

    protected function deliver(
        MailTemplate $template,
        Model $subject,
        array $to,
        array $cc,
        string $subjectLine,
        string $body,
        User $user,
        array $uploads,
        array $extraFiles = []
    ): Mailing {
        $replyTo = config('academy.mail.reply_to');
        $company = $this->company($subject);
        $html = $this->toHtml($body);

        $mailing = Mailing::create([
            'subject' => $subjectLine,
            'body' => $html,
            'recipients' => array_values($to),
            'cc' => array_values($cc),
            'bcc' => [],
            'reply_to' => $replyTo,
            'status' => 'scheduled',
            'user_id' => $user->id,
            'mail_template_id' => $template->id,
            'context_type' => get_class($subject),
            'context_id' => $subject->getKey(),
            'team_id' => $company?->team_id ?? $user->team_id,
        ]);

        $attachments = [];

        foreach ($template->getMedia('attachments') as $media) {
            $media->copy($mailing, 'attachments');
            $attachments[] = ['path' => $media->getPath(), 'name' => $media->file_name];
        }

        foreach ($uploads as $file) {
            $mailing->addMedia($file)->preservingOriginal()->toMediaCollection('attachments');
            $attachments[] = ['path' => $file->getPathname(), 'name' => $file->getClientOriginalName()];
        }

        foreach ($extraFiles as $file) {
            $mailing->addMedia($file['path'])->preservingOriginal()->usingFileName($file['name'])->toMediaCollection('attachments');
            $attachments[] = ['path' => $file['path'], 'name' => $file['name']];
        }

        try {
            $message = new PlainMail(
                $subjectLine,
                $html,
                $attachments,
                $cc,
                [],
                $replyTo,
                ['address' => config('academy.mail.from_address'), 'name' => config('academy.mail.from_name')],
                false
            );

            foreach ($to as $recipient) {
                Notification::route('mail', [$recipient => ''])->notify($message);
            }
        } catch (\Throwable $exception) {
            $mailing->update(['status' => 'failed']);
            \Log::error('Academy mail failed: ' . $exception->getMessage());

            throw ValidationException::withMessages(['recipients' => 'De mail kon niet worden verzonden: ' . $exception->getMessage()]);
        }

        $mailing->update(['status' => 'sent', 'sent_at' => now()]);

        return $mailing;
    }

    /**
     * Proof of a sent mail: tick the item or register the signal.
     */
    protected function registerProof(Model $subject, Mailing $mailing, User $user, ?AcademyChecklistItem $item, ?int $signalLevel = null): void
    {
        if ($item && $item->subject_type === get_class($subject) && (int) $item->subject_id === (int) $subject->getKey()) {
            $this->checklist->markDone($item, $user, $mailing);
        }

        if ($subject instanceof Certificate && $signalLevel) {
            CertificateSignal::updateOrCreate(
                [
                    'certificate_id' => $subject->id,
                    'level' => $signalLevel,
                    'expiry_date' => $subject->expiryCarbon()?->format('Y-m-d'),
                ],
                [
                    'mailing_id' => $mailing->id,
                    'sent_by_user_id' => $user->id,
                    'sent_at' => now(),
                ]
            );
        }
    }

    /* --------------------------------------------------------------------
     | Helpers
     | ------------------------------------------------------------------ */

    /**
     * Plain text from the editor becomes HTML with line breaks.
     */
    public function toHtml(string $body): string
    {
        return $body === strip_tags($body) ? nl2br(e($body)) : $body;
    }

    protected function driverName(?Driver $driver): string
    {
        return $driver?->contact
            ? trim($driver->contact->first_name . ' ' . $driver->contact->last_name)
            : '';
    }

    protected function listHtml(Collection $items): string
    {
        $items = $items->filter()->values();

        if ($items->isEmpty()) {
            return '';
        }

        return '<ul>' . $items->map(fn ($item) => '<li>' . e($item) . '</li>')->implode('') . '</ul>';
    }

    /**
     * M4: table with every driver in service, the expiry dates and the status.
     */
    public function validityOverviewHtml(Company $company): string
    {
        $drivers = $this->checklist->companyDrivers($company)->sortBy(fn (Driver $driver) => $driver->contact?->last_name);

        if ($drivers->isEmpty()) {
            return '';
        }

        $categories = $this->cards->categories()->reject(fn ($category) => $category->one_off);

        $html = '<table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse;font-size:13px;"><thead><tr><th align="left">Chauffeur</th>';
        foreach ($categories as $category) {
            $html .= '<th align="left">' . e($category->name) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($drivers as $driver) {
            $rows = $this->cards->rows($driver)->keyBy(fn ($row) => $row['category']->id);

            $html .= '<tr><td>' . e($this->driverName($driver)) . '</td>';
            foreach ($categories as $category) {
                $status = $rows[$category->id]['status'] ?? null;
                $cell = '-';

                if ($status && $status['status'] !== CertificateStatusService::NA) {
                    $cell = ($status['expiry'] ? $status['expiry']->format('d-m-Y') . ' ' : '') . '(' . $status['label'] . ')';
                } elseif ($status) {
                    $cell = 'n.v.t.';
                }

                $html .= '<td>' . e($cell) . '</td>';
            }
            $html .= '</tr>';
        }

        return $html . '</tbody></table>';
    }
}
