<?php

namespace App\Services\HISSync\DTO;

use Illuminate\Support\Collection;
use phpDocumentor\Reflection\Types\Boolean;

class DistrictDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public string $regencyId,
        public bool $isActive = true,
        public ?Collection $subdistricts = null,
    ) {}
}
