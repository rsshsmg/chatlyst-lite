<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Specialization;
use App\Models\Doctor;
use App\Models\DoctorSchedule;

class DoctorScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_shows_doctor()
    {
        // create specialization and doctor and schedule
        $spec = Specialization::create(['name' => 'Internist', 'slug' => 'internist']);
        $doc = Doctor::create(['code' => 'D001', 'name' => 'Dr. Budi', 'title' => 'Sp.PD', 'status' => 'available']);
        $doc->specializations()->attach($spec->id);
        $clinic = \App\Models\Clinic::create(['code' => 'PK000', 'name' => 'Poliklinik Utama', 'slug' => 'poliklinik-utama']);
        DoctorSchedule::create(['doctor_id' => $doc->id, 'clinic_id' => $clinic->id, 'day_of_week' => 'Monday', 'start_time' => '09:00:00', 'end_time' => '12:00:00']);

        $response = $this->get('/jadwal-dokter');
        $response->assertStatus(200);
        $response->assertSee('Dr. Budi');
        $response->assertSee('Internist');
    }
}
