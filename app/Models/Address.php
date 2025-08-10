<?php

namespace App\Models;

use App\Enums\AddressType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'person_id',
        'address_type',
        'address',
        'subdistrict_id',
        'country_id',
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

    public function subdistrict(): BelongsTo
    {
        return $this->belongsTo(Subdistrict::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
