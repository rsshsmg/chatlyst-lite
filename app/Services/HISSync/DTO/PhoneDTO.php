<?php

namespace App\Services\HISSync\DTO;

use DateTime;

class PhoneDTO
{
    public function __construct(
        public ?string $personId,
        public string $number,
        public ?string $countryCode = null,
        public ?bool $isWhatsapp = false,
        public ?DateTime $verifiedAt = null,
        public ?bool $isActive = true,
        public ?bool $isPrimary = false,
    ) {}
}
