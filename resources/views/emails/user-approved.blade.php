<x-mail::message>
# Account Approved

Hello {{ $user->name }},

Great news! Your account has been approved by {{ $approver->name }}.

You now have full access to the {{ config('app.name') }} system and can start using all available features.

<x-mail::button :url="route('login')">
Login to Your Account
</x-mail::button>

If you have any questions or need assistance getting started, please don't hesitate to reach out to our support team.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
