<?php

namespace App\Services\HISSync\DTO;

use App\Enums\Gender;

class PatientDataDTO
{
    public function __construct(
        public string $patientCode,
        public ?string $refPatientCode,
        public PersonDTO $person,
        public ?array $guardians = null,
    ) {}
}
