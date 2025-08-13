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
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PersonAdapter extends BaseSyncAdapter
{
    public $limit = 10;

    public function getAll(): iterable
    {
        $limit = $this->limit;

        $this->startTime = microtime(true);

        $pasienCols = Schema::connection('hisdb')->getColumnListing('PASIEN_AFILIASI');

        $pasienSelect = array_map(fn($col) => "PASIEN.$col as pasien_$col", $pasienCols);

        $rows = DB::connection('hisdb')
            ->table('PASIEN_AFILIASI')
            ->when(config('app.debug') === true, fn(Builder $query, $limit) => $query->limit($limit))
            ->get();

        foreach ($rows as $row) {
            yield $this->mapToDTO($row);
        }

        $this->endTime = microtime(true);
    }

    public function getById(string $id): ?object
    {
        $this->startTime = microtime(true);

        $row = DB::connection('hisdb')
            ->table('PASIEN_AFILIASI')
            ->where('KD_PASIEN', $id)->first();

        $this->endTime = microtime(true);

        return $row ? $this->mapToDTO($row) : null;
    }

    protected function mapToDTO(object $row): PersonDTO
    {
        $gender = ($row->JENIS_KELAMIN == 1) ? Gender::Male : Gender::Female;

        $identities = [];
        if (!empty($row->NIK) && $row->NIK !== '-') {
            $identities = [
                new IdentityDTO(
                    personId: null, // Will be set later when saving
                    identityType: IdentityType::KTP->value,
                    number: $row->NIK,
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
                    number: trim($raw_phone),
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
                addressType: AddressType::RESIDENTIAL->value,
                address: $row->ALAMAT,
                countryId: 103,
                countryCode: 'ID',
                subDistrictId: $this->mapSubDistrict($row->KD_KELURAHAN),
                postalCode: $row->KD_POS,
                isPrimary: true,
            );
        }

        return new PersonDTO(
            fullName: $row->NAMAPASIEN,
            nickname: null,
            gender: $gender,
            birthPlace: $row->TEMPAT_LAHIR,
            birthDate: $row->TGL_LAHIR,
            motherName: null,
            bloodType: $this->mapBloodType($row->GOL_DARAH)->value,
            religion: $this->mapReligion($row->AGAMA)->value,
            maritalStatus: $this->mapMaritalStatus($row->STATUS_MARITA)->value,
            educationId: $this->mapEducation($row->KD_PENDIDIKAN),
            jobTitleId: $this->mapJobTitle($row->KD_PEKERJAAN),
            langCode: $this->mapLangCode(null),
            ethnicityCode: null,
            isForeigner: false,
            nationality: 'ID',
            identities: $identities,
            phones: $phones,
            emails: $emails,
            addresses: $addresses,
        );
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
        if (is_null($code)) return null;

        return Education::where('code', $code)->first()?->id;
    }

    protected function mapJobTitle($code): mixed
    {
        if (is_null($code)) return null;

        return JobTitle::where('code', $code)->first()?->id;
    }

    protected function mapSubDistrict($code): mixed
    {
        if (is_null($code)) return null;

        return SubDistrict::where('code', $code)->first()?->id;
    }
}
