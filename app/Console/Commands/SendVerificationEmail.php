<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\EmailVerificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendVerificationEmail extends Command
{
    protected $signature = 'email:send-verification {email}';
    protected $description = 'Unverify and send verification email to a user';

    public function handle()
    {
        $email = $this->argument('email');

        $this->info("Looking for user with email: {$email}");

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No user found with email: {$email}");
            return Command::FAILURE;
        }

        // Unverify the user's email
        $user->update([
            'email_verified_at' => null,
        ]);

        $this->info("✓ Email unverified for: {$user->name}");
        $this->newLine();

        // Generate verification URL with signature (expires in 60 minutes)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        // Send verification email
        Mail::to($user->email)->send(
            new EmailVerificationMail(
                userName: $user->name,
                verificationUrl: $verificationUrl
            )
        );

        $this->info('✓ Verification email queued for sending!');
        $this->line("  User: {$user->name}");
        $this->line("  Email: {$user->email}");
        $this->newLine();
        $this->warn('Note: Since emails are queued, make sure queue worker is running:');
        $this->line('  php artisan queue:work');
        $this->newLine();
        $this->info('The verification link will expire in 60 minutes.');

        return Command::SUCCESS;
    }
}
