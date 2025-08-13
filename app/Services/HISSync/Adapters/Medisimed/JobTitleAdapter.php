<?php

namespace App\Services\HISSync\Adapters\Medisimed;

use App\Services\HISSync\BaseSyncAdapter;
use App\Services\HISSync\DTO\JobTitleDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class JobTitleAdapter extends BaseSyncAdapter
{
    public function getAll(): Collection
    {
        $this->startTime = microtime(true);

        $results = DB::connection('hisdb')->table('PEKERJAAN')->get();

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
        $row = DB::connection('hisdb')->table('PEKERJAAN')->where('KD_PEKERJAAN', $id)->first();

        $this->endTime = microtime(true);

        return $row ? $this->mapToDTO($row) : null;
    }

    protected function mapToDTO(object $row): JobTitleDTO
    {
        return new JobTitleDTO(
            code: $row->KD_PEKERJAAN,
            name: $row->PEKERJAAN,
            description: $row->PEKERJAAN,
        );
    }
}
