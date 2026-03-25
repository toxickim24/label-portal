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
        $contact->update([
            'status' => $status,
            'last_contact_date' => now(),
        ]);

        return $contact->fresh();
    }

    /**
     * Assign contact to user
     */
    public function assignContact(Contact $contact, int $userId): Contact
    {
        $contact->update([
            'assigned_to' => $userId,
        ]);

        return $contact->fresh(['assignedUser']);
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
