<?php

namespace App\Services\HISSync\DTO;

use DateTime;

class IdentityDTO
{
    public function __construct(
        public ?string $personId,
        public string $number,
        public string $countryCode = 'ID',
        public ?string $identityType = null,
        public ?DateTime $issuedAt = null,
        public ?DateTime $expiredAt = null,
        public ?string $imageId = null,
        public ?bool $isPrimary = true,
        public ?DateTime $verifiedAt = null,
    ) {}
}
