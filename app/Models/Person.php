<?php

namespace App\Models;

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\ReligionType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Tags\HasTags;

class Person extends Model
{
    use HasUuids;
    use SoftDeletes;
    use HasFactory;
    use HasTags;

    protected $fillable = [
        'full_name',
        'nickname',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'mother_name',
        'blood_type',
        'religion',
        'marital_status',
        'education_id',
        'job_title_id',
        'lang_code',
        'ethnicity_code',
        'is_foreigner',
        'nationality',
    ];

    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',
        'gender' => Gender::class,
        'blood_type' => BloodType::class,
        'religion' => ReligionType::class,
        'marital_status' => MaritalStatus::class,
    ];

    public function getAgeAttribute()
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function scopeWithAge($query)
    {
        return $query->addSelect([
            'age' => DB::raw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())')
        ]);
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function identities(): HasMany
    {
        return $this->hasMany(Identity::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function education(): HasOne
    {
        return $this->hasOne(Education::class, 'id', 'education_id');
    }

    public function job_title(): HasOne
    {
        return $this->hasOne(JobTitle::class, 'id', 'job_title_id');
    }

    public function patientsAsGuardian()
    {
        return $this->belongsToMany(Patient::class, 'patient_guardian')
            ->withPivot('contact_type')
            ->withTimestamps();
    }
}
