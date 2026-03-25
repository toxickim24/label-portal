<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptInvitationRequest;
use App\Models\User;
use App\Services\ClientInvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ClientInvitationController extends Controller
{
    public function __construct(
        protected ClientInvitationService $invitationService
    ) {}

    /**
     * Show the invitation acceptance page
     */
    public function show(Request $request, string $token): Response
    {
        // Verify the signed URL
        if (!$request->hasValidSignature()) {
            abort(403, 'This invitation link has expired or is invalid.');
        }

        // Find user by invitation token
        $client = User::where('invitation_token', $token)->first();

        if (!$client || !$client->hasPendingInvitation()) {
            abort(404, 'This invitation is invalid or has already been accepted.');
        }

        return Inertia::render('Client/AcceptInvitation', [
            'token' => $token,
            'email' => $client->email,
            'name' => $client->name,
        ]);
    }

    /**
     * Process the invitation acceptance
     */
    public function accept(AcceptInvitationRequest $request): RedirectResponse
    {
        try {
            $client = $this->invitationService->acceptInvitation(
                $request->token,
                $request->password
            );

            // Log the client in
            Auth::login($client);

            return redirect()->route('client.dashboard')
                ->with('success', 'Welcome! Your account has been activated.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
