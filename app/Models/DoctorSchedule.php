<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'day_of_week', 'start_time', 'end_time'];

    protected $casts = [
        'day_of_week' => \App\Enums\DayOfWeek::class,
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
