<?php

namespace App\Http\Controllers;

use App\Models\ContactTag;
use App\Services\ContactTagService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ContactTagController extends Controller
{
    public function __construct(
        protected ContactTagService $tagService
    ) {
    }

    /**
     * Display a listing of tags
     */
    public function index(): Response
    {
        $tags = $this->tagService->getAllTags();

        return Inertia::render('Contacts/Tags/Index', [
            'tags' => $tags,
        ]);
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contact_tags,name',
            'color' => 'required|string|max:7',
        ]);

        $this->tagService->createTag($validated);

        return back()->with('success', 'Tag created successfully.');
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, ContactTag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contact_tags,name,' . $tag->id,
            'color' => 'required|string|max:7',
        ]);

        $this->tagService->updateTag($tag, $validated);

        return back()->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified tag
     */
    public function destroy(ContactTag $tag): RedirectResponse
    {
        $this->tagService->deleteTag($tag);

        return back()->with('success', 'Tag deleted successfully.');
    }

    /**
     * Search tags
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:1',
        ]);

        $tags = $this->tagService->searchTags($validated['query']);

        return response()->json($tags);
    }

    /**
     * Get popular tags
     */
    public function popular(): JsonResponse
    {
        $tags = $this->tagService->getPopularTags(10);

        return response()->json($tags);
    }
}
