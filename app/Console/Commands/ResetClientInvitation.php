<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ResetClientInvitation extends Command
{
    protected $signature = 'invitation:reset {email}';
    protected $description = 'Reset a client to pending invitation status';

    public function handle()
    {
        $email = $this->argument('email');

        $this->info("Looking for client with email: {$email}");

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No user found with email: {$email}");
            return Command::FAILURE;
        }

        if (!$user->hasRole('client')) {
            $this->error("User {$email} is not a client!");
            return Command::FAILURE;
        }

        // Generate new invitation token
        $token = Str::random(64);

        // Reset to pending invitation state
        $user->update([
            'invitation_token' => $token,
            'invitation_sent_at' => now(),
            'invitation_accepted_at' => null,
            'email_verified_at' => null,
        ]);

        $this->newLine();
        $this->info('✓ Client invitation reset successfully!');
        $this->line("  Client: {$user->name}");
        $this->line("  Email: {$user->email}");
        $this->line("  Status: Pending Invitation");
        $this->newLine();
        $this->info('The client now appears as having a pending invitation.');
        $this->info('You can now click "Resend Invite" in the admin panel.');

        return Command::SUCCESS;
    }
}
