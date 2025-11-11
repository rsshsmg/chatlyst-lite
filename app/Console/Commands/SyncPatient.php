<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Traits\HasConsoleResult;
use Illuminate\Console\Command;

class SyncPatient extends Command
{
    use HasConsoleResult;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:patient {--all}
                            {--id= : ID of the patient to sync}
                            {--force : Force the synchronization even if it has already been done}
                            {--verbose : Show detailed processing information}';

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

        if (config('app.env') !== 'production' || config('app.debug') === true) {
            $this->alert("Seems the system running in Development Mode, Only {$adapter->limit} record(s) will be synchronized.");
            $this->info("Change system environment to Production and disable Debug Mode to syncronize all records.");
            $this->newLine();
        }

        if ($this->option('all')) {
            if (Patient::withTrashed()->count() > 0) {
                if (!$this->option('force')) {
                    $this->warn('Patients data have already been synchronized. Use --force to re-sync.');
                    return;
                }

                $this->warn('Force option is enabled. Proceeding with re-synchronization.');
            }

            $this->info('Starting synchronization of all Patients...');

            $result = $service->syncAll();
            $this->displaySyncResults($result);

            return;
        }

        if (!$this->option('id')) {
            $this->comment('No specific ID provided. Use --all to synchronize all Patients.');
            $this->error('Operation aborted.');
            $this->newLine();
            return;
        }

        $externalId = $this->option('id');
        $this->info("Synchronizing Patient with ID: {$externalId}");

        if ($this->option('force')) {
            $this->warn('Force option is enabled. Proceeding with re-synchronization.');
        }

        $this->info("Starting synchronization of Patient with ID {$externalId}...");

        $result = $service->syncById($externalId);
        $this->displaySyncResults($result);
    }
}
