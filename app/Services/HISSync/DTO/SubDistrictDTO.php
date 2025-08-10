<?php

namespace App\Services\HISSync\DTO;

use Illuminate\Support\Collection;
use phpDocumentor\Reflection\Types\Boolean;

class SubDistrictDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public string $districtId,
        public bool $isActive = true,
        public ?Collection $subDistricts = null,
    ) {}
}
