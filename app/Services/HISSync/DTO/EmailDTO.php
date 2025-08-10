<?php

namespace App\Services\HISSync\DTO;

class EmailDTO
{
    public function __construct(
        public string $personId,
        public string $email,
        public ?\DateTime $verifiedAt = null,
    ) {}
}
