<?php

namespace App\Services\Academy;

use App\Models\Driver;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Exports the driver card as a form ("Check je papieren"): what we know is printed,
 * what is missing is left open for the driver to fill in.
 */
class DriverCardFormService
{
    public function __construct(protected DriverCardService $cards)
    {
    }

    /**
     * @param Collection<int, Driver>|Driver $drivers One page per driver.
     * @return string PDF content
     */
    public function pdf(Collection|Driver $drivers): string
    {
        $drivers = $drivers instanceof Driver ? collect([$drivers]) : $drivers->values();

        $pages = $drivers->map(fn (Driver $driver) => $this->data($driver));

        return Pdf::loadView('admin.academy.forms.card', ['pages' => $pages])
            ->setPaper('a4')
            ->output();
    }

    public function fileName(Collection|Driver $drivers): string
    {
        if ($drivers instanceof Driver) {
            $contact = $drivers->contact;

            return 'Check-je-papieren-' . Str::slug(trim(($contact?->first_name ?? '') . ' ' . ($contact?->last_name ?? '')) ?: 'chauffeur') . '.pdf';
        }

        return 'Check-je-papieren-' . Carbon::today()->format('Y-m-d') . '.pdf';
    }

    /**
     * Values for one form page.
     */
    public function data(Driver $driver): array
    {
        $driver->loadMissing('contact', 'company', 'certificates.metaFields', 'certificates.category', 'certificates.code95Courses');

        $contact = $driver->contact;
        $rows = $this->cards->rows($driver)->keyBy(fn ($row) => $row['category']->code);

        $licenseCertificate = $rows['rijbewijs']['certificate'] ?? null;
        $code95 = $rows['code_95']['code95'] ?? null;

        $documents = $rows->map(function ($row) {
            $certificate = $row['certificate'];
            $category = $row['category'];
            $oneOff = $category->one_off;
            $date = $oneOff ? $certificate?->getRawOriginal('issue_date') : $certificate?->getRawOriginal('expiry_date');

            return [
                'code' => $category->code,
                'name' => $category->name,
                'one_off' => $oneOff,
                'allow_na' => $category->allow_not_applicable,
                'not_applicable' => (bool) $certificate?->not_applicable,
                'date' => $date ? Carbon::parse($date)->format('d-m-Y') : null,
            ];
        });

        return [
            'company' => $driver->company?->name,
            'name' => trim(($contact?->first_name ?? '') . ' ' . ($contact?->last_name ?? '')),
            'first_name' => $contact?->first_name,
            'function' => $driver->function,
            'birthdate' => $contact?->birthdate ? Carbon::parse($contact->birthdate)->format('d-m-Y') : null,
            'email' => $contact?->email ?: null,
            'phone' => $contact?->phone ?: null,
            'street' => $driver->street,
            'zipcode' => $driver->zipcode,
            'city' => $driver->city,
            'cbr_customer_number' => $driver->cbr_customer_number,
            'license_number' => $contact?->driver_license_id,
            'license_categories' => $licenseCertificate?->getMetaValue('license_categories'),
            'pass_number' => $driver->driver_pass_number,
            'code95_hours' => $code95 && $code95['openingHours'] > 0 ? (string) $code95['openingHours'] : null,
            'documents' => $documents,
            'date' => Carbon::today()->format('d-m-Y'),
            'reply_to' => config('academy.mail.reply_to'),
        ];
    }

    /**
     * Whether the card still misses something the driver can supply.
     */
    public function hasGaps(Driver $driver): bool
    {
        $missing = collect($this->cards->missing($driver))
            ->reject(fn ($item) => str_starts_with($item, 'vinkje'));

        return $missing->isNotEmpty() || !empty($this->cards->toFillIn($driver));
    }
}
