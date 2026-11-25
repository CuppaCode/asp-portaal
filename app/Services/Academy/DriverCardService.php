<?php

namespace App\Services\Academy;

use App\Models\AcademyTrainingParticipant;
use App\Models\Certificate;
use App\Models\CertificateCategory;
use App\Models\Contact;
use App\Models\Driver;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * The Academy driver card: person data, documents (certificates) and the four-eyes check.
 */
class DriverCardService
{
    public const STATE_INCOMPLETE = 'incomplete';
    public const STATE_UNCHECKED = 'unchecked';
    public const STATE_CHECKED = 'checked';

    public const STATE_LABELS = [
        self::STATE_INCOMPLETE => 'onvolledig',
        self::STATE_UNCHECKED => 'niet gecontroleerd',
        self::STATE_CHECKED => 'gecontroleerd',
    ];

    protected ?Collection $categories = null;

    public function __construct(
        protected CertificateStatusService $statusService,
        protected Code95Calculator $code95
    ) {
    }

    public function categories(): Collection
    {
        return $this->categories ??= CertificateCategory::academy()->get();
    }

    /**
     * The active certificate of a driver for a category: the one with the latest expiry date.
     */
    public function currentCertificate(Driver $driver, CertificateCategory $category): ?Certificate
    {
        return $driver->certificates
            ->where('category_id', $category->id)
            ->sortByDesc(fn (Certificate $certificate) => ($certificate->getRawOriginal('expiry_date') ?: '0000') . '#' . str_pad((string) $certificate->id, 10, '0', STR_PAD_LEFT))
            ->first();
    }

    /**
     * One row per Academy document type with its certificate and status.
     */
    public function rows(Driver $driver, ?Carbon $today = null): Collection
    {
        $driver->loadMissing('certificates.metaFields', 'certificates.code95Courses', 'certificates.category');

        return $this->categories()->map(function (CertificateCategory $category) use ($driver, $today) {
            $certificate = $this->currentCertificate($driver, $category);

            return [
                'category' => $category,
                'certificate' => $certificate,
                'status' => $this->statusService->evaluate($certificate, $category, $today),
                'code95' => ($category->isCode95() && $certificate)
                    ? $this->code95->progress($certificate, $today)
                    : null,
            ];
        });
    }

    /**
     * Items that keep the card "onvolledig".
     *
     * @return string[]
     */
    public function missing(Driver $driver): array
    {
        $driver->loadMissing('contact', 'certificates');

        $missing = [];
        $contact = $driver->contact;

        if (!$contact || trim(($contact->first_name ?? '') . ($contact->last_name ?? '')) === '') {
            $missing[] = 'naam';
        }

        if (!$contact || empty($contact->birthdate)) {
            $missing[] = 'geboortedatum';
        }

        $requiredCodes = ['rijbewijs' => 'rijbewijs', 'code_95' => 'Code 95', 'chauffeurspas' => 'chauffeurspas'];

        foreach ($this->categories() as $category) {
            if (!isset($requiredCodes[$category->code])) {
                continue;
            }

            $certificate = $this->currentCertificate($driver, $category);

            if (!$certificate || !$certificate->expiryCarbon()) {
                $missing[] = $requiredCodes[$category->code];
            }
        }

        if (!$driver->scans_attached) {
            $missing[] = 'vinkje "Scans weggelakt"';
        }

        return $missing;
    }

    /**
     * Documents that are neither filled in nor marked "n.v.t." (does not block the card).
     *
     * @return string[]
     */
    public function toFillIn(Driver $driver): array
    {
        return $this->rows($driver)
            ->filter(fn ($row) => $row['status']['status'] === CertificateStatusService::TODO)
            ->map(fn ($row) => $row['category']->name)
            ->values()
            ->all();
    }

    public function state(Driver $driver): string
    {
        if (!empty($this->missing($driver))) {
            return self::STATE_INCOMPLETE;
        }

        return $driver->checked_at ? self::STATE_CHECKED : self::STATE_UNCHECKED;
    }

    public function isComplete(Driver $driver): bool
    {
        return empty($this->missing($driver));
    }

    /**
     * Whether the card is complete and checked by a second person.
     */
    public function isFinal(Driver $driver): bool
    {
        return $this->state($driver) === self::STATE_CHECKED;
    }

