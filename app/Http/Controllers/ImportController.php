<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessContactImport;
use App\Models\Import;
use App\Services\ContactImportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ImportController extends Controller
{
    protected ContactImportService $importService;

    public function __construct(ContactImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display import history.
     */
    public function index(Request $request): Response
    {
        $imports = Import::with('user')
            ->when($request->user_id, function ($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(15);

        return Inertia::render('Imports/Index', [
            'imports' => $imports,
            'filters' => $request->only(['user_id', 'status']),
        ]);
    }

    /**
     * Show upload form.
     */
    public function create(): Response
    {
        return Inertia::render('Imports/Upload');
    }

    /**
     * Handle file upload and show preview/mapping.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        try {
            $import = $this->importService->uploadFile($request->file('file'), auth()->id());
            $preview = $this->importService->getPreview($import, 10);

            return redirect()->route('imports.map', $import->id)
                ->with('success', 'File uploaded successfully. Please map the columns.');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => $e->getMessage()]);
        }
    }

    /**
     * Show column mapping interface.
     */
    public function map(Import $import): Response
    {
        $this->authorize('update', $import);

        $preview = $this->importService->getPreview($import, 10);

        $availableFields = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'Zip Code',
            'contact_type' => 'Contact Type',
            'status' => 'Status',
            'source' => 'Source',
            'priority' => 'Priority',
        ];

        // Get all active users for assigned_to dropdown reference
        $users = \App\Models\User::where('is_approved', true)
            ->where('is_suspended', false)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        // Get all existing tags for global tag selection
        $tags = \App\Models\ContactTag::orderBy('name')->get();

        return Inertia::render('Imports/Map', [
            'importRecord' => $import,
            'preview' => $preview,
            'availableFields' => $availableFields,
            'users' => $users,
            'tags' => $tags,
        ]);
    }

    /**
     * Process the import with mapping.
     */
    public function process(Request $request, Import $import)
    {
        $this->authorize('update', $import);

        $request->validate([
            'mapping' => 'required|array',
            'duplicate_strategy' => 'required|in:skip,update,merge',
            'global_assigned_to' => 'nullable|exists:users,id',
            'global_tag_ids' => 'nullable|array',
            'global_tag_ids.*' => 'exists:contact_tags,id',
        ]);

        try {
            // Dispatch background job for processing
            ProcessContactImport::dispatch(
                $import,
                $request->mapping,
                $request->duplicate_strategy,
                $request->global_tag_ids ?? [],
                $request->global_assigned_to
            );

            return redirect()->route('imports.show', $import->id)
                ->with('success', 'Import is being processed in the background.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show import details and progress.
     */
    public function show(Import $import): Response
    {
        $this->authorize('view', $import);

        $import->load(['user', 'errors']);
        $statistics = $this->importService->getStatistics($import);

        return Inertia::render('Imports/Show', [
            'importRecord' => $import,
            'statistics' => $statistics,
            'errors' => $import->errors()->paginate(10),
        ]);
    }

    /**
     * Download CSV template.
     */
    public function downloadTemplate()
    {
        $filePath = $this->importService->generateTemplate();

        return response()->download($filePath, basename($filePath))->deleteFileAfterSend();
    }

    /**
     * Download failed rows as CSV.
     */
    public function downloadFailedRows(Import $import)
    {
        $this->authorize('view', $import);

        try {
            $filePath = $this->importService->exportFailedRows($import);

            return response()->download($filePath, basename($filePath))->deleteFileAfterSend();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Cancel a pending import.
     */
    public function cancel(Import $import)
    {
        // Check authorization - allow users to cancel their own imports or admins to cancel any
        if ($import->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'You can only cancel your own imports.');
        }

        // Only allow canceling pending, failed, or processing imports
        if (!in_array($import->status, ['pending', 'failed', 'processing'])) {
            return back()->withErrors(['error' => 'Only pending, processing, or failed imports can be canceled.']);
        }

        // If still processing, mark as failed first
        if ($import->status === 'processing') {
            $import->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);
        }

        // Delete the CSV file - handle Windows path separators
        // Note: 'local' disk uses 'app/private' as root
        $filePath = storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $import->filename));
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $import->delete();

        return redirect()->route('imports.index')
            ->with('success', 'Import canceled successfully.');
    }

    /**
     * Delete an import record.
     */
    public function destroy(Import $import)
    {
        $this->authorize('delete', $import);

        // Delete the CSV file - handle Windows path separators
        // Note: 'local' disk uses 'app/private' as root
        $filePath = storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $import->filename));
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $import->delete();

        return redirect()->route('imports.index')
            ->with('success', 'Import deleted successfully.');
    }
}
