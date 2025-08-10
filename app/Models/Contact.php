<?php

namespace App\Models;

use App\Enums\ContactType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contact extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'contact_type',
        'value',
        'ownerable_id',
        'ownerable_type',
        'verified_at',
    ];

    protected $casts = [
        'contact_type' => ContactType::class,
    ];

    // public function patient_contact(): HasMany
    // {
    //     return $this->hasMany(PatientContact::class);
    // }

    public function ownerable(): MorphTo
    {
        return $this->morphTo();
    }
}
