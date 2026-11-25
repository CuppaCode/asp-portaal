<?php

namespace App\Http\Controllers\Admin;

use App\Certificates\CertificateCategoryModuleRegistry;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateCategory;
use App\Models\CertificateRenewal;
use App\Models\Driver;
use App\Models\Claim;
use App\Services\MailTriggerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // Show a single overview page grouping certificates by category
        $categories = \App\Models\CertificateCategory::with(['certificates' => function($q) {
            $q->with('driver')->orderBy('expiry_date');
        }])->get();

        return view('admin.certificate.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Driver $driver)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $selectedCategory = old('category_id') ? CertificateCategory::find(old('category_id')) : null;
        $module = $this->resolveModuleForCategory($selectedCategory);
        $moduleFieldDefinitions = $module->fieldDefinitions();
        $moduleFieldValues = old('module_fields', []);
        $moduleFieldSchemas = $this->getModuleFieldSchemas();
        $selectedModuleKey = $selectedCategory?->module_key;

        return view('admin.certificate.create', compact(
            'driver',
            'moduleFieldDefinitions',
            'moduleFieldValues',
            'moduleFieldSchemas',
            'selectedModuleKey'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Driver $driver)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'category_id' => 'required|integer|exists:certificate_categories,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'card_number' => 'nullable|string|max:255',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $category = CertificateCategory::find($data['category_id']);
        $derivedName = $category?->name;

        $module = $this->resolveModuleForCategory($category);
        $request->validate($module->validationRules('store'));

        $moduleInput = $module->normalizeInput($request->all());
        
        $certificate = Certificate::create(
            [
                'driver_id' => $driver->id,
                'name' => $derivedName,
                'category_id' => $data['category_id'],
                // Reminder date; the observer recalculates it from the category settings.
                'notify_date' => $data['expiry_date'],
                'issue_date' => $data['issue_date'],
                'expiry_date' => $data['expiry_date'],
                'team_id' => auth()->user()->team_id
            ]
        );

        $module->persist($certificate, $moduleInput);
        $certificate->upsertMetaValue('card_number', 'string', $this->nullIfEmpty($data['card_number'] ?? null), 'internal');

        // Handle file attachments
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                if ($file && $file->isValid()) {
                    $certificate->addMedia($file)
                        ->preservingOriginal()
                        ->toMediaCollection('attachments');
                }
            }
        }

        // Prefer explicit back URL from the form (safer than relying on Referer header)
        $back = $request->input('back_to');

        // compute urls to avoid redirecting back to the same page (or the create page)
        $currentUrl = url()->current();
        $createUrl = route('admin.certificate.create', $driver->id);

        if ($back && Str::startsWith($back, url('/')) && $back !== $currentUrl && $back !== $createUrl) {
            return redirect($back)->with('success', 'Certificaat succesvol aangemaakt!');
        }

        return redirect()->route('admin.drivers.show', $driver->id)->with('success', 'Certificaat succesvol aangemaakt!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $certificate->load([
            'driver.contact',
            'category',
            'metaFields',
            'media',
            'code95Courses',
        ]);

        /** @var CertificateCategoryModuleRegistry $moduleRegistry */
        $moduleRegistry = app(CertificateCategoryModuleRegistry::class);
        $module = $moduleRegistry->resolveByCategory($certificate->category);
        $moduleSections = $module->buildDossierSections($certificate);

        $expiryRaw = $certificate->getRawOriginal('expiry_date');
        $daysUntilExpiry = $expiryRaw ? Carbon::today()->diffInDays(Carbon::parse($expiryRaw), false) : null;
        $nearExpiryThreshold = (int) config('certificates.near_expiry_days', 90);
        $isNearExpiry = $daysUntilExpiry !== null && $daysUntilExpiry >= 0 && $daysUntilExpiry <= $nearExpiryThreshold;

        return view('admin.certificate.show', compact(
            'certificate',
            'moduleSections',
            'daysUntilExpiry',
            'nearExpiryThreshold',
            'isNearExpiry'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $certificate->load('category', 'driver', 'metaFields');

        $module = $this->resolveModuleForCategory($certificate->category);
        $moduleFieldDefinitions = $module->fieldDefinitions();
        $moduleFieldValues = old('module_fields', $this->getModuleFieldValuesFromCertificate($certificate, $moduleFieldDefinitions));
        $moduleFieldSchemas = $this->getModuleFieldSchemas();
        $selectedModuleKey = $certificate->category?->module_key;

        return view('admin.certificate.edit', compact(
            'certificate',
            'moduleFieldDefinitions',
            'moduleFieldValues',
            'moduleFieldSchemas',
            'selectedModuleKey'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'category_id' => 'required|integer|exists:certificate_categories,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'card_number' => 'nullable|string|max:255',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'remove_attachment_ids' => 'nullable|array',
            'remove_attachment_ids.*' => 'integer',
        ]);

        $category = CertificateCategory::find($data['category_id']);
        $derivedName = $category?->name;

        $module = $this->resolveModuleForCategory($category);
        $request->validate($module->validationRules('update', $certificate));

        $moduleInput = $module->normalizeInput($request->all());

        $certificate->update([
            'name' => $derivedName,
            'category_id' => $data['category_id'],
            'issue_date' => $data['issue_date'],
            'expiry_date' => $data['expiry_date'],
        ]);

        $module->persist($certificate, $moduleInput);
        $certificate->upsertMetaValue('card_number', 'string', $this->nullIfEmpty($data['card_number'] ?? null), 'internal');

        // Handle file attachments
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                if ($file && $file->isValid()) {
                    $certificate->addMedia($file)
                        ->preservingOriginal()
                        ->toMediaCollection('attachments');
                }
            }
        }

        // Remove explicitly deselected attachments
        if (!empty($data['remove_attachment_ids'])) {
            foreach ($data['remove_attachment_ids'] as $removeId) {
                $mediaItem = $certificate->getMedia('attachments')->where('id', $removeId)->first();
                if ($mediaItem) {
                    $mediaItem->delete();
                }
            }
        }

        // Respect optional back_to field
        $back = $request->input('back_to');
        if ($back && str_starts_with($back, url('/'))) {
            return redirect($back)->with('success', 'Certificaat bijgewerkt');
        }

        return redirect()->route('admin.certificate.show', $certificate->id)->with('success', 'Certificaat bijgewerkt');
    }

    /**
     * Inline update of issue and/or expiry date (used on the driver dossier).
     */
    public function updateDates(Request $request, Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'issue_date' => 'required_without:expiry_date|date',
            'expiry_date' => 'required_without:issue_date|date',
        ]);

        $issueDate = Carbon::parse($data['issue_date'] ?? $certificate->getRawOriginal('issue_date') ?? $data['expiry_date']);
        $expiryDate = Carbon::parse($data['expiry_date'] ?? $certificate->getRawOriginal('expiry_date'));

        if ($expiryDate->lt($issueDate)) {
            return response()->json([
                'message' => 'Geldig tot mag niet voor de datum van afgifte liggen.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (isset($data['issue_date'])) {
            $certificate->issue_date = $data['issue_date'];
        }
        if (isset($data['expiry_date'])) {
            $certificate->expiry_date = $data['expiry_date'];
        }
        $certificate->save();

        return response()->json([
            'issue_date' => $certificate->issue_date,
            'expiry_date' => $certificate->expiry_date,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //
    }

    /**
     * Manually renew a single certificate
     */
    public function renew(Certificate $certificate, Request $request)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currentExpiryDate = $certificate->getRawOriginal('expiry_date'); // raw Y-m-d from DB

        $validated = $request->validate([
            'new_expiry_date' => 'required|date|after:' . $currentExpiryDate,
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldExpiryDate = $currentExpiryDate;
        
        // If first renewal, store original expiry date
        if (empty($certificate->original_expiry_date)) {
            $certificate->original_expiry_date = $oldExpiryDate;
        }

        // Update certificate
        $certificate->expiry_date = Carbon::parse($validated['new_expiry_date'])->format(config('panel.date_format'));
        $certificate->renewed_by_user_id = auth()->id();
        $certificate->renewal_token = null;
        $certificate->renewal_token_expires_at = null;
        $certificate->last_notification_sent_at = null;
        $certificate->save();

        // Create renewal history record
        CertificateRenewal::create([
            'certificate_id' => $certificate->id,
            'old_expiry_date' => $oldExpiryDate, // Y-m-d
            'new_expiry_date' => $validated['new_expiry_date'],
            'renewed_by_user_id' => auth()->id(),
            'renewal_method' => 'admin_manual',
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->startNewCode95CycleWhenRenewed($certificate);

        // Send confirmation email to driver and admins
        $mailTriggerService = app(MailTriggerService::class);

        $recipients = [];
        if (!empty($certificate->driver->contact->email)) {
            $recipients[] = $certificate->driver->contact->email;
        }
        if (!empty($certificate->category->notification_recipients)) {
            $recipients = array_merge($recipients, $certificate->category->notification_recipients);
        }
        $recipients = array_unique(array_filter($recipients));

        if (count($recipients) > 0) {
            try {
                $mailTriggerService->dispatch('CERTIFICATE_RENEWED', $certificate, ['recipients' => $recipients]);
            } catch (\Exception $e) {
                \Log::error('Failed to send renewal confirmation for certificate ' . $certificate->id . ': ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.certificate.show', $certificate->id)
            ->with('success', 'Certificaat succesvol verlengd tot ' . \Carbon\Carbon::parse($validated['new_expiry_date'])->format('d-m-Y'));
    }

    /**
     * Bulk renew multiple certificates
     */
    public function bulkRenew(Request $request)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'certificate_ids' => 'required|array|min:1',
            'certificate_ids.*' => 'exists:certificate,id',
            'new_expiry_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $certificates = Certificate::whereIn('id', $validated['certificate_ids'])->get();
        $successCount = 0;
        $mailTriggerService = app(MailTriggerService::class);

        foreach ($certificates as $certificate) {
            $currentExpiryDate = $certificate->getRawOriginal('expiry_date'); // raw Y-m-d from DB

            // Validate new date is after current expiry
            if (\Carbon\Carbon::parse($validated['new_expiry_date'])->lte(\Carbon\Carbon::parse($currentExpiryDate))) {
                continue; // Skip certificates where new date isn't later
            }

            $oldExpiryDate = $currentExpiryDate;
            
            // If first renewal, store original expiry date
            if (empty($certificate->original_expiry_date)) {
                $certificate->original_expiry_date = $oldExpiryDate;
            }

            // Update certificate
            $certificate->expiry_date = Carbon::parse($validated['new_expiry_date'])->format(config('panel.date_format'));
            $certificate->renewed_by_user_id = auth()->id();
            $certificate->renewal_token = null;
            $certificate->renewal_token_expires_at = null;
            $certificate->last_notification_sent_at = null;
            $certificate->save();

            // Create renewal history record
            CertificateRenewal::create([
                'certificate_id' => $certificate->id,
                'old_expiry_date' => $oldExpiryDate, // Y-m-d
                'new_expiry_date' => $validated['new_expiry_date'],
                'renewed_by_user_id' => auth()->id(),
                'renewal_method' => 'admin_bulk',
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->startNewCode95CycleWhenRenewed($certificate);

            // Send confirmation email
            $recipients = [];
            if (!empty($certificate->driver->contact->email)) {
                $recipients[] = $certificate->driver->contact->email;
            }
            if (!empty($certificate->category->notification_recipients)) {
                $recipients = array_merge($recipients, $certificate->category->notification_recipients);
            }
            $recipients = array_unique(array_filter($recipients));

            if (count($recipients) > 0) {
                try {
                    $mailTriggerService->dispatch('CERTIFICATE_RENEWED', $certificate, ['recipients' => $recipients]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send renewal confirmation for certificate ' . $certificate->id . ': ' . $e->getMessage());
                }
            }

            $successCount++;
        }

        return redirect()->route('admin.certificate.index')
            ->with('success', $successCount . ' certificaten succesvol verlengd tot ' . \Carbon\Carbon::parse($validated['new_expiry_date'])->format('d-m-Y'));
    }

    /**
     * Store a newly created Code 95 course in storage.
     */
    public function storeCode95Course(Request $request, Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $this->validateCode95Course($request);
        $data['certificate_id'] = $certificate->id;
        $data['team_id'] = auth()->user()->team_id;

        \App\Models\Code95Course::create($data);

        return redirect()->back()->with('success', 'Cursus toegevoegd.');
    }

    /**
     * Update the specified Code 95 course in storage.
     */
    public function updateCode95Course(Request $request, \App\Models\Code95Course $course)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $course->update($this->validateCode95Course($request));

        return redirect()->back()->with('success', 'Cursus bijgewerkt.');
    }

    /**
     * Remove the specified Code 95 course from storage.
     */
    public function destroyCode95Course(\App\Models\Code95Course $course)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $course->delete();

        return redirect()->back()->with('success', 'Cursus verwijderd.');
    }

    private function validateCode95Course(Request $request): array
    {
        return $request->validate([
            'course_date' => 'required|date',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'type' => 'required|in:theory,practice',
            'hours' => 'required|integer|min:1|max:24',
            'trainer' => 'nullable|string|max:255',
        ]);
    }

    /**
     * A renewed Code 95 certificate starts a new 5 year cycle and an M12 task.
     */
    private function startNewCode95CycleWhenRenewed(Certificate $certificate): void
    {
        app(\App\Services\Academy\CertificateRenewalHandler::class)->afterRenewal($certificate);
    }

    private function resolveModuleForCategory(?CertificateCategory $category)
    {
        /** @var CertificateCategoryModuleRegistry $moduleRegistry */
        $moduleRegistry = app(CertificateCategoryModuleRegistry::class);

        return $moduleRegistry->resolveByCategory($category);
    }

    private function getModuleFieldValuesFromCertificate(Certificate $certificate, array $fieldDefinitions): array
    {
        $values = [];

        foreach ($fieldDefinitions as $definition) {
            $key = $definition['key'] ?? null;
            if (!$key) {
                continue;
            }

            $value = $certificate->getMetaValue($key);

            if ($value instanceof Carbon) {
                $value = $value->format('Y-m-d');
            }

            $values[$key] = $value;
        }

        return $values;
    }

    private function getModuleFieldSchemas(): array
    {
        /** @var CertificateCategoryModuleRegistry $moduleRegistry */
        $moduleRegistry = app(CertificateCategoryModuleRegistry::class);

        $schemas = [];
        foreach ($moduleRegistry->allKeys() as $key) {
            $schemas[$key] = $moduleRegistry->resolveByKey($key)->fieldDefinitions();
        }

        return $schemas;
    }

    private function nullIfEmpty($value)
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value) && trim($value) === '') {
            return null;
        }

        return $value;
    }
}
