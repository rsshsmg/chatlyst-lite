<?php

namespace App\Console\Commands;

use App\Models\Education;
use App\Traits\HasConsoleResult;
use Illuminate\Console\Command;

class SyncEducation extends Command
{
    use HasConsoleResult;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:education {--all}
                            {--id= : ID of the patient to sync}
                            {--force : Force the synchronization even if it has already been done}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize master data Educations with the HIS system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new \App\Services\HISSync\SyncManager(
            app(\App\Services\HISSync\Adapters\Medisimed\EducationAdapter::class),
            app(\App\Services\HISSync\Handlers\EducationSyncHandler::class)
        );

        if ($this->option('id')) {
            $externalId = $this->option('id');
            $this->info("Synchronizing education with ID: {$externalId}");
            if ($this->option('force')) {
                $this->warn('Force option is enabled. Proceeding with re-synchronization.');

                $this->truncateTables($externalId); // Clear existing data for the specific educations

                $this->alert("Existing educations master data for ID {$externalId} have been cleared.");
            }

            $this->info("Starting synchronization of education with ID {$externalId}...");
            $result = $service->syncById($externalId);
            // $this->displaySyncResults($result);
            $this->info("Synchronization of education with ID {$externalId} completed successfully. (Execution time: {$service->getExecutionTime()} seconds)");

            return;
        } else {
            $this->comment('No specific ID provided. Synchronizing all educations...');
            $this->newLine();

            if (Education::withTrashed()->count() > 0) {
                if (!$this->option('force')) {
                    $this->warn('Educations master data have already been synchronized. Use --force to re-sync.');
                    return;
                }

                $this->warn('Force option is enabled. Proceeding with re-synchronization.');
                $this->truncateTables(); // Clear all existing data
                $this->alert('Existing educations master data have been cleared.');
            }

            $this->info('Starting synchronization of all educations...');
            $result = $service->syncAll();
            $this->displaySyncResults($result);
            $this->info("Synchronization of all educations completed successfully. (Execution time: {$service->getExecutionTime()} seconds)");
            return;
        }
    }


    protected function truncateTables(?string $educationId = null): void
    {
        if ($educationId) {
            // Delete specific data
            Education::where('code', $educationId)->withTrashed()->forceDelete();
        } else {
            // Delete all data in correct order
            Education::query()->withTrashed()->forceDelete();
        }
    }
}
