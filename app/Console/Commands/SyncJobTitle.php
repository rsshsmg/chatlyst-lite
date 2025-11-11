<?php

namespace App\Console\Commands;

use App\Models\JobTitle;
use App\Traits\HasConsoleResult;
use Illuminate\Console\Command;

class SyncJobTitle extends Command
{
    use HasConsoleResult;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:jobtitle {--all}
                            {--id= : ID of the job title to sync}
                            {--force : Force the synchronization even if it has already been done}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize master data Job Title with the HIS system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new \App\Services\HISSync\SyncManager(
            app(\App\Services\HISSync\Adapters\Medisimed\JobTitleAdapter::class),
            app(\App\Services\HISSync\Handlers\JobTitleSyncHandler::class)
        );

        if ($this->option('id')) {
            $externalId = $this->option('id');
            $this->info("Synchronizing Job Title with ID: {$externalId}");
            if ($this->option('force')) {
                $this->warn('Force option is enabled. Proceeding with re-synchronization.');

                $this->truncateTables($externalId); // Clear existing data for the specific Job Titles

                $this->alert("Existing Job Titles master data for ID {$externalId} have been cleared.");
            }

            $this->info("Starting synchronization of Job Title with ID {$externalId}...");
            $result = $service->syncById($externalId);
            // $this->displaySyncResults($result);
            $this->info("Synchronization of Job Title with ID {$externalId} completed successfully. (Execution time: {$service->getExecutionTime()} seconds)");

            return;
        } else {
            $this->comment('No specific ID provided. Synchronizing all Job Titles...');
            $this->newLine();

            if (JobTitle::withTrashed()->count() > 0) {
                if (!$this->option('force')) {
                    $this->warn('Job Titles master data have already been synchronized. Use --force to re-sync.');
                    return;
                }

                $this->warn('Force option is enabled. Proceeding with re-synchronization.');
                $this->truncateTables(); // Clear all existing data
                $this->alert('Existing Job Titles master data have been cleared.');
            }

            $this->info('Starting synchronization of all Job Titles...');
            $result = $service->syncAll();
            $this->displaySyncResults($result);
            // $this->info("Synchronization of all Job Titles completed successfully. (Execution time: {$service->getExecutionTime()} seconds)");
            return;
        }
    }


    protected function truncateTables(?string $jobTitleId = null): void
    {
        if ($jobTitleId) {
            // Delete specific data
            JobTitle::where('code', $jobTitleId)->withTrashed()->forceDelete();
        } else {
            // Delete all data in correct order
            JobTitle::query()->withTrashed()->forceDelete();
        }
    }
}
