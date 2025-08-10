<?php

namespace App\Models;

use App\Enums\RelationType;
use Illuminate\Database\Eloquent\Model;

class PatientGuardian extends Model
{
    protected $table = 'patient_guardian';

    protected $fillable = [
        'patient_id',
        'person_id',
        'relation_type',
        'note',
    ];

    protected $casts = [
        'relation_type' => RelationType::class,
    ];
}
