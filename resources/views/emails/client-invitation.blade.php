<x-mail::message>
# {{ $isResend ? 'Reminder: Complete Your Registration' : 'Welcome to McMullen Properties' }}

Hello {{ $clientName }},

@if($isResend)
This is a friendly reminder to complete your registration for the McMullen Properties Client Portal.
@else
You have been invited to join the McMullen Properties Client Portal. We're excited to have you as a client!
@endif

To get started, please click the button below to set up your account password and complete your registration:

<x-mail::button :url="$invitationUrl">
{{ $isResend ? 'Complete Registration' : 'Accept Invitation' }}
</x-mail::button>

**Important:** This invitation link will expire in 7 days. If you don't complete your registration within this time, please contact us for a new invitation.

Once you've completed your registration, you'll have access to:
- Your personalized client dashboard
- Important documents and resources
- Direct communication with your property manager
- Activity history and updates

If you have any questions or need assistance, please don't hesitate to reach out to us.

Thanks,<br>
{{ config('app.name') }}

---

<small>If you did not expect this invitation, please ignore this email or contact us if you have concerns.</small>
</x-mail::message>
