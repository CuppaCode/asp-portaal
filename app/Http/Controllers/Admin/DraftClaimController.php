<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Notifications\ClaimCreation;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;

class DraftClaimController extends Controller
{
    public function approve(Request $request, Claim $claim)
    {
        abort_if(Gate::denies('approve_draft_claim', $claim), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Ensure claim is in draft status
        if ($claim->status !== 'draft') {
            return response()->json(['error' => 'Deze claim is geen concept meer.'], 400);
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

        return response()->json(['success' => true, 'message' => 'Concept claim goedgekeurd.']);
    }

    public function deny(Request $request, Claim $claim)
    {
        abort_if(Gate::denies('approve_draft_claim', $claim), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Ensure claim is in draft status
        if ($claim->status !== 'draft') {
            return redirect()->back()->with('error', 'Deze claim is geen concept meer.');
        }

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        // Update claim status
        $claim->update([
            'status' => 'draft_denied',
            'denied_reason' => $request->input('reason'),
        ]);

        return redirect()->route('admin.claims.show', $claim->id)
            ->with('message', 'Concept claim afgewezen.');
    }

    public function resubmit(Request $request, Claim $claim)
    {
        abort_if(Gate::denies('approve_draft_claim', $claim), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Ensure claim is in denied status
        if ($claim->status !== 'draft_denied') {
            return redirect()->back()->with('error', 'Deze claim kan niet opnieuw ingediend worden.');
        }

        // Calculate new expiry date
        $draftExpiresAt = now()->addDays($claim->company->draft_expiry_days ?? 30);

        // Reset to draft status
        $claim->update([
            'status' => 'draft',
            'draft_expires_at' => $draftExpiresAt,
            'last_reminder_sent_at' => null,
            'denied_reason' => null,
        ]);

        return redirect()->route('admin.claims.edit', $claim->id)
            ->with('message', 'Claim opnieuw ingediend. U kunt nu aanpassingen maken.');
    }

    public function showDenyForm(Request $request, Claim $claim)
    {
        // For public (email) access with signed URL
        if (!auth()->check()) {
            return view('public.draft-deny-form', compact('claim'));
        }

        // For authenticated users, show modal or form in admin area
        return view('admin.claims.deny-draft', compact('claim'));
    }
}
