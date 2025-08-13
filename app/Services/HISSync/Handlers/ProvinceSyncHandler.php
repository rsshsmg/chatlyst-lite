<?php

namespace App\Services\HISSync\Handlers;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SubDistrict;
use App\Services\HISSync\BaseSyncHandler;
use App\Services\HISSync\Contracts\SyncHandlerInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProvinceSyncHandler extends BaseSyncHandler
{
    private $defaultCountryId = 103; // ID Negara Indonesia
    private $batchSize = 100;

    /**
     * Simpan data dari DTO ke dalam model internal.
     */
    public function handle(object $dto): mixed
    {
        $defaultCountryId = $this->defaultCountryId;

        return DB::transaction(function () use ($dto, $defaultCountryId) {
            $this->startTime = microtime(true);

            // Simpan atau update provinsi di database
            $province = Province::updateOrCreate(
                ['code' => $dto->code],
                [
                    'code' => $dto->code,
                    'name' => $dto->name,
                    'is_active' => $dto->isActive,
                    'country_id' => $defaultCountryId,
                ],
            );

            // Simpan atau update kabupaten/kota
            $this->syncRegencies($dto->regencies, $province->id);

            $this->endTime = microtime(true);
        });
    }

    protected function syncRegencies(?object $dto, int $provinceId): void
    {
        if ($dto === null || $dto->isEmpty()) {
            return;
        }

        foreach ($dto as $r) {
            $regency = Regency::updateOrCreate(
                ['code' => $r->code],
                [
                    'code' => $r->code,
                    'name' => $r->name,
                    'province_id' => $provinceId,
                    'is_active' => $r->isActive,
                ]
            );

            // Simpan atau update kecamatan
            $this->syncDistricts($r->districts, $regency->id);
        }
    }

    protected function syncDistricts(?object $dto, int $regencyId): void
    {
        if ($dto === null || $dto->isEmpty()) {
            return;
        }

        foreach ($dto as $d) {
            $district = District::updateOrCreate(
                ['code' => $d->code],
                [
                    'code' => $d->code,
                    'name' => $d->name,
                    'regency_id' => $regencyId,
                    'is_active' => $d->isActive,
                ]
            );

            // Simpan atau update kelurahan
            $this->syncSubDistricts($d->subdistricts, $district->id);
        }
    }

    protected function syncSubDistricts(?object $dto, int $districtId): void
    {
        if ($dto === null || $dto->isEmpty()) {
            return;
        }

        foreach ($dto as $s) {
            SubDistrict::updateOrCreate(
                ['code' => $s->code],
                [
                    'code' => $s->code,
                    'name' => $s->name,
                    'district_id' => $districtId,
                    'is_active' => $s->isActive,
                ]
            );
        }
    }
}
