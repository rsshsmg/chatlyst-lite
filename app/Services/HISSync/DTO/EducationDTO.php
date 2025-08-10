<?php

namespace App\Services\HISSync\DTO;

class EducationDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $description = null,
    ) {}
}
