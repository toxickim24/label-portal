<?php

namespace App\Console\Commands;

use App\Models\Import;
use Illuminate\Console\Command;

class CleanFailedImports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imports:clean-failed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all failed and pending imports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find all failed and pending imports
        $imports = Import::whereIn('status', ['failed', 'pending'])->get();

        if ($imports->isEmpty()) {
            $this->info('No failed or pending imports found.');
            return 0;
        }

        $count = 0;
        foreach ($imports as $import) {
            // Delete the CSV file
            $filePath = storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $import->filename));
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $import->delete();
            $count++;
            $this->info("Deleted import #{$import->id} ({$import->status})");
        }

        $this->info("Total: Deleted {$count} import(s).");
        return 0;
    }
}
