<?php

namespace App\Services\HISSync\Adapters\Medisimed;

use App\Enums\AddressType;
use App\Enums\Gender;
use App\Services\HISSync\Contracts\SyncAdapterInterface;
use App\Services\HISSync\DTO\PatientDataDTO;
use App\Services\HISSync\SyncAdapter;
use Illuminate\Support\Facades\DB;

class PatientAdapter extends SyncAdapter
{
    public function getAll(): iterable
    {
        $results = DB::connection('hisdb')->table('PASIEN')->get();

        foreach ($results as $row) {
            yield $this->mapToDTO($row);
        }
    }

    public function getById(string $id): ?object
    {
        $row = DB::connection('hisdb')->table('PASIEN')->where('KD_PASIEN', $id)->first();

        return $row ? $this->mapToDTO($row) : null;
    }

    protected function mapToDTO(object $row): PatientDataDTO
    {
        return new PatientDataDTO(
            patientId: $row->KD_PASIEN,
            refPatientId: $row->KD_PASIENOLD,
            fullName: $row->NAMAPASIEN,
            nickname: null,
            gender: ($row->JENIS_KELAMIN == 1) ? Gender::Male : Gender::Female,
            birthPlace: $row->TEMPAT_LAHIR,
            birthDate: $row->TGL_LAHIR,
            bloodType: $row->GOL_DARAH,
            nationality: $row->WNI ? 'ID' : null,
            phones: [
                'number' => $row->TELEPON,
                'country_code' => $row->WNI ? 'ID' : 'ID',
            ],
            emails: [$row->EMAIL],
            addresses: [
                [
                    'type' => AddressType::DOMICILE,
                    'address' => $row->ALAMAT,
                    'country_id' => $row->WNI ? 103 : 103,
                    'country_code' => $row->WNI ? 'ID' : null,
                    'subdistrict' => $row->KELURAHAN,
                    'postal_code' => $row->KODE_POS,
                    'is_primary' => true,
                ]
            ],
            guardian: [
                'name' => $row->guardian_name,
                'relation' => $row->guardian_relation,
                'phone' => $row->guardian_phone,
            ]
        );
    }
}
