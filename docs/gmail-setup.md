# Gmail SMTP Setup Guide

This guide explains how to configure Gmail for sending emails from the McMullen Properties Client Portal.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Step 1: Enable 2-Factor Authentication](#step-1-enable-2-factor-authentication)
- [Step 2: Generate Gmail App Password](#step-2-generate-gmail-app-password)
- [Step 3: Configure Laravel Application](#step-3-configure-laravel-application)
- [Step 4: Test Email Sending](#step-4-test-email-sending)
- [Switching Between Environments](#switching-between-environments)
- [Common Issues & Troubleshooting](#common-issues--troubleshooting)
- [Security Best Practices](#security-best-practices)

---

## Prerequisites

- Gmail account: **tim.mcmullen.properties@gmail.com**
- Access to the Gmail account settings
- Laravel application with mail configuration

---

## Step 1: Enable 2-Factor Authentication

Gmail requires 2-Factor Authentication (2FA) to generate App Passwords.

### Steps to Enable 2FA:

1. **Go to your Google Account**
   - Visit: https://myaccount.google.com/
   - Sign in with: tim.mcmullen.properties@gmail.com

2. **Navigate to Security**
   - Click on **"Security"** in the left sidebar
   - Or visit directly: https://myaccount.google.com/security

3. **Enable 2-Step Verification**
   - Scroll to **"How you sign in to Google"** section
   - Click on **"2-Step Verification"**
   - Click **"Get Started"**

4. **Follow the Setup Wizard**
   - Enter your password when prompted
   - Add your phone number
   - Choose verification method (text message or phone call)
   - Enter the verification code sent to your phone
   - Click **"Turn On"** to complete

5. **Verify 2FA is Active**
   - You should see "2-Step Verification ON" with a checkmark

---

## Step 2: Generate Gmail App Password

Once 2FA is enabled, you can generate an App Password for the application.

### Steps to Generate App Password:

1. **Access App Passwords Page**
   - Go to: https://myaccount.google.com/apppasswords
   - Or navigate: Google Account → Security → 2-Step Verification → App passwords

2. **Create New App Password**
   - You may need to re-enter your Gmail password
   - In the "Select app" dropdown, choose **"Mail"**
   - In the "Select device" dropdown, choose **"Other (Custom name)"**
   - Enter a descriptive name: **"McMullen Properties Portal"**
   - Click **"Generate"**

3. **Copy the App Password**
   - Google will display a 16-character password (e.g., `abcd efgh ijkl mnop`)
   - **IMPORTANT:** Copy this password immediately!
   - The spaces in the password don't matter - you can remove them
   - You won't be able to see this password again

4. **Keep the Password Secure**
   - Store it in a secure password manager
   - Never commit it to version control
   - Never share it publicly

---

## Step 3: Configure Laravel Application

### Update `.env` File

1. **Open your `.env` file** (NOT `.env.example`)

2. **Update the mail configuration:**

```env
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tim.mcmullen.properties@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tim.mcmullen.properties@gmail.com
MAIL_FROM_NAME="McMullen Properties"
```

3. **Replace `MAIL_PASSWORD` with your actual App Password**
   - Remove all spaces from the App Password
   - Example: `abcd efgh ijkl mnop` becomes `abcdefghijklmnop`

4. **Save the `.env` file**

5. **Clear configuration cache:**
```bash
php artisan config:clear
php artisan cache:clear
```

### Important Configuration Details:

| Setting | Value | Description |
|---------|-------|-------------|
| `MAIL_MAILER` | `smtp` | Use SMTP protocol for sending emails |
| `MAIL_HOST` | `smtp.gmail.com` | Gmail's SMTP server |
| `MAIL_PORT` | `587` | Standard SMTP port with TLS encryption |
| `MAIL_ENCRYPTION` | `tls` | Use TLS encryption (recommended) |
| `MAIL_USERNAME` | Gmail address | Your full Gmail email address |
| `MAIL_PASSWORD` | App Password | 16-character App Password (no spaces) |
| `MAIL_FROM_ADDRESS` | Gmail address | Sender email address (should match username) |
| `MAIL_FROM_NAME` | Company name | Sender name displayed to recipients |

---

## Step 4: Test Email Sending

### Method 1: Using Tinker (Quick Test)

```bash
php artisan tinker
```

Then run:
```php
Mail::raw('Test email from McMullen Properties Portal', function ($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email');
});
```

If successful, you'll see: `= Illuminate\Mail\SentMessage {#...}`

### Method 2: Create a Test Command

Create a test command:
```bash
php artisan make:command SendTestEmail
```

Edit `app/Console/Commands/SendTestEmail.php`:
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    protected $signature = 'email:test {recipient}';
    protected $description = 'Send a test email';

    public function handle()
    {
        $recipient = $this->argument('recipient');

        Mail::raw('This is a test email from McMullen Properties Portal.', function ($message) use ($recipient) {
            $message->to($recipient)
                    ->subject('Test Email - McMullen Properties');
        });

        $this->info("Test email sent to {$recipient}");
    }
}
```

Run the command:
```bash
php artisan email:test your-email@example.com
```

### Method 3: Test Client Invitation Email

The easiest way to test is to invite a client:

1. Log in as an admin
2. Go to **Users → Invite Client**
3. Enter a test email address
4. Check if the invitation email is received

---

## Switching Between Environments

### Local Development (Use Log Driver)

For local development, it's recommended to use the `log` driver to avoid sending real emails:

```env
MAIL_MAILER=log
```

Emails will be logged to: `storage/logs/laravel.log`

You can view the email content without actually sending it.

### Staging/Testing Environment

Use Gmail SMTP but send to test email addresses only:

```env
MAIL_MAILER=smtp
# ... rest of Gmail configuration
```

### Production Environment

Use Gmail SMTP with the real App Password:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tim.mcmullen.properties@gmail.com
MAIL_PASSWORD=your_actual_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tim.mcmullen.properties@gmail.com
MAIL_FROM_NAME="McMullen Properties"
```

### Quick Switch Commands

```bash
# Switch to log (local development)
php artisan config:clear && echo "MAIL_MAILER=log" >> .env

# Switch to smtp (production)
php artisan config:clear && echo "MAIL_MAILER=smtp" >> .env
```

**Note:** Always clear config cache after changing `.env` values:
```bash
php artisan config:clear
```

---

## Common Issues & Troubleshooting

### Issue 1: "Invalid credentials" or "Authentication failed"

**Possible Causes:**
- Using regular Gmail password instead of App Password
- App Password has spaces in it
- 2FA is not enabled

**Solution:**
1. Verify 2FA is enabled on the Gmail account
2. Generate a new App Password
3. Copy the password without spaces
4. Update `.env` and clear config cache

---

### Issue 2: "Connection could not be established with host smtp.gmail.com"

**Possible Causes:**
- Firewall blocking port 587
- Incorrect SMTP host or port
- Internet connection issues

**Solution:**
1. Check MAIL_HOST is `smtp.gmail.com`
2. Check MAIL_PORT is `587`
3. Check MAIL_ENCRYPTION is `tls`
4. Test connection: `telnet smtp.gmail.com 587`
5. Check firewall settings

---

### Issue 3: "Less secure app access" error

**This should NOT happen with App Passwords**, but if you see this:

**Solution:**
- You must use an App Password, NOT your regular Gmail password
- Regular passwords no longer work for SMTP
- Follow Step 2 to generate an App Password

---

### Issue 4: Gmail sending limit reached

Gmail has sending limits:
- **Free Gmail accounts:** 500 emails per day
- **Google Workspace accounts:** 2,000 emails per day

**Solution:**
1. Monitor your daily email volume
2. Consider implementing email throttling
3. Use Laravel's queue system to spread out emails
4. For high volume, consider a dedicated email service (SendGrid, Mailgun, etc.)

---

### Issue 5: Emails going to spam

**Possible Causes:**
- SPF/DKIM records not configured
- Sending volume too high
- Email content flagged as spam

**Solution:**
1. Check spam folder
2. Ask recipients to whitelist your email
3. Avoid spam trigger words in subject/body
4. Configure SPF and DKIM records for your domain
5. Consider using a custom domain instead of Gmail

---

### Issue 6: "Failed to authenticate on SMTP server"

**Possible Causes:**
- App Password expired or revoked
- Gmail account locked
- Too many failed login attempts

**Solution:**
1. Verify the Gmail account is active
2. Generate a new App Password
3. Check if the account has any security alerts
4. Wait 15 minutes if there were multiple failed attempts

---

## Security Best Practices

### 1. Never Commit Credentials

**DO NOT:**
- Commit `.env` file to Git
- Hardcode passwords in config files
- Share App Passwords in Slack, email, etc.

**DO:**
- Use `.env.example` as a template
- Store App Passwords in secure password managers
- Use different App Passwords for different environments

---

### 2. Protect `.env` File

Ensure `.env` is in `.gitignore`:
```
.env
.env.backup
.env.production
```

Check your `.gitignore`:
```bash
grep ".env" .gitignore
```

---

### 3. Rotate App Passwords Regularly

- Regenerate App Passwords every 6-12 months
- Revoke old App Passwords after updating
- Keep a record of when passwords were last changed

---

### 4. Monitor Email Activity

- Check Gmail's "Recent security activity" regularly
- Set up alerts for suspicious login attempts
- Review sent emails in Gmail to verify they're legitimate

---

### 5. Limit Access

- Only give access to developers who need it
- Use separate Gmail accounts for different applications if possible
- Consider using Google Workspace for better access control

---

## Additional Resources

- [Gmail SMTP Documentation](https://support.google.com/mail/answer/7126229)
- [Google App Passwords Help](https://support.google.com/accounts/answer/185833)
- [Laravel Mail Documentation](https://laravel.com/docs/11.x/mail)
- [Laravel Mail Configuration](https://laravel.com/docs/11.x/mail#configuration)

---

## Quick Reference

### Gmail SMTP Settings Summary

```
SMTP Server: smtp.gmail.com
Port: 587 (TLS) or 465 (SSL)
Encryption: TLS (recommended)
Username: tim.mcmullen.properties@gmail.com
Password: [16-character App Password]
```

### Important Commands

```bash
# Clear config cache
php artisan config:clear

# Send test email
php artisan tinker
Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });

# View mail configuration
php artisan config:show mail

# Check email logs (when using log driver)
tail -f storage/logs/laravel.log
```

---

## Support

If you encounter issues not covered in this guide:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Gmail security settings and recent activity
3. Verify 2FA is enabled and App Password is correct
4. Try generating a new App Password
5. Contact the development team for assistance

---

**Last Updated:** March 25, 2026
**Maintained By:** Development Team
