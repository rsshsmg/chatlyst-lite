<?php

namespace App\Services\HISSync\Handlers;

use App\Models\District;
use App\Models\Education;
use App\Models\JobTitle;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SubDistrict;
use App\Services\HISSync\BaseSyncHandler;
use App\Services\HISSync\Contracts\SyncHandlerInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class JobTitleSyncHandler extends BaseSyncHandler
{
    private $batchSize = 100;

    /**
     * Simpan data dari DTO ke dalam model internal.
     */
    public function handle(object $jobTitle): mixed
    {
        return DB::transaction(function () use ($jobTitle) {
            $this->startTime = microtime(true);

            if (empty($jobTitle)) {
                return;
            }

            JobTitle::updateOrCreate(
                ['code' => $jobTitle->code],
                [
                    'code' => $jobTitle->code,
                    'name' => $jobTitle->name,
                    'description' => $jobTitle->description,
                ]
            );

            $this->endTime = microtime(true);
        });
    }
}
