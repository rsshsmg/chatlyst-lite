<?php

namespace App\Console\Commands;

use App\Models\Patient;
use Illuminate\Console\Command;

class SyncPatient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:patient {--all}
                            {--id= : ID of the patient to sync}
                            {--force : Force the synchronization even if it has already been done}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize patients with the HIS system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adapter = app(\App\Services\HISSync\Adapters\Medisimed\PatientAdapter::class);
        $handler = app(\App\Services\HISSync\Handlers\PatientSyncHandler::class);

        $service = new \App\Services\HISSync\SyncManager($adapter, $handler);

        if (config('app.debug') === true) {
            $this->alert("Debug mode is enabled. Only {$adapter->limit} record(s) will be synchronized.");
        }

        if ($this->option('id')) {
            $externalId = $this->option('id');
            $this->info("Synchronizing Patient with ID: {$externalId}");
            if ($this->option('force')) {
                $this->warn('Force option is enabled. Proceeding with re-synchronization.');

                // $this->truncateTables($externalId); // Clear existing data for the specific Patients

                // $this->alert("Existing Patients data for ID {$externalId} have been cleared.");
            }

            $this->info("Starting synchronization of Patient with ID {$externalId}...");
            $service->syncById($externalId);
            $this->info("Synchronization of Patient with ID {$externalId} completed successfully. (Execution time: {$service->getExecutionTime()} seconds)");

            return;
        } else {
            $this->comment('No specific ID provided. Synchronizing all Patients...');
            $this->newLine();

            if (Patient::withTrashed()->count() > 0) {
                if (!$this->option('force')) {
                    $this->warn('Patients data have already been synchronized. Use --force to re-sync.');
                    return;
                }

                $this->warn('Force option is enabled. Proceeding with re-synchronization.');
                // $this->truncateTables(); // Clear all existing data
                // $this->alert('Existing Patients data have been cleared.');
            }

            $this->info('Starting synchronization of all Patients...');
            $service->syncAll();
            $this->info("Synchronization of all Patients completed successfully. (Execution time: {$service->getExecutionTime()} seconds)");
            return;
        }
    }
}
