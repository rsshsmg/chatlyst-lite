<?php

namespace App\Services\HISSync\Traits;

use App\Models\Person;
use App\Models\Identity;
use App\Models\Phone;
use App\Models\Email;
use App\Services\HISSync\DTO\PersonDTO;
use App\Exceptions\DuplicateDataException;
use Illuminate\Support\Facades\DB;

trait HasPersonDetail
{
    public Person $person;
    public bool $isForce = false;

    public function setPerson(Person $person): void
    {
        $this->person = $person;
    }

    public function getPerson(): Person
    {
        return $this->person;
    }

    public function setForce(bool $isForce): void
    {
        $this->isForce = $isForce;
    }

    public function getIsForce(): bool
    {
        return $this->isForce;
    }

    /**
     * Check for duplicate data across person, identities, phones, and emails
     *
     * @param PersonDTO $person
     * @param array $identities Array of IdentityDTO
     * @param array $phones Array of PhoneDTO
     * @param array $emails Array of EmailDTO
     * @param bool $force If true, allow duplicates to proceed
     * @return array Duplicate information
     * @throws DuplicateDataException If duplicates found and force=false
     */
    public function checkDuplicates(
        PersonDTO $person,
        array $identities = [],
        array $phones = [],
        array $emails = []
    ): array {
        $duplicates = [
            'person' => null,
            'identities' => [],
            'phones' => [],
            'emails' => [],
            'summary' => [
                'has_duplicates' => false,
                'total_count' => 0,
            ]
        ];

        // Check person duplicate
        $personDuplicate = Person::where('full_name', $person->fullName)
            ->whereDate('date_of_birth', $person->birthDate)
            ->where('gender', $person->gender)
            ->first();

        if ($personDuplicate) {
            $duplicates['person'] = [
                'id' => $personDuplicate->id,
                'full_name' => $personDuplicate->full_name,
                'date_of_birth' => $personDuplicate->date_of_birth,
                'gender' => $personDuplicate->gender,
            ];
            $duplicates['summary']['has_duplicates'] = true;
            $duplicates['summary']['total_count']++;
        }

        // Check identity duplicates
        foreach ($identities as $index => $identity) {
            $existingIdentity = Identity::where('identity_type', $identity->identityType)
                ->where('number', $identity->number)
                ->first();

            if ($existingIdentity) {
                $duplicates['identities'][$index] = [
                    'existing_person_id' => $existingIdentity->person_id,
                    'identity_type' => $identity->identityType,
                    'number' => $identity->number,
                ];
                $duplicates['summary']['has_duplicates'] = true;
                $duplicates['summary']['total_count']++;
            }
        }

        // Check phone duplicates
        foreach ($phones as $index => $phone) {
            $formattedNumber = $this->formatToIndonesiaE164($phone->number);
            $existingPhone = Phone::where('number', $formattedNumber)->first();

            if ($existingPhone) {
                $duplicates['phones'][$index] = [
                    'existing_person_id' => $existingPhone->person_id,
                    'number' => $formattedNumber,
                ];
                $duplicates['summary']['has_duplicates'] = true;
                $duplicates['summary']['total_count']++;
            }
        }

        // Check email duplicates
        foreach ($emails as $index => $email) {
            $normalizedEmail = strtolower(trim($email->email));
            $existingEmail = Email::where('email', $normalizedEmail)->first();

            if ($existingEmail) {
                $duplicates['emails'][$index] = [
                    'existing_person_id' => $existingEmail->person_id,
                    'email' => $normalizedEmail,
                ];
                $duplicates['summary']['has_duplicates'] = true;
                $duplicates['summary']['total_count']++;
            }
        }

        // Throw exception if duplicates found and force=false
        if ($duplicates['summary']['has_duplicates'] && !$this->isForce) {
            throw new DuplicateDataException(
                $duplicates,
                'Duplicate data detected. Use force=true to proceed with update.'
            );
        }

        return $duplicates;
    }

