<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ClientInvitationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestClientInvitation extends Command
{
    protected $signature = 'invitation:test {action=list} {email?}';
    protected $description = 'Test client invitation functionality';

    public function handle(ClientInvitationService $invitationService)
    {
        $action = $this->argument('action');
        $email = $this->argument('email');

        match ($action) {
            'list' => $this->listPendingInvitations(),
            'create' => $this->createTestInvitation($invitationService, $email),
            'resend' => $this->resendInvitation($invitationService, $email),
            default => $this->error("Unknown action: {$action}. Use: list, create, or resend"),
        };

        return Command::SUCCESS;
    }

    protected function listPendingInvitations()
    {
        $this->info('Checking for clients with pending invitations...');
        $this->newLine();

        $clients = User::role('client')
            ->whereNotNull('invitation_token')
            ->whereNull('invitation_accepted_at')
            ->get(['id', 'name', 'email', 'invitation_sent_at', 'created_at']);

        if ($clients->isEmpty()) {
            $this->warn('No clients with pending invitations found.');
            $this->newLine();
            $this->info('To create a test invitation, run:');
            $this->line('  php artisan invitation:test create test@example.com');
            return;
        }

        $this->info("Found {$clients->count()} client(s) with pending invitations:");
        $this->newLine();

        $tableData = $clients->map(function ($client) {
            return [
                'ID' => $client->id,
                'Name' => $client->name,
                'Email' => $client->email,
                'Invitation Sent' => $client->invitation_sent_at?->diffForHumans() ?? 'N/A',
            ];
        })->toArray();

        $this->table(
            ['ID', 'Name', 'Email', 'Invitation Sent'],
            $tableData
        );

        $this->newLine();
        $this->info('To resend an invitation, run:');
        $this->line('  php artisan invitation:test resend EMAIL');
    }

    protected function createTestInvitation(ClientInvitationService $invitationService, ?string $email)
    {
        if (!$email) {
            $this->error('Email address is required.');
            $this->line('Usage: php artisan invitation:test create EMAIL');
            return;
        }

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return;
        }

        $this->info("Creating test client invitation for: {$email}");

        // Set authenticated user for activity logging
        $admin = User::role('admin')->first();
        if (!$admin) {
            $this->error('No admin user found to act as invitation sender.');
            return;
        }
        Auth::login($admin);

        try {
            $client = $invitationService->createInvitation([
                'name' => 'Test Client',
                'email' => $email,
            ]);

            $this->newLine();
            $this->info('✓ Test client created successfully!');
            $this->line("  Client ID: {$client->id}");
            $this->line("  Name: {$client->name}");
            $this->line("  Email: {$client->email}");
            $this->line("  Has pending invitation: " . ($client->hasPendingInvitation() ? 'Yes' : 'No'));
            $this->newLine();
            $this->info('Invitation email queued for sending.');
            $this->warn('Note: Since emails are queued, make sure queue worker is running:');
            $this->line('  php artisan queue:work');

        } catch (\Exception $e) {
            $this->error('Failed to create invitation: ' . $e->getMessage());
        } finally {
            Auth::logout();
        }
    }

    protected function resendInvitation(ClientInvitationService $invitationService, ?string $email)
    {
        if (!$email) {
            $this->error('Email address is required.');
            $this->line('Usage: php artisan invitation:test resend EMAIL');
            return;
        }

        $this->info("Looking for client with email: {$email}");

        $client = User::where('email', $email)->first();

        if (!$client) {
            $this->error("No user found with email: {$email}");
            return;
        }

        if (!$client->hasRole('client')) {
            $this->error("User {$email} is not a client!");
            return;
        }

        if (!$client->hasPendingInvitation()) {
            $this->warn("Client {$email} doesn't have a pending invitation.");
            $this->line('Invitation status:');
            $this->line("  - Has invitation token: " . ($client->invitation_token ? 'Yes' : 'No'));
            $this->line("  - Invitation accepted: " . ($client->invitation_accepted_at ? 'Yes' : 'No'));
            return;
        }

        // Set authenticated user for activity logging
        $admin = User::role('admin')->first();
        if (!$admin) {
            $this->error('No admin user found to act as invitation sender.');
            return;
        }
        Auth::login($admin);

        try {
            $this->info('Resending invitation...');
            $invitationService->resendInvitation($client);

            $this->newLine();
            $this->info('✓ Invitation resent successfully!');
            $this->line("  Client: {$client->name}");
            $this->line("  Email: {$client->email}");
            $this->newLine();
            $this->info('Invitation email queued for sending.');
            $this->warn('Note: Since emails are queued, make sure queue worker is running:');
            $this->line('  php artisan queue:work');
            $this->newLine();
            $this->info('Check the queue worker output or laravel.log for email sending status.');

        } catch (\Exception $e) {
            $this->error('Failed to resend invitation: ' . $e->getMessage());
        } finally {
            Auth::logout();
        }
    }
}
