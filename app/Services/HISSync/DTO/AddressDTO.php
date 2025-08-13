<?php

namespace App\Services\HISSync\DTO;

use App\Enums\AddressType;

class AddressDTO
{
    public function __construct(
        public ?string $personId,
        public AddressType $addressType,
        public string $address,
        public ?int $countryId = null,
        public ?int $subDistrictId = null,
        public ?string $countryCode = null,
        public ?string $postalCode = null,
        public ?bool $isPrimary = true,
    ) {}
}
