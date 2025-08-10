<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PersonContact extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'person_id',
        'relation_type',
        'patient_id',
        'is_primary',
        'notes'
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
