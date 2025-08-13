<?php

namespace App\Services\HISSync\Handlers;

use App\Models\Address;
use App\Models\Email;
use App\Models\Identity;
use App\Models\Patient;
use App\Services\HISSync\Contracts\SyncHandlerInterface;
use App\Services\HISSync\DTO\PatientDataDTO;
use App\Models\Person;
use App\Models\Phone;
use App\Services\HISSync\BaseSyncHandler;
use App\Services\HISSync\Traits\HasPersonDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersonSyncHandler extends BaseSyncHandler
{
    use HasPersonDetail;

    public function handle(object $dto): mixed
    {
        /** @var PersonDTO $dto */
        return DB::transaction(function () use ($dto) {
            $this->startTime = microtime(true);

            // Create or update person
            $this->person = Person::updateOrCreate(
                [
                    'full_name' => $dto->fullName,
                    'date_of_birth' => $dto->birthDate,
                    'gender' => $dto->gender,
                ],
                [
                    'full_name' => $dto->fullName,
                    'nickname' => $dto->nickname,
                    'place_of_birth' => $dto->birthPlace,
                    'date_of_birth' => $dto->birthDate,
                    'mother_name' => $dto->motherName,
                    'blood_type' => $dto->bloodType,
                    'religion' => $dto->religion,
                    'marital_status' => $dto->maritalStatus,
                    'education_id' => $dto->educationId,
                    'job_title_id' => $dto->jobTitleId,
                    'lang_code' => $dto->langCode,
                    'ethnicity_code' => $dto->ethnicityCode,
                    'is_foreigner' => $dto->isForeigner,
                    'nationality' => $dto->nationality,
                ]
            );

            // // Process identities
            // $this->processIdentities($person, $dto->identities);

            // // Process phones
            // $this->processPhones($person, $dto->phones);

            // // Process emails
            // $this->processEmails($person, $dto->emails);

            // // Process addresses
            // $this->processAddresses($person, $dto->addresses);

            // Process identities
            $this->syncIdentities($dto->identities);

            // Process phones
            $this->syncPhones($dto->phones);

            // Process emails
            $this->syncEmails($dto->emails);

            // Process addresses
            $this->syncAddresses($dto->addresses);

            $this->endTime = microtime(true);

            return $this->person;
        });
    }

    // private function processIdentities(Person $person, ?array $identities): void
    // {
    //     if (empty($identities)) {
    //         return;
    //     }

    //     foreach ($identities as $key => $identity) {
    //         $isPrimary = false;

    //         if ($key === 0) {
    //             $isPrimary = $person->identities()->count() === 0;
    //         }

    //         $person->identities()->updateOrCreate(
    //             [
    //                 'number' => $identity->number,
    //                 'identity_type' => $identity->identityType,
    //             ],
    //             [
    //                 'identity_type' => $identity->identityType,
    //                 'number' => $identity->number,
    //                 'issued_at' => $identity->issuedAt,
    //                 'expired_at' => $identity->expiredAt,
    //                 'country_code' => $identity->countryCode,
    //                 'image_id' => $identity->imageId,
    //                 'is_primary' => $isPrimary,
    //             ]
    //         );
    //     }
    // }

    // private function processPhones(Person $person, ?array $phones): void
    // {
    //     if (empty($phones)) {
    //         return;
    //     }

    //     foreach ($phones as $key => $phone) {
    //         $isPrimary = false;

    //         if ($key === 0) {
    //             $isPrimary = $person->phones()->count() === 0;
    //         }

    //         $person->phones()->updateOrCreate(
    //             [
    //                 'number' => $phone->number,
    //             ],
    //             [
    //                 'country_code' => $phone->countryCode,
    //                 'is_whatsapp' => $phone->isWhatsapp,
    //                 'verified_at' => $phone->verifiedAt,
    //                 'is_active' => $phone->isActive,
    //                 'is_primary' => $isPrimary,
    //             ]
    //         );
    //     }
    // }

    // private function processEmails(Person $person, ?array $emails): void
    // {
    //     if (empty($emails)) {
    //         return;
    //     }

    //     foreach ($emails as $key => $email) {
    //         $isPrimary = false;

    //         if ($key === 0) {
    //             $isPrimary = $person->emails()->count() === 0;
    //         }

    //         $person->emails()->updateOrCreate(
    //             [
    //                 'email' => $email->email,
    //             ],
    //             [
    //                 'email' => $email->email,
    //                 'verified_at' => $email->verifiedAt,
    //                 'is_primary' => $isPrimary,
    //             ]
    //         );
    //     }
    // }

    // private function processAddresses(Person $person, ?array $addresses): void
    // {
    //     if (empty($addresses)) {
    //         return;
    //     }

    //     foreach ($addresses as $key => $address) {
    //         $isPrimary = false;

    //         if ($key === 0) {
    //             $isPrimary = $person->addresses()->count() === 0;
    //         }

    //         $person->addresses()->updateOrCreate(
    //             [
    //                 'address' => $address->address,
    //                 'address_type' => $address->addressType,
    //             ],
    //             [
    //                 'address_type' => $address->addressType,
    //                 'address' => $address->address,
    //                 'country_id' => $address->countryId,
    //                 'country_code' => $address->countryCode,
    //                 'subdistrict_id' => $address->subDistrictId,
    //                 'postal_code' => $address->postalCode,
    //                 'is_primary' => $isPrimary,
    //             ]
    //         );
    //     }
    // }
}
