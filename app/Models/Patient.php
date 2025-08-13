<?php

namespace App\Models;

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\ReligionType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;

class Patient extends Model
{
    use HasUuids, SoftDeletes, HasFactory, HasTags;

    protected $fillable = [
        'patient_code',
        'ref_patient_code',
        'person_id',
    ];

    protected $casts = [];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function guardians()
    {
        return $this->belongsToMany(Person::class, 'patient_guardian')
            ->withPivot('relation_type')
            ->withTimestamps();
    }

    public function guarantors(): BelongsToMany
    {
        return $this->belongsToMany(Guarantor::class, 'patient_guarantor')
            ->withPivot(['member_number', 'is_primary', 'valid_from', 'valid_to'])
            ->withTimestamps();
    }
}
