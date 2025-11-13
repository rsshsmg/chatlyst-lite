<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = ['specialization_id', 'name', 'title', 'profile_photo_path', 'education', 'experience', 'status'];

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function leaves()
    {
        return $this->hasMany(DoctorLeave::class);
    }
}
