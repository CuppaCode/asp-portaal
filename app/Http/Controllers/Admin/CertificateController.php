<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
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

        return view('admin.certificate.create', compact('driver'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Driver $driver)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $certificate = Certificate::create(
            [
                'driver_id' => $driver->id,
                'name' => $request->name,
                'category_id' => $request->input('category_id'),
                'notify_date' => $request->notify_date,
                'expiry_date' => $request->expiry_date,
                'team_id' => auth()->user()->team_id
            ]
        );

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
        // Show single certificate details
        $certificate->load('driver.contact', 'category');
        return view('admin.certificate.show', compact('certificate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // show edit form
        $certificate->load('category', 'driver');
        return view('admin.certificate.edit', compact('certificate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certificate $certificate)
    {
        abort_if(Gate::denies('certificate_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|integer|exists:certificate_categories,id',
            'notify_date' => 'nullable|string',
            'expiry_date' => 'nullable|string',
        ]);

        $certificate->update([
            'name' => $data['name'],
            'category_id' => $data['category_id'] ?? null,
            'notify_date' => $data['notify_date'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
        ]);

        // Respect optional back_to field
        $back = $request->input('back_to');
        if ($back && str_starts_with($back, url('/'))) {
            return redirect($back)->with('success', 'Certificaat bijgewerkt');
        }

        return redirect()->route('admin.certificate.show', $certificate->id)->with('success', 'Certificaat bijgewerkt');
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
            'certificate_ids.*' => 'exists:certificates,id',
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
}
