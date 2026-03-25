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

        return Inertia::render('Imports/Map', [
            'import' => $import,
            'preview' => $preview,
            'availableFields' => $availableFields,
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
        ]);

        try {
            // Dispatch background job for processing
            ProcessContactImport::dispatch(
                $import,
                $request->mapping,
                $request->duplicate_strategy
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
            'import' => $import,
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
     * Delete an import record.
     */
    public function destroy(Import $import)
    {
        $this->authorize('delete', $import);

        // Delete the CSV file
        if (file_exists(storage_path('app/' . $import->filename))) {
            unlink(storage_path('app/' . $import->filename));
        }

        $import->delete();

        return redirect()->route('imports.index')
            ->with('success', 'Import deleted successfully.');
    }
}
