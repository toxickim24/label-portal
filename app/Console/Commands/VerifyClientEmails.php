<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyClientEmails extends Command
{
    protected $signature = 'clients:verify-emails';
    protected $description = 'Mark accepted client invitations as email verified';

    public function handle()
    {
        $this->info('Finding clients who accepted invitations but email not verified...');

        // Find clients who accepted invitation but email_verified_at is null
        $clients = User::role('client')
            ->whereNotNull('invitation_accepted_at')
            ->whereNull('email_verified_at')
            ->get();

        if ($clients->isEmpty()) {
            $this->info('No clients found that need email verification.');
            return Command::SUCCESS;
        }

        $this->info("Found {$clients->count()} client(s) to verify.");
        $this->newLine();

        foreach ($clients as $client) {
            $client->update([
                'email_verified_at' => $client->invitation_accepted_at ?? now(),
            ]);

            $this->line("✓ Verified email for: {$client->name} ({$client->email})");
        }

        $this->newLine();
        $this->info("Successfully verified {$clients->count()} client email(s)!");

        return Command::SUCCESS;
    }
}
