<?php

namespace App\Services\HISSync\DTO;

use App\Enums\Gender;

class PersonDTO
{
    public function __construct(
        public string $fullName,
        public ?string $nickname = null,
        public ?Gender $gender,
        public ?string $birthPlace,
        public ?string $birthDate,
        public ?string $motherName,
        public ?int $bloodType = null,
        public ?int $religion = null,
        public ?int $maritalStatus = null,
        public ?int $educationId = null,
        public ?int $jobTitleId = null,
        public ?string $langCode = null,
        public ?string $ethnicityCode = null,
        public ?bool $isForeigner = false,
        public ?string $nationality = null,
        public ?array $identities = [],
        public ?array $phones = [],
        public ?array $emails = [],
        public ?array $addresses = [],
    ) {}
}
