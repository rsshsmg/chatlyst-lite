<?php

namespace App\Services\HISSync\Adapters\Medisimed;

use App\Enums\AddressType;
use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\IdentityType;
use App\Enums\MaritalStatus;
use App\Enums\ReligionType;
use App\Models\Education;
use App\Models\JobTitle;
use App\Models\SubDistrict;
use App\Services\HISSync\BaseSyncAdapter;
use App\Services\HISSync\Contracts\SyncAdapterInterface;
use App\Services\HISSync\DTO\AddressDTO;
use App\Services\HISSync\DTO\EmailDTO;
use App\Services\HISSync\DTO\IdentityDTO;
use App\Services\HISSync\DTO\PatientDataDTO;
use App\Services\HISSync\DTO\PersonDTO;
use App\Services\HISSync\DTO\PhoneDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PatientAdapter extends BaseSyncAdapter
{
    public $limit = 10;

    public function getAll(): iterable
    {
        $limit = $this->limit;

        $this->startTime = microtime(true);

        $pasienCols = Schema::connection('hisdb')->getColumnListing('PASIEN');
        $keluargaCols = Schema::connection('hisdb')->getColumnListing('KELUARGA');

        $pasienSelect = array_map(fn($col) => "PASIEN.$col as pasien_$col", $pasienCols);
        $keluargaSelect = array_map(fn($col) => "KELUARGA.$col as keluarga_$col", $keluargaCols);

        $rows = DB::connection('hisdb')
            ->table('PASIEN')
            ->leftJoin('KELUARGA', 'KELUARGA.KD_PASIEN', '=', 'PASIEN.KD_PASIEN')
            ->select(array_merge($pasienSelect, $keluargaSelect))
            ->when(config('app.debug'), fn($query) => $query->limit(100))
            ->get();

        $results = $this->buildNestedAuto($rows, 'pasien_', 'guardians', 'keluarga_');

        foreach ($results as $row) {
            yield $this->mapToDTO($row);
        }

        $this->endTime = microtime(true);
    }

    public function getById(string $id): ?object
    {
        $this->startTime = microtime(true);

        $pasienCols = Schema::connection('hisdb')->getColumnListing('PASIEN');
        $keluargaCols = Schema::connection('hisdb')->getColumnListing('KELUARGA');

        $pasienSelect = array_map(fn($col) => "PASIEN.$col as pasien_$col", $pasienCols);
        $keluargaSelect = array_map(fn($col) => "KELUARGA.$col as keluarga_$col", $keluargaCols);

        $row = DB::connection('hisdb')
            ->table('PASIEN')
            ->leftJoin('KELUARGA', 'KELUARGA.KD_PASIEN', '=', 'PASIEN.KD_PASIEN')
            ->select(array_merge($pasienSelect, $keluargaSelect))
            ->where('KD_PASIEN', $id)->first();

        $row = $this->buildNestedAuto($row, 'pasien_', 'guardians', 'keluarga_');

        $this->endTime = microtime(true);

        return $row ? $this->mapToDTO($row) : null;
    }

    protected function mapToDTO(object $row): PatientDataDTO
    {
        $gender = ($row->JENIS_KELAMIN == 1) ? Gender::Male : Gender::Female;

        $identities = [];
        if (!empty($row->NO_PENGENAL) || $row->NO_PENGENAL == '-') {
            $identities = [
                new IdentityDTO(
                    personId: null, // Will be set later when saving
                    identityType: $this->mapIdentityType($row->TANDA_PENGENAL),
                    number: $row->NO_PENGENAL,
                    issuedAt: null,
                    expiredAt: null,
                    countryCode: 'ID',
                    imageId: null,
                    isPrimary: true,
                ),
            ];
        }

        $phones = [];
        if (!is_null($row->TELEPON) && !empty($row->TELEPON) && $row->TELEPON !== '-') {
            $raw_phones = preg_split('/[\/,]/', $row->TELEPON);

            foreach ($raw_phones as $raw_phone) {
                $raw_phone = trim($raw_phone);

                if ($raw_phone === '') {
                    continue;
                }

                $phones[] = new PhoneDTO(
                    personId: null,
                    number: $raw_phone,
                    countryCode: 'ID',
                    isWhatsapp: false,
                    verifiedAt: null,
                    isActive: true,
                    isPrimary: true,
                );
            }
        }

        $emails = [];
        if (!empty($row->EMAIL) && $row->EMAIL !== '-') {
            $emails[] = new EmailDTO(
                personId: null,
                email: $row->EMAIL,
                verifiedAt: null,
                isPrimary: true,
            );
        }

        $addresses = [];
        if (!empty($row->ALAMAT) && $row->ALAMAT !== '-') {
            $addresses[] = new AddressDTO(
                personId: null,
                addressType: AddressType::RESIDENTIAL,
                address: $row->ALAMAT,
                countryId: $row->WNI ? 103 : 103,
                countryCode: $row->WNI ? 'ID' : null,
                subDistrictId: $this->mapSubDistrict($row->KD_KELURAHAN),
                postalCode: $row->KD_POS,
                isPrimary: true,
            );
        }

        $person = new PersonDTO(
            fullName: $row->NAMAPASIEN,
            nickname: null,
            gender: $gender,
            birthPlace: $row->TEMPAT_LAHIR,
            birthDate: $row->TGL_LAHIR,
            motherName: null,
            bloodType: $this->mapBloodType($row->GOL_DARAH),
            religion: $this->mapReligion($row->AGAMA),
            maritalStatus: $this->mapMaritalStatus($row->STATUS_MARITA),
            educationId: $this->mapEducation($row->KD_PENDIDIKAN),
            jobTitleId: $this->mapJobTitle($row->KD_PEKERJAAN),
            langCode: $this->mapLangCode($row->BAHASA),
            ethnicityCode: null,
            isForeigner: $row->WNI ? false : true,
            nationality: $row->WNI ? 'ID' : null,
            identities: $identities,
            phones: $phones,
            emails: $emails,
            addresses: $addresses,
        );

        // Loop dari hasil nested query
        $guardians = $this->mapGuardians($row->guardians, $person);

        return new PatientDataDTO(
            patientCode: $row->KD_PASIEN,
            refPatientCode: $row->KD_PASIENOLD,
            person: $person,
            guardians: $guardians
        );
    }

    protected function mapGuardians($guardians, $person): array
    {
        $lifePartnerGender = match ($person->gender) {
            Gender::Male => Gender::Female, // Pasangan Laki-laki
            Gender::Female => Gender::Male, // Pasangan Perempuan
            default => null,
        };

        $guards = [];
        foreach ($guards as $guardian) {

            $guardGender = match ($guardian->HUBUNGAN) {
                1 => $lifePartnerGender,    // Suami/Istri
                2 => Gender::Male,          // Ayah
                3 => Gender::Female,        // Ibu
                4 => Gender::Male,          // Anak
                5 => Gender::Male,          // Saudara Kandung
                6 => Gender::Male,          // Kerabat Dekat
                7 => $person->gender,               // Diri Sendiri
                default => null,
            };

            $guardIdentities = [];
            if (!empty($guardian->NIK) && $guardian->NIK == '-') {
                $guardIdentities[] = new IdentityDTO(
                    personId: null, // Will be set later when saving
                    identityType: IdentityType::KTP,
                    number: $guardian->NIK,
                    issuedAt: null,
                    expiredAt: null,
                    countryCode: 'ID',
                    imageId: null,
                    isPrimary: true,
                );
            }

            $guardAddresses = [];
            if (!empty($guardian->ALAMAT) && $guardian->ALAMAT !== '-') {
                $guardAddresses[] = new AddressDTO(
                    personId: null,
                    addressType: AddressType::RESIDENTIAL,
                    address: $guardian->ALAMAT . ', ' . $guardian->KOTA,
                    countryId: 103,
                    countryCode: 'ID',
                    subDistrictId: null,
                    postalCode: $guardian->KD_POS,
                    isPrimary: true,
                );
            }

            $guardians[] = new PersonDTO(
                fullName: $guardian->NAMA,
                nickname: null,
                gender: $guardGender,
                birthPlace: null,
                birthDate: null,
                motherName: null,
                bloodType: null,
                religion: null,
                maritalStatus: null,
                educationId: null,
                jobTitleId: null,
                langCode: null,
                ethnicityCode: null,
                isForeigner: false,
                nationality: null,
                identities: $guardIdentities,
                phones: null,
                emails: null,
                addresses: $guardAddresses,
            );
        }

        return $guards;
    }

    protected function mapIdentityType($identity_type): IdentityType
    {
        return match ($identity_type) {
            '1' => IdentityType::KTP,
            '2' => IdentityType::SIM,
            '3' => IdentityType::STUDENTCARD,
            '4' => IdentityType::PASSPORT,
            '5' => IdentityType::OTHER,
            default => IdentityType::KTP,
        };
    }

    protected function mapBloodType($blood_type): BloodType
    {
        return match ($blood_type) {
            '1' => BloodType::A,
            '2' => BloodType::B,
            '3' => BloodType::AB,
            '4' => BloodType::O,
            default => BloodType::UNKNOWN,
        };
    }

    protected function mapLangCode($lang): string
    {
        return match ($lang) {
            '1' => 'id',
            '2' => 'zh-cn',
            '3' => 'en',
            default => 'id',
        };
    }

    protected function mapMaritalStatus($status): MaritalStatus
    {
        return match ($status) {
            '1' => MaritalStatus::Married,
            '2' => MaritalStatus::Single,
            '3' => MaritalStatus::Widow,
            '4' => MaritalStatus::Widower,
            '5' => MaritalStatus::Other,
            default => MaritalStatus::Other,
        };
    }

    protected function mapReligion($religion): ReligionType
    {
        return match ($religion) {
            '1' => ReligionType::Islam,
            '2' => ReligionType::Christian,
            '3' => ReligionType::Catholic,
            '4' => ReligionType::Hindu,
            '5' => ReligionType::Buddhist,
            '6' => ReligionType::Confucian,
            '7' => ReligionType::Other,
            default => ReligionType::Other,
        };
    }

    protected function mapEducation($code): mixed
    {
        return Education::where('code', $code)->first()?->id;
    }

    protected function mapJobTitle($code): mixed
    {
        return JobTitle::where('code', $code)->first()?->id;
    }

    protected function mapSubDistrict($code): mixed
    {
        if (is_null($code)) return null;

        return SubDistrict::where('code', $code)->first()?->id;
    }

    protected function buildNestedAuto($rows, string $parentPrefix, string $childKey, string $childPrefix)
    {
        return $rows
            ->groupBy($parentPrefix . 'KD_PASIEN') // asumsi primary key parent
            ->map(function ($items) use ($parentPrefix, $childKey, $childPrefix) {
                $parent = (object) [];
                $children = [];

                // Ambil parent dari record pertama
                foreach ($items[0] as $key => $value) {
                    if (str_starts_with($key, $parentPrefix)) {
                        $parent->{substr($key, strlen($parentPrefix))} = $value;
                    }
                }

                // Ambil children dari setiap record
                foreach ($items as $item) {
                    $child = (object) [];
                    $hasData = false;

                    foreach ($item as $key => $value) {
                        if (str_starts_with($key, $childPrefix)) {
                            $fieldName = substr($key, strlen($childPrefix));
                            $child->{$fieldName} = $value;
                            if (!is_null($value)) {
                                $hasData = true;
                            }
                        }
                    }

                    if ($hasData) {
                        $children[] = $child;
                    }
                }

                $parent->{$childKey} = $children;
                return $parent;
            })
            ->values();
    }
}
