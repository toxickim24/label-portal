<x-mail::message>
# Reset Your Password

Hello {{ $userName }},

You are receiving this email because we received a password reset request for your account.

<x-mail::button :url="$resetUrl">
Reset Password
</x-mail::button>

**This password reset link will expire in 60 minutes.**

If you did not request a password reset, no further action is required. Your password will remain unchanged.

For security reasons, if you continue to receive password reset emails that you did not request, please contact support immediately.

Thanks,<br>
{{ config('app.name') }}

---

<small>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</small>
<small>{{ $resetUrl }}</small>
</x-mail::message>
