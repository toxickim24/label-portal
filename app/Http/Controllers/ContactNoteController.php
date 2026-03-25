<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactNote;
use App\Services\ContactNoteService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ContactNoteController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ContactNoteService $noteService
    ) {
    }

    /**
     * Store a newly created note
     */
    public function store(Request $request, Contact $contact): RedirectResponse
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'content' => 'required|string',
            'is_pinned' => 'boolean',
        ]);

        $this->noteService->createNote($contact, $validated);

        return back()->with('success', 'Note added successfully.');
    }

    /**
     * Update the specified note
     */
    public function update(Request $request, ContactNote $note): RedirectResponse
    {
        $this->authorize('update', $note->contact);

        $validated = $request->validate([
            'content' => 'required|string',
            'is_pinned' => 'boolean',
        ]);

        $this->noteService->updateNote($note, $validated);

        return back()->with('success', 'Note updated successfully.');
    }

    /**
     * Toggle pin status
     */
    public function togglePin(ContactNote $note): JsonResponse
    {
        $this->authorize('update', $note->contact);

        $updatedNote = $this->noteService->togglePin($note);

        return response()->json([
            'success' => true,
            'note' => $updatedNote,
        ]);
    }

    /**
     * Remove the specified note
     */
    public function destroy(ContactNote $note): RedirectResponse
    {
        $this->authorize('update', $note->contact);

        $this->noteService->deleteNote($note);

        return back()->with('success', 'Note deleted successfully.');
    }
}
