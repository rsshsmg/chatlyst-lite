<?php

namespace App\Console\Commands;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SubDistrict;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncProvinces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:provinces {--id=}
                            {--force : Force the synchronization even if it has already been done}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize provinces with the HIS system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new \App\Services\HISSync\SyncManager(
            app(\App\Services\HISSync\Adapters\Medisimed\ProvinceAdapter::class),
            app(\App\Services\HISSync\Handlers\ProvinceSyncHandler::class)
        );

        if ($this->option('id')) {
            $externalId = $this->option('id');
            $this->info("Synchronizing province with ID: {$externalId}");
            if ($this->option('force')) {
                $this->warn('Force option is enabled. Proceeding with re-synchronization.');

                $this->truncateTables($externalId); // Clear existing data for the specific province

                $this->alert("Existing province, regencies, districts, and sub-districts for ID {$externalId} have been cleared.");
            }

            $this->info("Starting synchronization of province with ID {$externalId}...");
            $service->syncById($externalId);
            $this->info("Synchronization of province with ID {$externalId} completed successfully. (Execution time: {$service->getExecutionTime()} seconds)");

            return;
        } else {
            $this->info('No specific ID provided. Synchronizing all provinces...');

            if (\App\Models\Province::count() > 0) {
                if (!$this->option('force')) {
                    $this->warn('Provinces have already been synchronized. Use --force to re-sync.');
                    return;
                }

                $this->warn('Force option is enabled. Proceeding with re-synchronization.');
                $this->truncateTables(); // Clear all existing data
                $this->alert('Existing provinces, regencies, districts, and sub-districts have been cleared.');
            }

            $this->info('Starting synchronization of all provinces...');
            $service->syncAll();
            $this->info("Synchronization of all provinces completed successfully. (Execution time: {$service->getExecutionTime()} seconds)");
            return;
        }
    }

    // protected function truncateTables(?string $provinceId = null): void
    // {
    //     if ($provinceId) {
    //         $province = \App\Models\Province::where('code', $provinceId)->first();
    //         if (!$province) {
    //             $this->error("Province with ID {$provinceId} not found.");
    //             return;
    //         }
    //         $id = $province->id;
    //         \App\Models\SubDistrict::district()->regency()->province()->where('province_id', $id)->delete();
    //         \App\Models\District::where('province_id', $id)->delete();
    //         \App\Models\Regency::where('province_id', $id)->delete();
    //         \App\Models\Province::find($id)->delete();
    //         return;
    //     }

    //     \App\Models\SubDistrict::truncate();
    //     \App\Models\District::truncate();
    //     \App\Models\Regency::truncate();
    //     \App\Models\Province::truncate();
    // }

    protected function truncateTables(?string $provinceId = null): void
    {
        if ($provinceId) {
            // Delete specific data
            Province::where('code', $provinceId)->delete();
        } else {
            // Delete all data in correct order
            SubDistrict::query()->delete();
            District::query()->delete();
            Regency::query()->delete();
            Province::query()->delete();
        }
    }
}
