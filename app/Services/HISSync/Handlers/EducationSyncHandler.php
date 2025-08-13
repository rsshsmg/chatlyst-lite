<?php

namespace App\Services\HISSync\Handlers;

use App\Models\District;
use App\Models\Education;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SubDistrict;
use App\Services\HISSync\BaseSyncHandler;
use App\Services\HISSync\Contracts\SyncHandlerInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EducationSyncHandler extends BaseSyncHandler
{
    private $batchSize = 100;

    /**
     * Simpan data dari DTO ke dalam model internal.
     */
    public function handle(object $education): mixed
    {
        return DB::transaction(function () use ($education) {
            $this->startTime = microtime(true);

            if (empty($education)) {
                return;
            }

            Education::updateOrCreate(
                ['code' => $education->code],
                [
                    'code' => $education->code,
                    'name' => $education->name,
                    'description' => $education->description,
                ]
            );

            $this->endTime = microtime(true);
        });
    }
}
