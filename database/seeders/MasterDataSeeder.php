<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Clinic;
use App\Models\Specialization;
use App\Models\Doctor;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedClinics();
        $this->seedSpecializations();
        $this->seedDoctors();
    }

    protected function seedClinics(): void
    {
        $path = database_path('data/clinics.json');
        if (! File::exists($path)) {
            return;
        }

        $items = json_decode(File::get($path), true);
        foreach ($items as $item) {
            // Use updateOrCreate keyed by slug to avoid duplicates
            Clinic::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'code' => $item['code'] ?? null,
                    'name' => $item['name'] ?? null,
                    'slug' => $item['slug'] ?? null,
                    'created_at' => $item['created_at'] ?? now(),
                    'updated_at' => $item['updated_at'] ?? now(),
                ]
            );
        }
    }

    protected function seedSpecializations(): void
    {
        $path = database_path('data/specializations.json');
        if (! File::exists($path)) {
            return;
        }

        $items = json_decode(File::get($path), true);
        foreach ($items as $item) {
            Specialization::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'code' => $item['code'] ?? null,
                    'name' => $item['name'] ?? null,
                    'slug' => $item['slug'] ?? null,
                    'created_at' => $item['created_at'] ?? now(),
                    'updated_at' => $item['updated_at'] ?? now(),
                ]
            );
        }
    }

    protected function seedDoctors(): void
    {
        $path = database_path('data/doctors.json');
        if (! File::exists($path)) {
            return;
        }

        $items = json_decode(File::get($path), true);

        foreach ($items as $item) {
            Doctor::updateorCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'] ?? null,
                    'title' => $item['title'] ?? null,
                    'initials' => $item['initials'] ?? null,
                    'license_no' => $item['license_no'] ?? null,
                    'dpjp_code' => $item['dpjp_code'] ?? null,
                    'fhir_code' => $item['fhir_code'] ?? null,
                    'status' => $item['status'],
                    'created_at' => $item['created_at'] ?? now(),
                    'updated_at' => $item['updated_at'] ?? now(),
                ]
            );

            // Use code or slug unique key to upsert
            if (! empty($data['code'])) {
                Doctor::updateOrCreate(['code' => $data['code']], $data);
            }
        }
    }
}
