<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactService
{
    /**
     * Get paginated contacts with filters
     */
    public function getPaginatedContacts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Contact::with(['assignedUser', 'tags', 'notes']);

        // Filter by status
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by contact type
        if (isset($filters['contact_type']) && !empty($filters['contact_type'])) {
            $query->where('contact_type', $filters['contact_type']);
        }

        // Filter by priority
        if (isset($filters['priority']) && !empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Filter by source
        if (isset($filters['source']) && !empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        // Filter by assigned agent
        if (isset($filters['assigned_to']) && !empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        // Filter by tags
        if (isset($filters['tags']) && !empty($filters['tags'])) {
            $tagIds = is_array($filters['tags']) ? $filters['tags'] : [$filters['tags']];
            $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('contact_tags.id', $tagIds);
            });
        }

        // Search
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Include trashed
        if (isset($filters['with_trashed']) && $filters['with_trashed']) {
            $query->withTrashed();
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Create a new contact
     */
    public function createContact(array $data): Contact
    {
        DB::beginTransaction();
        try {
            $contact = Contact::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'zip' => $data['zip'] ?? null,
                'contact_type' => $data['contact_type'] ?? null,
                'status' => $data['status'] ?? 'lead',
                'source' => $data['source'] ?? null,
                'priority' => $data['priority'] ?? 'medium',
                'assigned_to' => $data['assigned_to'] ?? null,
                'last_contact_date' => $data['last_contact_date'] ?? now(),
            ]);

            // Attach tags if provided
            if (isset($data['tags']) && is_array($data['tags'])) {
                $contact->tags()->sync($data['tags']);
            }

            // Add initial note if provided
            if (isset($data['note']) && !empty($data['note'])) {
                $contact->notes()->create([
                    'user_id' => auth()->id(),
                    'content' => $data['note'],
                    'is_pinned' => false,
                ]);
            }

            DB::commit();
            return $contact->fresh(['assignedUser', 'tags', 'notes']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update contact
     */
    public function updateContact(Contact $contact, array $data): Contact
    {
        DB::beginTransaction();
        try {
            $contact->update([
                'first_name' => $data['first_name'] ?? $contact->first_name,
                'last_name' => $data['last_name'] ?? $contact->last_name,
                'email' => $data['email'] ?? $contact->email,
                'phone' => $data['phone'] ?? $contact->phone,
                'address' => $data['address'] ?? $contact->address,
                'city' => $data['city'] ?? $contact->city,
                'state' => $data['state'] ?? $contact->state,
                'zip' => $data['zip'] ?? $contact->zip,
                'contact_type' => $data['contact_type'] ?? $contact->contact_type,
                'status' => $data['status'] ?? $contact->status,
                'source' => $data['source'] ?? $contact->source,
                'priority' => $data['priority'] ?? $contact->priority,
                'assigned_to' => $data['assigned_to'] ?? $contact->assigned_to,
            ]);

            // Sync tags if provided
            if (isset($data['tags'])) {
                $contact->tags()->sync($data['tags']);
            }

            DB::commit();
            return $contact->fresh(['assignedUser', 'tags', 'notes']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update contact status
     */
    public function updateStatus(Contact $contact, string $status): Contact
    {
        $oldStatus = $contact->status;

        $contact->update([
            'status' => $status,
            'last_contact_date' => now(),
        ]);

        // Create client activity and notification if status changed and contact has client user
        if ($oldStatus !== $status && $contact->email) {
            $clientUser = \App\Models\User::where('email', $contact->email)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'client');
                })
                ->first();

            if ($clientUser) {
                // Log activity
                app(\App\Services\ClientActivityService::class)->logStatusChange(
                    $clientUser,
                    $oldStatus,
                    $status,
                    auth()->user()->name
                );

                // Create notification
                app(\App\Services\ClientNotificationService::class)->notifyStatusChange(
                    $clientUser,
                    $oldStatus,
                    $status,
                    auth()->user()->name
                );
            }
        }

        return $contact->fresh();
    }

    /**
     * Assign contact to user
     */
    public function assignContact(Contact $contact, int $userId): Contact
    {
        $previousAssignee = $contact->assigned_to;

        $contact->update([
            'assigned_to' => $userId,
        ]);

        $contact->load(['assignedUser']);

        // Send email notification to the new assignee (only if changed)
        if ($previousAssignee !== $userId && $contact->assignedUser) {
            \Illuminate\Support\Facades\Mail::to($contact->assignedUser->email)->send(
                new \App\Mail\ContactAssignedMail(
                    contact: $contact,
                    agent: $contact->assignedUser,
                    assignedBy: auth()->user()
                )
            );
        }

        return $contact;
    }

    /**
     * Delete contact (soft delete)
     */
    public function deleteContact(Contact $contact): bool
    {
        return $contact->delete();
    }

    /**
     * Restore contact
     */
    public function restoreContact(int $contactId): bool
    {
        $contact = Contact::withTrashed()->findOrFail($contactId);
        return $contact->restore();
    }

    /**
     * Permanently delete contact
     */
    public function forceDeleteContact(Contact $contact): bool
    {
        return $contact->forceDelete();
    }

    /**
     * Bulk delete contacts
     */
    public function bulkDelete(array $contactIds): int
    {
        return Contact::whereIn('id', $contactIds)->delete();
    }

    /**
     * Bulk assign contacts
     */
    public function bulkAssign(array $contactIds, int $userId): int
    {
        return Contact::whereIn('id', $contactIds)->update([
            'assigned_to' => $userId,
        ]);
    }

    /**
     * Bulk tag contacts
     */
    public function bulkTag(array $contactIds, array $tagIds): void
    {
        foreach ($contactIds as $contactId) {
            $contact = Contact::find($contactId);
            if ($contact) {
                $contact->tags()->syncWithoutDetaching($tagIds);
            }
        }
    }

    /**
     * Bulk update contacts with multiple fields
     */
    public function bulkUpdate(array $contactIds, array $data): int
    {
        DB::beginTransaction();
        try {
            // Prepare update data - only include non-null values
            $updateData = [];

            if (isset($data['status'])) {
                $updateData['status'] = $data['status'];
            }

            if (isset($data['priority'])) {
                $updateData['priority'] = $data['priority'];
            }

            if (isset($data['assigned_to'])) {
                $updateData['assigned_to'] = $data['assigned_to'];
            }

            if (isset($data['contact_type'])) {
                $updateData['contact_type'] = $data['contact_type'];
            }

            if (isset($data['source'])) {
                $updateData['source'] = $data['source'];
            }

            // Update basic fields if any
            $count = 0;
            if (!empty($updateData)) {
                $count = Contact::whereIn('id', $contactIds)->update($updateData);
            }

            // Handle tags separately
            $clearExistingTags = isset($data['clear_existing_tags']) && $data['clear_existing_tags'] === true;
            $tagIds = isset($data['tag_ids']) && is_array($data['tag_ids']) ? $data['tag_ids'] : [];

            if ($clearExistingTags || !empty($tagIds)) {
                foreach ($contactIds as $contactId) {
                    $contact = Contact::find($contactId);
                    if ($contact) {
                        if ($clearExistingTags) {
                            // If clear is enabled, sync (replace) tags instead of adding
                            $contact->tags()->sync($tagIds); // Empty array clears all, non-empty replaces
                        } elseif (!empty($tagIds)) {
                            // If clear is not enabled, add without removing existing
                            $contact->tags()->syncWithoutDetaching($tagIds);
                        }
                    }
                }
            }

            DB::commit();
            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get contact statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Contact::count(),
            'lead' => Contact::where('status', 'lead')->count(),
            'prospect' => Contact::where('status', 'prospect')->count(),
            'active' => Contact::where('status', 'active')->count(),
            'closed' => Contact::where('status', 'closed')->count(),
            'by_type' => Contact::select('contact_type', DB::raw('count(*) as count'))
                ->groupBy('contact_type')
                ->get()
                ->pluck('count', 'contact_type'),
            'unassigned' => Contact::whereNull('assigned_to')->count(),
        ];
    }

    /**
     * Get contacts by status for pipeline view
     */
    public function getContactsByPipeline(): array
    {
        $statuses = ['lead', 'contacted', 'qualified', 'showing', 'offer', 'contract', 'closed', 'lost'];
        $pipeline = [];

        foreach ($statuses as $status) {
            $pipeline[$status] = Contact::with(['assignedUser', 'tags'])
                ->where('status', $status)
                ->orderBy('priority', 'desc')
                ->orderBy('last_contact_date', 'desc')
                ->get();
        }

        return $pipeline;
    }
}
