<?php

namespace App\Services\HISSync\Handlers;

use App\Models\Patient;
use App\Services\HISSync\Contracts\SyncHandlerInterface;
use App\Services\HISSync\DTO\PatientDataDTO;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class PatientSyncHandler implements SyncHandlerInterface
{
    public function handle(object $dto): mixed
    {
        /** @var PatientDataDTO $dto */
        return DB::transaction(function () use ($dto) {
            $person = Person::updateOrCreate(
                [
                    'full_name' => $dto->fullName,
                    'nickname' => $dto->nickname,
                    'gender' => $dto->gender,
                    'birth_date' => $dto->birthDate,
                    'blood_type' => $dto->bloodType,
                ]
            );

            $patient = Patient::updateOrCreate(
                [
                    'person_id' => $person->id,
                    'patient_id' => $dto->patientId,
                    'ref_patient_id' => $dto->refPatientId,
                ]
            );

            // Sync phones, emails, guardian (sama seperti sebelumnya)
            $identities = $dto->identities ?? [];
            foreach ($identities as $identity) {
                $person->identities()->updateOrCreate(
                    ['identity_type' => $identity->type],
                    ['number' => $identity->number],
                    ['issued_at' => $identity->issued_at],
                    ['expired_at' => $identity->expired_at],
                    ['country_code' => $identity->countryCode],
                    ['is_primary' => $identity->isPrimary],
                );
            };

            // Sync phones
            $phones = $dto->phones ?? [];
            foreach ($phones as $phone) {
                $person->phones()->updateOrCreate(
                    ['number' => $phone->number],
                    ['country_code' => $phone->country_code],
                    ['is_primary' => $phone->isPrimary],
                );
            };


            $emails = $dto->emails ?? [];
            foreach ($emails as $email) {
                $person->emails()->updateOrCreate(
                    ['email' => $email],
                    ['is_primary' => $email->isPrimary],
                );
            };

            // Sync address
            $address = $dto->address ?? [];
            $person->address()->updateOrCreate(
                ['street' => $address->street],
                ['city' => $address->city],
                ['state' => $address->state],
                ['zip_code' => $address->zipCode],
                ['country' => $address->country],
            );
        });
    }
}
