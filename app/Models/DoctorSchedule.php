<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'clinic_id', 'day_of_week', 'start_time', 'end_time'];

    protected $casts = [
        'day_of_week' => \App\Enums\DayOfWeek::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if ($model->start_time && $model->end_time) {
                $start = Carbon::parse($model->start_time);
                $end = Carbon::parse($model->end_time);
                if (! $end->greaterThan($start)) {
                    throw ValidationException::withMessages([
                        'end_time' => 'End time must be after start time.',
                    ]);
                }
            }
        });
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
