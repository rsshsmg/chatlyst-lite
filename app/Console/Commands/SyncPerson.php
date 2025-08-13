<?php

namespace App\Console\Commands;

use App\Models\Person;
use Illuminate\Console\Command;

class SyncPerson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:person {--all}
                            {--id= : ID of the person to sync}
                            {--force : Force the synchronization even if it has already been done}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize person (non patient) with the HIS system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adapter = app(\App\Services\HISSync\Adapters\Medisimed\PersonAdapter::class);
        $handler = app(\App\Services\HISSync\Handlers\PersonSyncHandler::class);

        $service = new \App\Services\HISSync\SyncManager($adapter, $handler);

        if (config('app.debug') === true) {
            $this->alert("Debug mode is enabled. Only {$adapter->limit} record(s) will be synchronized.");
        }

        if ($this->option('id')) {
            $externalId = $this->option('id');
            $this->info("Synchronizing Person with ID: {$externalId}");
            if ($this->option('force')) {
                $this->warn('Force option is enabled. Proceeding with re-synchronization.');

                // $this->truncateTables($externalId); // Clear existing data for the specific Persons

                $this->alert("Existing Persons data for ID {$externalId} have been cleared.");
            }

            $this->info("Starting synchronization of Person with ID {$externalId}...");
            $service->syncById($externalId);
            $this->info("Synchronization of Person with ID {$externalId} completed successfully. (Execution time: {$service->getExecutionTime()} seconds)");

            return;
        } else {
            $this->comment('No specific ID provided. Synchronizing all People...');
            $this->newLine();

            if ($this->option('force')) {
                $this->warn('Force option is enabled. Proceeding with re-synchronization.');
                // $this->truncateTables(); // Clear all existing data
                $this->alert('Existing People data have been cleared.');
                $this->newLine();
            }

            $this->info('Starting synchronization of all People...');
            $service->syncAll();
            $this->info("Synchronization of all People completed successfully. (Execution time: {$service->getExecutionTime()} seconds)");
            return;
        }
    }
}