    private function syncPerson(PersonDTO $person): void
    {
        $this->person = Person::updateOrCreate(
            [
                'full_name' => $person->fullName,
                'date_of_birth' => $person->birthDate,
                'gender' => $person->gender,
            ],
            [
                'nickname' => $person->nickname,
                'place_of_birth' => $person->birthPlace,
                'mother_name' => $person->motherName,
                'blood_type' => $person->bloodType,
                'religion' => $person->religion,
                'marital_status' => $person->maritalStatus,
                'education_id' => $person->educationId,
                'job_title_id' => $person->jobTitleId,
                'lang_code' => $person->langCode,
                'ethnicity_code' => $person->ethnicityCode,
                'is_foreigner' => $person->isForeigner,
                'nationality' => $person->nationality,
            ]
        );
    }

    private function syncIdentities(?array $identities): void
    {
        if (empty($identities)) {
            return;
        }

        foreach ($identities as $key => $identity) {
            $isPrimary = false;

            if ($key === 0) {
                $isPrimary = $this->person->identities()->count() === 0;
            }

            $this->person->identities()->updateOrCreate(
                [
                    'number' => $identity->number,
                    'identity_type' => $identity->identityType,
                ],
                [
                    'identity_type' => $identity->identityType,
                    'number' => $identity->number,
                    'issued_at' => $identity->issuedAt,
                    'expired_at' => $identity->expiredAt,
                    'country_code' => $identity->countryCode,
                    'image_id' => $identity->imageId,
                    'is_primary' => $isPrimary,
                ]
            );
        }
    }

    private function syncPhones(?array $phones): void
    {
        if (empty($phones)) {
            return;
        }

        foreach ($phones as $key => $phone) {
            $isPrimary = false;

            if ($key === 0) {
                $isPrimary = $this->person->phones()->count() === 0;
            }

            $this->person->phones()->updateOrCreate(
                [
                    'number' => $this->formatToIndonesiaE164($phone->number),
                ],
                [
                    'country_code' => $phone->countryCode,
                    'is_whatsapp' => $phone->isWhatsapp,
                    'verified_at' => $phone->verifiedAt,
                    'is_active' => $phone->isActive,
                    'is_primary' => $isPrimary,
                ]
            );
        }
    }

    private function syncEmails(?array $emails): void
    {
        if (empty($emails)) {
            return;
        }

        foreach ($emails as $key => $email) {
            $isPrimary = false;

            if ($key === 0) {
                $isPrimary = $this->person->emails()->count() === 0;
            }

            $this->person->emails()->updateOrCreate(
                [
                    'email' => $email->email,
                ],
                [
                    'email' => $email->email,
                    'verified_at' => $email->verifiedAt,
                    'is_primary' => $isPrimary,
                ]
            );
        }
    }

    private function syncAddresses(?array $addresses): void
    {
        if (empty($addresses)) {
            return;
        }

        foreach ($addresses as $key => $address) {
            $isPrimary = false;

            if ($key === 0) {
                $isPrimary = $this->person->addresses()->count() === 0;
            }

            $this->person->addresses()->updateOrCreate(
                [
                    'address' => $address->address,
                    'address_type' => $address->addressType,
                ],
                [
                    'address_type' => $address->addressType,
                    'address' => $address->address,
                    'country_id' => $address->countryId,
                    'country_code' => $address->countryCode,
                    'subdistrict_id' => $address->subDistrictId,
                    'postal_code' => $address->postalCode,
                    'is_primary' => $isPrimary,
                ]
            );
        }
    }

    private function formatToIndonesiaE164(string $phone): string
    {
        // Hilangkan spasi, strip, dll, tapi biarkan + di awal
        $phone = preg_replace('/(?!^\+)\D+/', '', $phone);

        // Jika mulai dengan +62 -> sudah benar
        if (strpos($phone, '+62') === 0) {
            return $phone;
        }

        // Jika mulai dengan 0 -> ganti jadi +62
        if (strpos($phone, '0') === 0) {
            return '+62' . substr($phone, 1);
        }

        // Jika mulai dengan 62 tanpa + -> tambahkan +
        if (strpos($phone, '62') === 0) {
            return '+' . $phone;
        }

        // Default: asumsikan nomor lokal -> tambahkan +62
        return '+62' . $phone;
    }
}
