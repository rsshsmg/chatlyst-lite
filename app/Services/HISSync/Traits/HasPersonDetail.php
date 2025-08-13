<?php

namespace App\Services\HISSync\Traits;

use App\Models\Person;

trait HasPersonDetail
{
    public Person $person;

    public function setPerson(Person $person): void
    {
        $this->person = $person;
    }

    public function getPerson(): Person
    {
        return $this->person;
    }

    private function hasIdentities(?array $identities): void
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

    private function hasPhones(?array $phones): void
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

    private function hasEmails(?array $emails): void
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

    private function hasAddresses(?array $addresses): void
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
