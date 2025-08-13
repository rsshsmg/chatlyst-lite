<?php

namespace App\Services\HISSync\DTO;

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\ReligionType;

class PersonDTO
{
    public function __construct(
        public string $fullName,
        public ?Gender $gender,
        public ?string $birthDate,
        public ?string $nickname = null,
        public ?string $birthPlace = null,
        public ?string $motherName = null,
        public ?BloodType $bloodType = null,
        public ?ReligionType $religion = null,
        public ?MaritalStatus $maritalStatus = null,
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
