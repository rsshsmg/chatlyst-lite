<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DoctorScheduleController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->query('name');
        $clinicParam = $request->query('clinic'); // id atau slug
        $day = $request->query('day');

        // helper: apakah clinicParam adalah numeric id
        $clinicIsId = $clinicParam !== null && ctype_digit((string) $clinicParam);

        // Build query dari Clinic (root) supaya outputnya nested: clinic > doctors > schedules
        $query = Clinic::with([
            // Jika relasi Clinic->doctors adalah belongsToMany lewat doctor_schedules,
            // ini akan meng-include doctors yang punya schedule di clinic tersebut.
            'doctors' => function ($q) use ($clinicParam, $clinicIsId) {
                // pastikan dokter punya schedule (opsional)
                $q->whereHas('schedules');

                // jika ingin batasi dokter yg punya jadwal di clinic tertentu:
                if ($clinicParam) {
                    $q->whereHas('schedules', function ($sq) use ($clinicParam, $clinicIsId) {
                        if ($clinicIsId) {
                            $sq->where('clinic_id', $clinicParam);
                        } else {
                            $sq->whereHas('clinic', function ($cq) use ($clinicParam) {
                                $cq->where('slug', $clinicParam);
                            });
                        }
                    });
                }
            },

            // eager load turunannya
            'doctors.specializations',
            'doctors.leaves' => function ($q) {
                $q->where('end_date', '>=', Carbon::today());
            },
            // kita bisa mem-filter/order schedules berdasarkan request params
            'doctors.schedules' => function ($q) use ($clinicParam, $clinicIsId, $day) {
                // only select necessary columns to reduce payload
                $q->select('id', 'doctor_id', 'clinic_id', 'day_of_week', 'start_time', 'end_time');

                if ($clinicParam) {
                    if ($clinicIsId) {
                        $q->where('clinic_id', $clinicParam);
                    } else {
                        $q->whereHas('clinic', function ($cq) use ($clinicParam) {
                            $cq->where('slug', $clinicParam);
                        });
                    }
                }

                if ($day) {
                    $q->where('day_of_week', $day);
                }

                $q->orderBy('day_of_week', 'asc')->orderBy('start_time', 'asc');
            },
        ]);

        // Filter: search by clinic name OR dokter name (lebih fleksibel)
        if ($name) {
            $query->where(function ($q) use ($name) {
                $q->where('name', 'like', "%{$name}%")
                    ->orWhereHas('doctors', function ($dq) use ($name) {
                        $dq->where('name', 'like', "%{$name}%");
                    });
            });
        }

        // Jika user ingin membatasi ke satu clinic via param (id atau slug), lakukan filter di root
        if ($clinicParam) {
            if (is_numeric($clinicParam)) {
                $query->where('id', $clinicParam);
            } else {
                $query->where('slug', $clinicParam);
            }
        }

        // Filter by day (pastikan clinics yang diambil punya doctor dengan schedule di hari itu)
        if ($day) {
            $query->whereHas('doctors.schedules', function ($sq) use ($day) {
                $sq->where('day_of_week', $day);
            });
        }

        // Paginate clinics (ini menghasilkan struktur nested: clinic -> doctors -> schedules)
        $clinics = $query->paginate(12)->withQueryString();

        // Untuk dropdown/filter UI: semua clinic
        $clinicsList = Clinic::orderBy('name')->get();
        $doctorsCount = Doctor::count();
        $clinicsCount = Clinic::count();
        // Determine leave status per doctor for display
        $today = Carbon::today();

        return view('public.doctor_schedule', compact('clinics', 'clinicsList', 'today', 'doctorsCount', 'clinicsCount'));
    }
}
