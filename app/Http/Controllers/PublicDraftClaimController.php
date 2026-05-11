<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Notifications\ClaimCreation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class PublicDraftClaimController extends Controller
{
    public function approve(Request $request, Claim $claim)
    {
        // Ensure claim is in draft status
        if ($claim->status !== 'draft') {
            return view('public.draft-error', [
                'message' => 'Deze claim is geen concept meer en kan niet worden goedgekeurd.'
            ]);
        }

        // Update claim status
        $claim->update([
            'status' => 'new',
            'draft_expires_at' => null,
            'last_reminder_sent_at' => null,
        ]);

        // Send standard claim creation notification
        Notification::route('mail', 'patrick@autoschadeplan.nl')
            ->notify(new ClaimCreation($claim));

        return view('public.draft-approved', compact('claim'));
    }

    public function showDenyForm(Request $request, Claim $claim)
    {
        // Ensure claim is in draft status
        if ($claim->status !== 'draft') {
            return view('public.draft-error', [
                'message' => 'Deze claim is geen concept meer.'
            ]);
        }

        return view('public.draft-deny-form', compact('claim'));
    }

    public function deny(Request $request, Claim $claim)
    {
        // Ensure claim is in draft status
        if ($claim->status !== 'draft') {
            return view('public.draft-error', [
                'message' => 'Deze claim is geen concept meer en kan niet worden afgewezen.'
            ]);
        }

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        // Update claim status
        $claim->update([
            'status' => 'draft_denied',
            'denied_reason' => $request->input('reason'),
        ]);

        return view('public.draft-denied', compact('claim'));
    }
}
