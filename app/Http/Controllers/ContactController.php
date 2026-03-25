<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use App\Services\ContactService;
use App\Services\ContactTagService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ContactController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ContactService $contactService,
        protected ContactTagService $tagService
    ) {
    }

    /**
     * Display a listing of contacts
     */
    public function index(Request $request): Response
    {
        $filters = $request->only([
            'status',
            'contact_type',
            'priority',
            'source',
            'assigned_to',
            'tags',
            'search',
            'with_trashed',
            'sort_by',
            'sort_direction'
        ]);

        $contacts = $this->contactService->getPaginatedContacts($filters, 15);
        $statistics = $this->contactService->getStatistics();
        $tags = $this->tagService->getAllTags();
        $users = User::all(['id', 'name']);

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
            'statistics' => $statistics,
            'tags' => $tags,
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new contact
     */
    public function create(): Response
    {
        $this->authorize('create', Contact::class);

        $tags = $this->tagService->getAllTags();
        $users = User::role(['admin', 'agent'])->get(['id', 'name']);

        return Inertia::render('Contacts/Create', [
            'tags' => $tags,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created contact
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Contact::class);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip' => 'nullable|string|max:20',
            'contact_type' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:100',
            'priority' => 'nullable|string|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:contact_tags,id',
            'note' => 'nullable|string',
        ]);

        $this->contactService->createContact($validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Contact created successfully.');
    }

    /**
     * Display the specified contact
     */
    public function show(Contact $contact): Response
    {
        $this->authorize('view', $contact);

        $contact->load(['assignedUser', 'tags', 'notes.user']);

        return Inertia::render('Contacts/Show', [
            'contact' => $contact,
        ]);
    }

    /**
     * Show the form for editing the contact
     */
    public function edit(Contact $contact): Response
    {
        $this->authorize('update', $contact);

        $contact->load(['tags']);
        $tags = $this->tagService->getAllTags();
        $users = User::role(['admin', 'agent'])->get(['id', 'name']);

        return Inertia::render('Contacts/Edit', [
            'contact' => $contact,
            'tags' => $tags,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified contact
     */
    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip' => 'nullable|string|max:20',
            'contact_type' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:100',
            'priority' => 'nullable|string|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:contact_tags,id',
        ]);

        $this->contactService->updateContact($contact, $validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Contact updated successfully.');
    }

    /**
     * Remove the specified contact
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        $this->authorize('delete', $contact);

        $this->contactService->deleteContact($contact);

        return redirect()->route('contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }

    /**
     * Restore a deleted contact
     */
    public function restore(int $id): RedirectResponse
    {
        $this->contactService->restoreContact($id);

        return back()->with('success', 'Contact restored successfully.');
    }

    /**
     * Update contact status
     */
    public function updateStatus(Request $request, Contact $contact): RedirectResponse
    {
        $this->authorize('update', $contact);

        $validated = $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $this->contactService->updateStatus($contact, $validated['status']);

        return back()->with('success', 'Contact status updated successfully.');
    }

    /**
     * Assign contact to user
     */
    public function assign(Request $request, Contact $contact): RedirectResponse
    {
        $this->authorize('assign', $contact);

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $this->contactService->assignContact($contact, $validated['assigned_to']);

        return back()->with('success', 'Contact assigned successfully.');
    }

    /**
     * Bulk delete contacts
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id',
        ]);

        $this->contactService->bulkDelete($validated['contact_ids']);

        return back()->with('success', 'Contacts deleted successfully.');
    }

    /**
     * Bulk assign contacts
     */
    public function bulkAssign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $this->contactService->bulkAssign($validated['contact_ids'], $validated['assigned_to']);

        return back()->with('success', 'Contacts assigned successfully.');
    }

    /**
     * Bulk tag contacts
     */
    public function bulkTag(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id',
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:contact_tags,id',
        ]);

        $this->contactService->bulkTag($validated['contact_ids'], $validated['tag_ids']);

        return back()->with('success', 'Tags applied successfully.');
    }

    /**
     * Bulk update contacts
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id',
            'status' => 'nullable|string|max:50',
            'priority' => 'nullable|string|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
            'contact_type' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:100',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:contact_tags,id',
            'clear_existing_tags' => 'nullable|boolean',
        ]);

        // Remove contact_ids from the data to update
        $contactIds = $validated['contact_ids'];
        unset($validated['contact_ids']);

        // Remove empty values (but keep boolean false values)
        $dataToUpdate = array_filter($validated, function ($value, $key) {
            if ($key === 'clear_existing_tags') {
                return true; // Always include this boolean field
            }
            return $value !== null && $value !== '';
        }, ARRAY_FILTER_USE_BOTH);

        if (empty($dataToUpdate) || (count($dataToUpdate) === 1 && isset($dataToUpdate['clear_existing_tags']))) {
            return back()->withErrors(['error' => 'No fields selected for update.']);
        }

        $this->contactService->bulkUpdate($contactIds, $dataToUpdate);

        return back()->with('success', 'Contacts updated successfully.');
    }

    /**
     * Display pipeline/kanban view
     */
    public function pipeline(): Response
    {
        $pipeline = $this->contactService->getContactsByPipeline();
        $users = User::all(['id', 'name']);

        return Inertia::render('Contacts/Pipeline', [
            'pipeline' => $pipeline,
            'users' => $users,
        ]);
    }
}
