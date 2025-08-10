<?php

namespace App\Services\HISSync\DTO;

use Brick\Math\BigInteger;
use Illuminate\Support\Collection;
use phpDocumentor\Reflection\Types\Boolean;

class RegencyDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public string $provinceId,
        public bool $isActive = true,
        public ?Collection $districts = null,
    ) {}
}