    /**
     * Save the card. $data is validated by DriverCardController.
     */
    public function save(Driver $driver, array $data): void
    {
        $driver->loadMissing('contact', 'certificates.metaFields');
        $invalidateCheck = false;

        // Person data lives on the contact.
        $contact = $driver->contact ?? new Contact([
            'company_id' => $driver->company_id,
            'team_id' => $driver->team_id,
            'is_driver' => true,
            'create_user' => 0,
        ]);

        $contact->fill(array_filter([
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
        ], fn ($value) => $value !== null));
        $contact->email = $data['email'] ?? $contact->email ?? '';
        $contact->phone = $data['phone'] ?? $contact->phone;
        $contact->birthdate = $data['birthdate'] ?? null;
        $contact->driver_license_id = $data['driver_license_id'] ?? null;

        if ($contact->isDirty(['birthdate', 'driver_license_id'])) {
            $invalidateCheck = true;
        }

        $contact->save();

        if (!$driver->contact_id) {
            $driver->contact_id = $contact->id;
        }

        $inService = (bool) ($data['in_service'] ?? false);
        $driver->fill([
            'function' => $data['function'] ?? null,
            'in_service' => $inService,
            'out_of_service_at' => $inService ? null : ($driver->out_of_service_at ?? Carbon::today()),
            'street' => $data['street'] ?? null,
            'zipcode' => $data['zipcode'] ?? null,
            'city' => $data['city'] ?? null,
            'cbr_customer_number' => $data['cbr_customer_number'] ?? null,
            'driver_pass_number' => $data['driver_pass_number'] ?? null,
            'scans_attached' => (bool) ($data['scans_attached'] ?? false),
        ]);

        if ($driver->isDirty('driver_pass_number')) {
            $invalidateCheck = true;
        }

        $driver->save();

        foreach ($this->categories() as $category) {
            $input = $data['certificates'][$category->id] ?? null;

            if (is_array($input) && $this->saveCertificate($driver, $category, $input, $data)) {
                $invalidateCheck = true;
            }
        }

        if ($invalidateCheck && $driver->checked_at) {
            $driver->forceFill(['checked_by_user_id' => null, 'checked_at' => null])->save();
        }
    }

    /**
     * @return bool Whether something relevant for the four-eyes check changed.
     */
    protected function saveCertificate(Driver $driver, CertificateCategory $category, array $input, array $data): bool
    {
        $certificate = $this->currentCertificate($driver, $category);

        $notApplicable = $category->allow_not_applicable && !empty($input['not_applicable']);
        $expiry = $notApplicable || $category->one_off ? null : ($input['expiry_date'] ?? null);
        $issue = $notApplicable ? null : ($input['issue_date'] ?? null);

        if ($expiry === '') {
            $expiry = null;
        }
        if ($issue === '') {
            $issue = null;
        }

        $extraFilled = $category->isCode95()
            && (($data['code95_hours'] ?? '') !== '' || ($data['code95_practice_hours'] ?? '') !== '');

        if (!$certificate && !$notApplicable && !$expiry && !$issue && !$extraFilled) {
            return false;
        }

        $changed = false;

        if (!$certificate) {
            $certificate = new Certificate([
                'driver_id' => $driver->id,
                'category_id' => $category->id,
                'name' => $category->name,
                'team_id' => $driver->team_id ?? auth()->user()?->team_id,
            ]);
            $changed = true;
        }

        $certificate->fill([
            'not_applicable' => $notApplicable,
            'expiry_date' => $expiry,
            'issue_date' => $issue,
        ]);

        if ($certificate->isDirty()) {
            $changed = true;
        }

        $certificate->save();

        if ($category->code === 'rijbewijs' && array_key_exists('license_categories', $data)) {
            $certificate->upsertMetaValue('license_categories', 'string', $data['license_categories'] ?: null, 'internal');
        }

        if ($category->isCode95()) {
            $hours = $data['code95_hours'] ?? null;
            $practice = $data['code95_practice_hours'] ?? null;

            if ($hours !== null && $hours !== '') {
                if ((int) $certificate->getMetaValue(Code95Calculator::META_OPENING_HOURS, 0) !== (int) $hours) {
                    $changed = true;
                }
                $certificate->upsertMetaValue(Code95Calculator::META_OPENING_HOURS, 'number', (int) $hours, 'internal');
            }

            if ($practice !== null && $practice !== '') {
                if ((int) $certificate->getMetaValue(Code95Calculator::META_OPENING_PRACTICE, 0) !== (int) $practice) {
                    $changed = true;
                }
                $certificate->upsertMetaValue(Code95Calculator::META_OPENING_PRACTICE, 'number', (int) $practice, 'internal');
            }
        }

        $driver->unsetRelation('certificates');

        return $changed;
    }

    /**
     * The four-eyes check: a second person compares the card with the original documents and Mijn CBR.
     *
     * @throws ValidationException
     */
    public function check(Driver $driver, User $checker, User $actor): void
    {
        if ($checker->is($actor)) {
            throw ValidationException::withMessages([
                'checked_by_user_id' => 'De controle moet door een tweede persoon worden gedaan.',
            ]);
        }

        if (!$this->isComplete($driver)) {
            throw ValidationException::withMessages([
                'checked_by_user_id' => 'De kaart is nog onvolledig: ' . implode(', ', $this->missing($driver)) . '.',
            ]);
        }

        $driver->forceFill([
            'checked_by_user_id' => $checker->id,
            'checked_at' => now(),
        ])->save();
    }

    /**
     * Trainings the driver is planned for (status "ingepland" on the card).
     */
    public function plannedTrainings(Driver $driver): Collection
    {
        return AcademyTrainingParticipant::query()
            ->with('training')
            ->where('driver_id', $driver->id)
            ->whereHas('training', fn ($query) => $query->whereNull('closed_at'))
            ->get()
            ->sortBy(fn ($participant) => $participant->training->training_date);
    }
}
