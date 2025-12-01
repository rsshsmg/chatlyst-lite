<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clinic extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'slug', 'address', 'status', 'visibility'];

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_schedules')
            ->withPivot(['id', 'day_of_week', 'start_time', 'end_time'])
            ->withTimestamps();
    }
}
