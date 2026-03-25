<?php

namespace App\Services;

use App\Models\ContactTag;
use Illuminate\Support\Collection;

class ContactTagService
{
    /**
     * Get all tags
     */
    public function getAllTags(): Collection
    {
        return ContactTag::withCount('contacts')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get popular tags (most used)
     */
    public function getPopularTags(int $limit = 10): Collection
    {
        return ContactTag::withCount('contacts')
            ->orderBy('contacts_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Create a new tag
     */
    public function createTag(array $data): ContactTag
    {
        return ContactTag::create([
            'name' => $data['name'],
            'color' => $data['color'] ?? '#3b82f6',
        ]);
    }

    /**
     * Update a tag
     */
    public function updateTag(ContactTag $tag, array $data): ContactTag
    {
        $tag->update([
            'name' => $data['name'] ?? $tag->name,
            'color' => $data['color'] ?? $tag->color,
        ]);

        return $tag->fresh();
    }

    /**
     * Delete a tag
     */
    public function deleteTag(ContactTag $tag): bool
    {
        // This will automatically remove all pivot relationships
        return $tag->delete();
    }

    /**
     * Merge tags (merge source into target, delete source)
     */
    public function mergeTags(ContactTag $source, ContactTag $target): ContactTag
    {
        // Get all contacts with the source tag
        $contactIds = $source->contacts()->pluck('contacts.id')->toArray();

        // Attach them to the target tag (without detaching existing)
        $target->contacts()->syncWithoutDetaching($contactIds);

        // Delete the source tag
        $source->delete();

        return $target->fresh(['contacts']);
    }

    /**
     * Search tags by name
     */
    public function searchTags(string $query): Collection
    {
        return ContactTag::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->get();
    }
}
