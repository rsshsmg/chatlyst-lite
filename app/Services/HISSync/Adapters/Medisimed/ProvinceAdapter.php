<?php

namespace App\Services\HISSync\Adapters\Medisimed;

use App\Services\HISSync\BaseSyncAdapter;
use App\Services\HISSync\DTO\ProvinceDTO;
use App\Services\HISSync\DTO\RegencyDTO;
use App\Services\HISSync\DTO\DistrictDTO;
use App\Services\HISSync\DTO\SubDistrictDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProvinceAdapter extends BaseSyncAdapter
{
    private $defaultCountryId = 103; // ID Negara Indonesia

    /**
     * Get all provinces with their complete hierarchy in a single optimized query
     */
    public function getAll(): Collection
    {
        $this->startTime = microtime(true);

        // Single optimized query to fetch all hierarchical data
        $hierarchicalData = DB::connection('hisdb')
            ->table('PROPINSI as p')
            ->leftJoin('KABUPATEN as k', 'p.KD_PROPINSI', '=', 'k.KD_PROPINSI')
            ->leftJoin('KECAMATAN as c', 'k.KD_KABUPATEN', '=', 'c.KD_KABUPATEN')
            ->leftJoin('KELURAHAN as s', 'c.KD_KECAMATAN', '=', 's.KD_KECAMATAN')
            ->select([
                'p.KD_PROPINSI as province_code',
                'p.PROPINSIN as province_name',
                'k.KD_KABUPATEN as regency_code',
                'k.KABUPATEN as regency_name',
                'c.KD_KECAMATAN as district_code',
                'c.KECAMATAN as district_name',
                's.KD_KELURAHAN as subdistrict_code',
                's.KELURAHAN as subdistrict_name'
            ])
            ->orderBy('p.KD_PROPINSI')
            ->orderBy('k.KD_KABUPATEN')
            ->orderBy('c.KD_KECAMATAN')
            ->orderBy('s.KD_KELURAHAN')
            ->get();

        // Build hierarchical structure in memory
        $provinces = collect();
        $provinceMap = [];
        $regencyMap = [];
        $districtMap = [];

        foreach ($hierarchicalData as $row) {
            // Build province
            if (!isset($provinceMap[$row->province_code])) {
                $province = new ProvinceDTO(
                    code: $row->province_code,
                    name: $row->province_name,
                    countryId: $this->defaultCountryId,
                    regencies: collect()
                );
                $provinceMap[$row->province_code] = $province;
                $provinces->push($province);
            }

            // Skip if no regency data
            if (!$row->regency_code) continue;

            // Build regency
            $regencyKey = $row->province_code . '_' . $row->regency_code;
            if (!isset($regencyMap[$regencyKey])) {
                $regency = new RegencyDTO(
                    code: $row->regency_code,
                    name: $row->regency_name,
                    provinceId: $row->province_code,
                    districts: collect()
                );
                $regencyMap[$regencyKey] = $regency;
                $provinceMap[$row->province_code]->regencies->push($regency);
            }

            // Skip if no district data
            if (!$row->district_code) continue;

            // Build district
            $districtKey = $regencyKey . '_' . $row->district_code;
            if (!isset($districtMap[$districtKey])) {
                $district = new DistrictDTO(
                    code: $row->district_code,
                    name: $row->district_name,
                    regencyId: $row->regency_code,
                    subdistricts: collect()
                );
                $districtMap[$districtKey] = $district;
                $regencyMap[$regencyKey]->districts->push($district);
            }

            // Skip if no subdistrict data
            if (!$row->subdistrict_code) continue;

            // Build subdistrict
            $subdistrict = new SubDistrictDTO(
                code: $row->subdistrict_code,
                name: $row->subdistrict_name,
                districtId: $row->district_code
            );
            $districtMap[$districtKey]->subdistricts->push($subdistrict);
        }

        $this->endTime = microtime(true);
        return $provinces;
    }

    /**
     * Get single province by ID with complete hierarchy
     */
    public function getById(string $id): ?object
    {
        $this->startTime = microtime(true);

        $hierarchicalData = DB::connection('hisdb')
            ->table('PROPINSI as p')
            ->leftJoin('KABUPATEN as k', 'p.KD_PROPINSI', '=', 'k.KD_PROPINSI')
            ->leftJoin('KECAMATAN as c', 'k.KD_KABUPATEN', '=', 'c.KD_KABUPATEN')
            ->leftJoin('KELURAHAN as s', 'c.KD_KECAMATAN', '=', 's.KD_KECAMATAN')
            ->where('p.KD_PROPINSI', $id)
            ->select([
                'p.KD_PROPINSI as province_code',
                'p.PROPINSIN as province_name',
                'k.KD_KABUPATEN as regency_code',
                'k.KABUPATEN as regency_name',
                'c.KD_KECAMATAN as district_code',
                'c.KECAMATAN as district_name',
                's.KD_KELURAHAN as subdistrict_code',
                's.KELURAHAN as subdistrict_name'
            ])
            ->orderBy('k.KD_KABUPATEN')
            ->orderBy('c.KD_KECAMATAN')
            ->orderBy('s.KD_KELURAHAN')
            ->get();

        if ($hierarchicalData->isEmpty()) {
            $this->endTime = microtime(true);
            return null;
        }

        // Build single province with hierarchy
        $province = new ProvinceDTO(
            code: $hierarchicalData->first()->province_code,
            name: $hierarchicalData->first()->province_name,
            countryId: $this->defaultCountryId,
            regencies: collect()
        );

        $regencyMap = [];
        $districtMap = [];

        foreach ($hierarchicalData as $row) {
            // Skip if no regency data
            if (!$row->regency_code) continue;

            // Build regency
            if (!isset($regencyMap[$row->regency_code])) {
                $regency = new RegencyDTO(
                    code: $row->regency_code,
                    name: $row->regency_name,
                    provinceId: $row->province_code,
                    districts: collect()
                );
                $regencyMap[$row->regency_code] = $regency;
                $province->regencies->push($regency);
            }

            // Skip if no district data
            if (!$row->district_code) continue;

            // Build district
            $districtKey = $row->regency_code . '_' . $row->district_code;
            if (!isset($districtMap[$districtKey])) {
                $district = new DistrictDTO(
                    code: $row->district_code,
                    name: $row->district_name,
                    regencyId: $row->regency_code,
                    subdistricts: collect()
                );
                $districtMap[$districtKey] = $district;
                $regencyMap[$row->regency_code]->districts->push($district);
            }

            // Skip if no subdistrict data
            if (!$row->subdistrict_code) continue;

            // Build subdistrict
            $subdistrict = new SubDistrictDTO(
                code: $row->subdistrict_code,
                name: $row->subdistrict_name,
                districtId: $row->district_code
            );
            $districtMap[$districtKey]->subdistricts->push($subdistrict);
        }

        $this->endTime = microtime(true);
        return $province;
    }
}
