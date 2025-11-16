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
        $query = Doctor::with(['specializations', 'schedules', 'leaves']);

        if ($name = $request->query('name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($clinic = $request->query('clinic')) {
            $query->whereHas('schedules', function ($q) use ($clinic) {
                $q->where('clinic_id', $clinic)
                    ->orWhereHas('clinic', function ($q2) use ($clinic) {
                        $q2->where('id', $clinic)->orWhere('slug', $clinic);
                    });
            });
        }

        if ($day = $request->query('day')) {
            $query->whereHas('schedules', function ($q) use ($day) {
                $q->where('day_of_week', $day);
            });
        }

        $doctors = $query->paginate(12)->withQueryString();
        $clinics = Clinic::orderBy('name')->get();
        $doctorsCount = Doctor::count();
        $clinicsCount = Clinic::count();
        // Determine leave status per doctor for display
        $today = Carbon::today();

        return view('public.doctor_schedule', compact('doctors', 'clinics', 'today', 'doctorsCount', 'clinicsCount'));
    }
}
