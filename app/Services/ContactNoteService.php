<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\ContactNote;
use Illuminate\Support\Collection;

class ContactNoteService
{
    /**
     * Get all notes for a contact
     */
    public function getContactNotes(Contact $contact): Collection
    {
        return $contact->notes()
            ->with('user')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create a new note
     */
    public function createNote(Contact $contact, array $data): ContactNote
    {
        // Update last contact date
        $contact->update(['last_contact_date' => now()]);

        return $contact->notes()->create([
            'user_id' => auth()->id(),
            'content' => $data['content'],
            'is_pinned' => $data['is_pinned'] ?? false,
        ]);
    }

    /**
     * Update a note
     */
    public function updateNote(ContactNote $note, array $data): ContactNote
    {
        $note->update([
            'content' => $data['content'] ?? $note->content,
            'is_pinned' => $data['is_pinned'] ?? $note->is_pinned,
        ]);

        return $note->fresh(['user']);
    }

    /**
     * Toggle pin status
     */
    public function togglePin(ContactNote $note): ContactNote
    {
        $note->update(['is_pinned' => !$note->is_pinned]);
        return $note->fresh();
    }

    /**
     * Delete a note
     */
    public function deleteNote(ContactNote $note): bool
    {
        return $note->delete();
    }
}
