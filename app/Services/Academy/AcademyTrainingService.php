<?php

namespace App\Services\Academy;

use App\Models\AcademyTraining;
use App\Models\AcademyTrainingParticipant;
use App\Models\Certificate;
use App\Models\CertificateCategory;
use App\Models\Code95Course;
use App\Models\Contact;
use App\Models\Driver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AcademyTrainingService
{
    public function __construct(protected AcademyChecklistService $checklist)
    {
    }

    /**
     * @param int[] $driverIds
     */
    public function addParticipants(AcademyTraining $training, array $driverIds): int
    {
        $drivers = Driver::where('company_id', $training->company_id)
            ->whereIn('id', $driverIds)
            ->pluck('id');

        $added = 0;

        foreach ($drivers as $driverId) {
            $participant = AcademyTrainingParticipant::firstOrCreate([
                'training_id' => $training->id,
                'driver_id' => $driverId,
            ]);

            $added += $participant->wasRecentlyCreated ? 1 : 0;
        }

        $this->checklist->refresh($training);

        return $added;
    }

    /**
     * A participant that is not known yet: creates the driver and adds him to the dossier.
     */
    public function createParticipant(AcademyTraining $training, array $data): AcademyTrainingParticipant
    {
        return DB::transaction(function () use ($training, $data) {
            $company = $training->company;
            $teamId = $company->team_id ?? auth()->user()?->team_id;

            $contact = Contact::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? null,
                'birthdate' => $data['birthdate'] ?? null,
                'company_id' => $company->id,
                'team_id' => $teamId,
                'is_driver' => true,
                'create_user' => 0,
            ]);

            $driver = Driver::create([
                'contact_id' => $contact->id,
                'company_id' => $company->id,
                'team_id' => $teamId,
                'function' => $data['function'] ?? null,
            ]);

            $participant = AcademyTrainingParticipant::create([
                'training_id' => $training->id,
                'driver_id' => $driver->id,
            ]);

            $this->checklist->refresh($training);

            return $participant;
        });
    }

    public function removeParticipant(AcademyTraining $training, AcademyTrainingParticipant $participant): void
    {
        $this->assertOpen($training);

        if ($participant->course) {
            $participant->course->delete();
        }

        $participant->delete();

        $this->checklist->refresh($training);
    }

    public function setAttendance(AcademyTraining $training, AcademyTrainingParticipant $participant, bool $attended): void
    {
        $this->assertOpen($training);

        if (!$attended) {
            $this->unconfirmHours($participant);
        }

        $participant->update(['attended' => $attended]);
    }

    /**
     * Hours go on the card only after they are visible in the CBR register.
     *
     * @throws ValidationException
     */
    public function confirmHours(AcademyTraining $training, AcademyTrainingParticipant $participant): Code95Course
    {
        $this->assertOpen($training);

        if (!$training->hasCode95Module()) {
            throw ValidationException::withMessages(['participant' => 'Deze training heeft geen Code 95-uren.']);
        }

        if (!$participant->attended) {
            throw ValidationException::withMessages(['participant' => 'Alleen aanwezige deelnemers krijgen uren.']);
        }

        if ($participant->course) {
            return $participant->course;
        }

        $certificate = $this->code95Certificate($participant->driver);

        $course = Code95Course::create([
            'certificate_id' => $certificate->id,
            'course_date' => $training->training_date,
            'name' => $training->name,
            'code' => $training->cbr_code ?? '',
            'type' => $training->code95_type ?: 'theory',
            'hours' => $training->code95_hours,
            'cbr_registered' => true,
            'academy_training_id' => $training->id,
            'trainer' => $training->provider_name,
            'team_id' => $training->team_id,
        ]);

        $participant->update(['cbr_confirmed' => true, 'code95_course_id' => $course->id]);

        return $course;
    }

    public function unconfirmHours(AcademyTrainingParticipant $participant): void
    {
        if ($participant->course) {
            $participant->course->delete();
        }

        $participant->update(['cbr_confirmed' => false, 'code95_course_id' => null]);
    }

    /**
     * The training can be closed when every step item is ticked or not applicable.
     *
     * @return string[] Reasons why not.
     */
    public function closeBlockers(AcademyTraining $training): array
    {
        $items = $this->checklist->refresh($training);

        return $items
            ->filter(fn ($item) => $item->isOpen())
            ->map(fn ($item) => 'Stap ' . $item->step . ': ' . ($item->definition()['label'] ?? $item->item_key))
            ->values()
            ->all();
    }

    /**
     * @throws ValidationException
     */
    public function close(AcademyTraining $training): void
    {
        $blockers = $this->closeBlockers($training);

        if (!empty($blockers)) {
            throw ValidationException::withMessages([
                'close' => 'Het dossier kan nog niet worden gesloten. Nog open: ' . implode('; ', $blockers),
            ]);
        }

        $training->update(['closed_at' => now()]);
    }

    /**
     * Participants without a birthdate: the birthdate is needed for M6.
     */
    public function participantsWithoutBirthdate(AcademyTraining $training): Collection
    {
        return $training->participants()->with('driver.contact')->get()
            ->filter(fn ($participant) => empty($participant->driver?->contact?->birthdate));
    }

    protected function code95Certificate(Driver $driver): Certificate
    {
        $category = CertificateCategory::where('code', 'code_95')->firstOrFail();

        $certificate = Certificate::where('driver_id', $driver->id)
            ->where('category_id', $category->id)
            ->orderByDesc('expiry_date')
            ->first();

        return $certificate ?? Certificate::create([
            'driver_id' => $driver->id,
            'category_id' => $category->id,
            'name' => $category->name,
            'team_id' => $driver->team_id,
        ]);
    }

    protected function assertOpen(AcademyTraining $training): void
    {
        if ($training->isClosed()) {
            throw ValidationException::withMessages(['training' => 'Dit dossier is gesloten.']);
        }
    }
}
