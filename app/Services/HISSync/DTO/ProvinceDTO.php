<?php

namespace App\Services\HISSync\DTO;

use Illuminate\Support\Collection;
use phpDocumentor\Reflection\Types\Boolean;

class ProvinceDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public string $countryId,
        public bool $isActive = true,
        public ?Collection $regencies = null,
    ) {}
}
