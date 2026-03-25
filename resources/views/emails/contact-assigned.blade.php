<x-mail::message>
# New Contact Assigned

Hello {{ $agent->name }},

A new contact has been assigned to you by {{ $assignedBy->name }}.

## Contact Details

- **Name:** {{ $contact->first_name }} {{ $contact->last_name }}
- **Email:** {{ $contact->email ?? 'Not provided' }}
- **Phone:** {{ $contact->phone ?? 'Not provided' }}
- **Type:** {{ ucfirst($contact->contact_type) }}
- **Status:** {{ ucfirst($contact->status) }}
@if($contact->source)
- **Source:** {{ ucfirst($contact->source) }}
@endif

<x-mail::button :url="route('contacts.show', $contact)">
View Contact
</x-mail::button>

You can now manage this contact, add notes, and update their status in the CRM system.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
