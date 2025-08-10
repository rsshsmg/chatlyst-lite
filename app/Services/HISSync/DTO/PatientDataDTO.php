<?php

namespace App\Services\HISSync\DTO;

use App\Enums\Gender;

class PatientDataDTO
{
    public function __construct(
        public string $patientId,
        public ?string $refPatientId,
        public string $fullName,
        public ?string $nickname = null,
        public ?Gender $gender,
        public ?string $birthPlace,
        public ?string $birthDate,
        public ?string $nationality = null,
        public ?string $bloodType = null,
        public ?array $identity = [],
        public ?array $phones = [],
        public ?array $emails = [],
        public ?array $addresses = [],
        public ?array $guardian = null,
    ) {}
}
