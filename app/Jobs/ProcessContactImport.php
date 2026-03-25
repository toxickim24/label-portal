<?php

namespace App\Jobs;

use App\Models\Import;
use App\Services\ContactImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Exception;

class ProcessContactImport implements ShouldQueue
{
    use Queueable;

    protected Import $import;
    protected array $mapping;
    protected string $duplicateStrategy;
    protected array $globalTagIds;
    protected ?int $globalAssignedTo;

    /**
     * Create a new job instance.
     */
    public function __construct(Import $import, array $mapping, string $duplicateStrategy = 'skip', array $globalTagIds = [], ?int $globalAssignedTo = null)
    {
        $this->import = $import;
        $this->mapping = $mapping;
        $this->duplicateStrategy = $duplicateStrategy;
        $this->globalTagIds = $globalTagIds;
        $this->globalAssignedTo = $globalAssignedTo;
    }

    /**
     * Execute the job.
     */
    public function handle(ContactImportService $importService): void
    {
        try {
            $importService->processImport(
                $this->import,
                $this->mapping,
                $this->duplicateStrategy,
                $this->globalTagIds,
                $this->globalAssignedTo
            );
        } catch (Exception $e) {
            $this->import->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        $this->import->update([
            'status' => 'failed',
            'completed_at' => now(),
        ]);
    }
}
