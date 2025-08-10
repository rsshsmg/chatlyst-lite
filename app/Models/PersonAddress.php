<?php

namespace App\Models;

use App\Enums\AddressType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonAddress extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'address_type',
        'address',
        'city',
        'province',
        'country',
        'postal_code',
        'is_primary',
    ];

    protected $casts = [
        'address_type' => AddressType::class,
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
