<?php

namespace App\Console\Commands;

use App\Models\Import;
use Illuminate\Console\Command;

class CleanupStuckImports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imports:cleanup-stuck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark imports stuck in pending/processing status for more than 5 minutes as failed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find imports that have been pending/processing for more than 5 minutes
        $stuckImports = Import::whereIn('status', ['pending', 'processing'])
            ->where('created_at', '<', now()->subMinutes(5))
            ->get();

        if ($stuckImports->isEmpty()) {
            $this->info('No pending imports found.');
            return 0;
        }

        $count = 0;
        foreach ($stuckImports as $import) {
            $import->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);
            $count++;
            $this->info("Marked import #{$import->id} as failed.");
        }

        $this->info("Total: Marked {$count} pending import(s) as failed.");
        return 0;
    }
}
