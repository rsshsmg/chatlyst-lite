<?php

namespace App\Models;

use App\Enums\IdentityType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Identity extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'identity_type',
        'number',
        'issued_at',
        'expired_at',
        'image_id',
        'country_code',
        'is_primary',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expired_at' => 'date',
        'is_primary' => 'boolean',
        'identity_type' => IdentityType::class,
    ];

    public function type(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => strtolower($value),
        );
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
