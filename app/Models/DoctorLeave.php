<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorLeave extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'start_date', 'end_date', 'reason'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
