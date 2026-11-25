<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\CertificateRenewal;
use App\Services\MailTriggerService;
use Carbon\Carbon;

class CertificateRenewalController extends Controller
{
    public function showRenewalForm($token)
    {
        $certificate = Certificate::where('renewal_token', $token)->firstOrFail();

        // Check if token is expired
        if (!$certificate->renewal_token_expires_at || Carbon::now()->gt($certificate->renewal_token_expires_at)) {
            return view('certificate-renewal.expired', compact('certificate'));
        }

        // Check if already renewed
        if ($certificate->isRenewed()) {
            return view('certificate-renewal.already-renewed', compact('certificate'));
        }

        return view('certificate-renewal.form', compact('certificate'));
    }

    public function processRenewal(Request $request, $token)
    {
        $certificate = Certificate::where('renewal_token', $token)->firstOrFail();

        // Check if token is expired
        if (!$certificate->renewal_token_expires_at || Carbon::now()->gt($certificate->renewal_token_expires_at)) {
            return redirect()->back()->with('error', 'Deze verlengingslink is verlopen.');
        }

        // Check if already renewed
        if ($certificate->isRenewed()) {
            return redirect()->back()->with('error', 'Dit certificaat is al verlengd.');
        }

        // Validate
        $request->validate([
            'new_expiry_date' => 'required|date|after:' . $certificate->expiry_date,
            'email' => 'required|email',
        ], [
            'new_expiry_date.required' => 'Nieuwe vervaldatum is verplicht.',
            'new_expiry_date.date' => 'Nieuwe vervaldatum moet een geldige datum zijn.',
            'new_expiry_date.after' => 'Nieuwe vervaldatum moet later zijn dan de huidige vervaldatum.',
            'email.required' => 'Email adres is verplicht.',
            'email.email' => 'Voer een geldig email adres in.',
        ]);

        // Create renewal history record
        CertificateRenewal::create([
            'certificate_id' => $certificate->id,
            'old_expiry_date' => $certificate->expiry_date,
            'new_expiry_date' => $request->new_expiry_date,
            'renewed_by_email' => $request->email,
            'renewal_method' => 'email_link',
        ]);

        // Update certificate
        if (!$certificate->original_expiry_date) {
            $certificate->original_expiry_date = $certificate->expiry_date;
        }
        
        $certificate->expiry_date = $request->new_expiry_date;
        $certificate->renewed_by_email = $request->email;
        $certificate->renewal_token = null;
        $certificate->renewal_token_expires_at = null;
        $certificate->last_notification_sent_at = null; // Reset for new expiry cycle
        $certificate->save();

        // Send renewal confirmation emails
        $recipients = [];
        
        // Add category admin emails
        if ($certificate->category && $certificate->category->notification_recipients) {
            $recipients = array_merge($recipients, $certificate->category->notification_recipients);
        }
        
        // Add driver email
        if ($certificate->driver && $certificate->driver->email) {
            $recipients[] = $certificate->driver->email;
        }

        $recipients = array_unique($recipients);

        if (!empty($recipients)) {
            $mailService = new MailTriggerService();
            $mailService->dispatch('CERTIFICATE_RENEWED', $certificate, [
                'recipients' => $recipients
            ]);
        }

        return view('certificate-renewal.success', compact('certificate'));
    }
}

