<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Import;
use App\Models\ImportError;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContactImportService
{
    protected CSVParserService $csvParser;
    protected DuplicateDetectionService $duplicateDetector;

    public function __construct(
        CSVParserService $csvParser,
        DuplicateDetectionService $duplicateDetector
    ) {
        $this->csvParser = $csvParser;
        $this->duplicateDetector = $duplicateDetector;
    }

    /**
     * Upload and validate CSV file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $userId
     * @return Import
     */
    public function uploadFile($file, int $userId): Import
    {
        // Generate unique filename
        $filename = time() . '_' . $file->getClientOriginalName();

        // Store file first
        $path = $file->storeAs('imports', $filename);

        if (!$path) {
            throw new Exception('Failed to store CSV file');
        }

        // Full path to the stored file - use Storage facade to get correct path
        // Laravel's 'local' disk uses 'app/private' as root by default
        $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));

        // Validate the stored file
        if (!file_exists($fullPath)) {
            throw new Exception('File was not stored correctly at: ' . $fullPath);
        }

        $validation = $this->csvParser->validate($fullPath);

        if (!$validation['valid']) {
            // Clean up the stored file if validation fails
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            throw new Exception('Invalid CSV file: ' . implode(', ', $validation['errors']));
        }

        // Parse CSV to get row count
        $preview = $this->csvParser->preview($fullPath, 1);

        // Create import record
        $import = Import::create([
            'user_id' => $userId,
            'filename' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'total_rows' => $preview['total_rows'],
            'status' => 'pending',
        ]);

        return $import;
    }

    /**
     * Get preview of CSV data with suggested mapping.
     *
     * @param Import $import
     * @param int $limit
     * @return array
     */
    public function getPreview(Import $import, int $limit = 10): array
    {
        $filePath = $this->getImportFilePath($import);
        $preview = $this->csvParser->preview($filePath, $limit);
        $suggestedMapping = $this->csvParser->suggestMapping($preview['headers']);

        return [
            'headers' => $preview['headers'],
            'data' => $preview['data'],
            'total_rows' => $preview['total_rows'],
            'suggested_mapping' => $suggestedMapping,
        ];
    }

    /**
     * Process import with mapping and duplicate handling.
     *
     * @param Import $import
     * @param array $mapping
     * @param string $duplicateStrategy
     * @param array $globalTagIds
     * @param int|null $globalAssignedTo
     * @return void
     */
    public function processImport(Import $import, array $mapping, string $duplicateStrategy = 'skip', array $globalTagIds = [], ?int $globalAssignedTo = null): void
    {
        $import->update([
            'status' => 'processing',
            'started_at' => now(),
            'mapping' => $mapping,
        ]);

        $filePath = $this->getImportFilePath($import);
        $csvData = $this->csvParser->parse($filePath);

        $successful = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($csvData['data'] as $rowNumber => $row) {
            try {
                // Map CSV data to contact fields
                $contactData = $this->mapRowData($row, $mapping);

                // Apply global assigned_to to all contacts
                if ($globalAssignedTo) {
                    $contactData['assigned_to'] = $globalAssignedTo;
                }

                // Validate data
                $validator = Validator::make($contactData, [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'nullable|email|max:255',
                    'phone' => 'nullable|string|max:50',
                ]);

                if ($validator->fails()) {
                    $failed++;
                    $this->logError($import, $rowNumber + 2, $row, $validator->errors()->first());
                    continue;
                }

                $contact = null;

                // Check for duplicates
                $duplicateCheck = $this->duplicateDetector->detect($contactData);

                if ($duplicateCheck['is_duplicate']) {
                    $existing = $duplicateCheck['matches'][0]['contact'];
                    $result = $this->duplicateDetector->handleDuplicate($existing, $contactData, $duplicateStrategy);

                    if ($result['action'] === 'skipped') {
                        $skipped++;
                        $contact = $existing; // Still apply tags to existing contact
                    } else {
                        $successful++;
                        $contact = $result['contact'];
                    }
                } else {
                    // Create new contact
                    $contact = Contact::create($contactData);
                    $successful++;
                }

                // Apply tags to the contact
                if ($contact) {
                    $this->applyTags($contact, $globalTagIds);
                }
            } catch (Exception $e) {
                $failed++;
                $this->logError($import, $rowNumber + 2, $row, $e->getMessage());
            }
        }

        $import->update([
            'status' => 'completed',
            'completed_at' => now(),
            'successful_rows' => $successful,
            'failed_rows' => $failed,
            'skipped_rows' => $skipped,
        ]);
    }

    /**
     * Map row data according to mapping configuration.
     *
     * @param array $row
     * @param array $mapping
     * @return array
     */
    protected function mapRowData(array $row, array $mapping): array
    {
        $contactData = [];

        foreach ($mapping as $csvColumn => $contactField) {
            if ($contactField && isset($row[$csvColumn])) {
                $contactData[$contactField] = trim($row[$csvColumn]);
            }
        }

        // Set defaults for required fields
        if (!isset($contactData['contact_type'])) {
            $contactData['contact_type'] = 'buyer';
        }

        if (!isset($contactData['status'])) {
            $contactData['status'] = 'lead';
        }

        if (!isset($contactData['source'])) {
            $contactData['source'] = 'CSV Import';
        }

        if (!isset($contactData['priority'])) {
            $contactData['priority'] = 'medium';
        }

        return $contactData;
    }

    /**
     * Log import error.
     *
     * @param Import $import
     * @param int $rowNumber
     * @param array $rowData
     * @param string $errorMessage
     * @return void
     */
    protected function logError(Import $import, int $rowNumber, array $rowData, string $errorMessage): void
    {
        ImportError::create([
            'import_id' => $import->id,
            'row_number' => $rowNumber,
            'row_data' => $rowData,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Get import statistics.
     *
     * @param Import $import
     * @return array
     */
    public function getStatistics(Import $import): array
    {
        return [
            'total_rows' => $import->total_rows,
            'successful_rows' => $import->successful_rows,
            'failed_rows' => $import->failed_rows,
            'skipped_rows' => $import->skipped_rows,
            'progress_percentage' => $import->progress_percentage,
            'status' => $import->status,
        ];
    }

    /**
     * Generate CSV template.
     *
     * @return string
     */
    public function generateTemplate(): string
    {
        $headers = [
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Address',
            'City',
            'State',
            'Zip',
            'Contact Type',
            'Status',
            'Source',
            'Priority',
        ];

        $exampleRow = [
            'John',
            'Doe',
            'john.doe@example.com',
            '(555) 123-4567',
            '123 Main Street',
            'Los Angeles',
            'CA',
            '90001',
            'buyer',
            'lead',
            'Website',
            'high',
        ];

        $filename = 'contact_import_template_' . time() . '.csv';
        $templateDir = storage_path('app' . DIRECTORY_SEPARATOR . 'templates');
        $path = $templateDir . DIRECTORY_SEPARATOR . $filename;

        // Ensure templates directory exists
        if (!file_exists($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        $handle = fopen($path, 'w');
        fputcsv($handle, $headers);
        fputcsv($handle, $exampleRow);
        fclose($handle);

        return $path;
    }

    /**
     * Export failed rows to CSV.
     *
     * @param Import $import
     * @return string
     */
    public function exportFailedRows(Import $import): string
    {
        $errors = $import->errors;

        if ($errors->isEmpty()) {
            throw new Exception('No failed rows to export');
        }

        $filename = 'failed_rows_' . $import->id . '_' . time() . '.csv';
        $exportDir = storage_path('app' . DIRECTORY_SEPARATOR . 'exports');
        $path = $exportDir . DIRECTORY_SEPARATOR . $filename;

        // Ensure exports directory exists
        if (!file_exists($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $handle = fopen($path, 'w');

        // Add headers
        $firstError = $errors->first();
        $headers = array_keys($firstError->row_data);
        $headers[] = 'Error Message';
        fputcsv($handle, $headers);

        // Add failed rows with error messages
        foreach ($errors as $error) {
            $row = array_values($error->row_data);
            $row[] = $error->error_message;
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $path;
    }

    /**
     * Apply global tags to a contact.
     *
     * @param Contact $contact
     * @param array $globalTagIds Array of tag IDs to apply globally
     * @return void
     */
    protected function applyTags(Contact $contact, array $globalTagIds): void
    {
        if (!empty($globalTagIds)) {
            // Sync tags (add without removing existing)
            $contact->tags()->syncWithoutDetaching($globalTagIds);
        }
    }

    /**
     * Get the full file path for an import, handling Windows path separators.
     *
     * @param Import $import
     * @return string
     */
    protected function getImportFilePath(Import $import): string
    {
        // Normalize path separators for Windows
        // Note: 'local' disk uses 'app/private' as root
        $relativePath = str_replace('/', DIRECTORY_SEPARATOR, $import->filename);
        return storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . $relativePath);
    }
}
