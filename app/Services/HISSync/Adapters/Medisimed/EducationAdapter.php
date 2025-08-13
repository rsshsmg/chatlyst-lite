<?php

namespace App\Services\HISSync\Adapters\Medisimed;

use App\Services\HISSync\BaseSyncAdapter;
use App\Services\HISSync\DTO\EducationDTO;
use App\Services\HISSync\SyncAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EducationAdapter extends BaseSyncAdapter
{
    public function getAll(): Collection
    {
        $this->startTime = microtime(true);
        $results = DB::connection('hisdb')->table('PENDIDIKAN')->get();

        $output = collect();

        foreach ($results as $row) {
            $output->push($this->mapToDTO($row));
        }
        $this->endTime = microtime(true);
        return $output;
    }

    public function getById(string $id): ?object
    {
        $this->startTime = microtime(true);
        $row = DB::connection('hisdb')->table('PENDIDIKAN')->where('KD_PENDIDIKAN', $id)->first();

        $this->endTime = microtime(true);
        return $row ? $this->mapToDTO($row) : null;
    }

    protected function mapToDTO(object $row): EducationDTO
    {
        return new EducationDTO(
            code: $row->KD_PENDIDIKAN,
            name: $row->PENDIDIKAN,
            description: $row->PENDIDIKAN,
        );
    }
}
