<x-mail::message>
# Verify Your Email Address

Hello {{ $userName }},

Thank you for registering with McMullen Properties Client Portal. To complete your registration and access your account, please verify your email address by clicking the button below:

<x-mail::button :url="$verificationUrl">
Verify Email Address
</x-mail::button>

**Important:** This verification link will expire in 60 minutes. If you don't verify your email within this time, you may need to request a new verification link.

If you did not create an account with McMullen Properties, please ignore this email and no action is required.

Thanks,<br>
{{ config('app.name') }}

---

<small>If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:</small>
<small>{{ $verificationUrl }}</small>
</x-mail::message>
