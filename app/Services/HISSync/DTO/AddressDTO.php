<?php

namespace App\Services\HISSync\DTO;

class AddressDTO
{
    public function __construct(
        public ?string $personId,
        public int $addressType,
        public string $address,
        public ?int $countryId = null,
        public ?int $subDistrictId = null,
        public ?string $countryCode = null,
        public ?string $postalCode = null,
        public ?bool $isPrimary = true,
    ) {}
}
