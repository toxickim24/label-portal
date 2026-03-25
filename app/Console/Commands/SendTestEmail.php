<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {recipient}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify Gmail SMTP configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $recipient = $this->argument('recipient');

        $this->info('Sending test email to: ' . $recipient);
        $this->info('Using mailer: ' . config('mail.default'));
        $this->info('SMTP Host: ' . config('mail.mailers.smtp.host'));
        $this->info('From: ' . config('mail.from.address'));
        $this->newLine();

        try {
            Mail::raw(
                "This is a test email from McMullen Properties Client Portal.\n\n" .
                "If you received this email, your Gmail SMTP configuration is working correctly!\n\n" .
                "Configuration Details:\n" .
                "- Mailer: " . config('mail.default') . "\n" .
                "- SMTP Host: " . config('mail.mailers.smtp.host') . "\n" .
                "- SMTP Port: " . config('mail.mailers.smtp.port') . "\n" .
                "- Encryption: " . config('mail.mailers.smtp.encryption') . "\n" .
                "- From: " . config('mail.from.address') . "\n\n" .
                "Sent at: " . now()->format('Y-m-d H:i:s'),
                function ($message) use ($recipient) {
                    $message->to($recipient)
                            ->subject('Test Email - McMullen Properties Portal');
                }
            );

            $this->newLine();
            $this->info('✓ Test email sent successfully!');
            $this->info('Please check the inbox of: ' . $recipient);
            $this->newLine();
            $this->warn('Note: Check spam folder if you don\'t see it in inbox.');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('✗ Failed to send test email!');
            $this->newLine();
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Common issues:');
            $this->line('  1. Check if MAIL_PASSWORD in .env is correct (Gmail App Password)');
            $this->line('  2. Verify 2FA is enabled on Gmail account');
            $this->line('  3. Make sure MAIL_MAILER=smtp in .env');
            $this->line('  4. Run: php artisan config:clear');
            $this->newLine();
            $this->info('For detailed troubleshooting, see: docs/gmail-setup.md');

            return Command::FAILURE;
        }
    }
}
