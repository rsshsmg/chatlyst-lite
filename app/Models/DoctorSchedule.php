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

    // Keep as string for now until DayOfWeek DB migration is applied
    protected $casts = [
        'day_of_week' => 'string',
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

    public function getPeriodAttribute(): string
    {
        // parse jam dari start_at (asumsi format 'H:i:s' atau 'H:i')
        $start = Carbon::parse($this->start_time);
        $h = (int) $start->format('H');

        // Aturan: pagi 05-11, siang 12-16, malam 17-4
        if ($h >= 5 && $h <= 11) {
            return 'morning';
        }

        if ($h >= 12 && $h <= 16) {
            return 'afternoon';
        }

        // else malam (17-23 and 0-4)
        return 'night';
    }

    // optional: nicely formatted label
    public function getPeriodLabelAttribute(): string
    {
        return match ($this->period) {
            'morning' => 'Pagi',
            'afternoon' => 'Siang',
            'night' => 'Malam',
            default => ucfirst($this->period),
        };
    }
}
