<?php

namespace App\Services;

use App\Models\User;
use App\Mail\ClientInvitationMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ClientInvitationService
{
    /**
     * Create a client invitation
     */
    public function createInvitation(array $data): User
    {
        // Generate unique invitation token
        $token = Str::random(64);

        // Create the client user with pending invitation
        $client = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(32)), // Temporary password
            'invitation_token' => $token,
            'invitation_sent_at' => now(),
            'is_approved' => true, // Auto-approve clients
            'is_suspended' => false,
            'email_verified_at' => now(), // Auto-verify clients
        ]);

        // Assign client role
        $client->assignRole('client');

        // Send invitation email
        $this->sendInvitationEmail($client, $token);

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($client)
            ->log('Client invited');

        return $client;
    }

    /**
     * Send invitation email
     */
    protected function sendInvitationEmail(User $client, string $token, bool $isResend = false): void
    {
        $invitationUrl = URL::temporarySignedRoute(
            'client.invitation.accept',
            now()->addDays(7),
            ['token' => $token]
        );

        Mail::to($client->email)->send(
            new ClientInvitationMail(
                clientName: $client->name,
                invitationUrl: $invitationUrl,
                isResend: $isResend
            )
        );

        // Also log for debugging purposes
        logger()->info("Client invitation email sent to {$client->email}", [
            'client_id' => $client->id,
            'is_resend' => $isResend,
        ]);
    }

    /**
     * Accept invitation and set password
     */
    public function acceptInvitation(string $token, string $password): User
    {
        $client = User::where('invitation_token', $token)->firstOrFail();

        if (!$client->hasPendingInvitation()) {
            throw new \Exception('This invitation has already been accepted or is invalid.');
        }

        // Set the password and mark invitation as accepted
        $client->update([
            'password' => Hash::make($password),
            'invitation_accepted_at' => now(),
            'invitation_token' => null, // Clear the token
            'email_verified_at' => now(), // Mark email as verified
        ]);

        // Log activity
        activity()
            ->performedOn($client)
            ->log('Client accepted invitation');

        return $client;
    }

    /**
     * Resend invitation
     */
    public function resendInvitation(User $client): void
    {
        if (!$client->isClient()) {
            throw new \Exception('User is not a client.');
        }

        // Generate new token
        $token = Str::random(64);

        $client->update([
            'invitation_token' => $token,
            'invitation_sent_at' => now(),
        ]);

        // Send invitation email with resend flag
        $this->sendInvitationEmail($client, $token, isResend: true);

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($client)
            ->log('Client invitation resent');
    }

    /**
     * Cancel invitation
     */
    public function cancelInvitation(User $client): void
    {
        if (!$client->isClient()) {
            throw new \Exception('User is not a client.');
        }

        $client->update([
            'invitation_token' => null,
            'invitation_sent_at' => null,
        ]);

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($client)
            ->log('Client invitation cancelled');
    }
}
