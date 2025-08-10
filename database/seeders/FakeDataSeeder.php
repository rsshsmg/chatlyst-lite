<?php

namespace Database\Seeders;

use App\Enums\RelationType;
use App\Models\Address;
use App\Models\Education;
use App\Models\Email;
use App\Models\Guarantor;
use App\Models\Identity;
use App\Models\JobTitle;
use App\Models\Patient;
use App\Models\PatientGuarantor;
use App\Models\Person;
use App\Models\Phone;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Guarantor::factory()
            ->count(20)
            ->create();

        Patient::factory()
            ->withPatientPerson()
            ->count(100)
            ->create();

        $patients = Patient::all();
        $guarantors = Guarantor::all();

        $relationTypes = RelationType::cases();

        foreach ($patients as $patient) {

            $relationType = $relationTypes[array_rand($relationTypes)];

            $guardian = Person::factory()
                ->asGuardian()
                ->hasPhones(rand(1, 2))
                ->hasEmails(rand(0, 1))
                // ->hasAddresses(rand(0, 3))
                ->create();
            $note = 'Guardian note for ' . $guardian->name;

            $patient->guardians()->attach($guardian->id, [
                'relation_type' => $relationType,
                'note' => $note,
            ]);

            // Ambil 1-3 guarantor acak untuk setiap patient
            $randomGuarantors = $guarantors->random(rand(1, 3));

            foreach ($randomGuarantors as $guarantor) {
                // $patient->guarantors()->attach($guarantor->id);
                PatientGuarantor::factory()->create([
                    'patient_id' => $patient->id,
                    'guarantor_id' => $guarantor->id,
                ]);
            }
        }
    }
}
