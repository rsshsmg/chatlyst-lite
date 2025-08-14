<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class Phone extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'person_id',
        'number',
        'country_code',
        'is_whatsapp',
        'verified_at',
        'is_active',
        'is_primary',
    ];

    protected $casts = [
        'is_whatsapp' => 'boolean',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    protected function isVerified(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->verified_at ? true : false,
        );
    }

    protected function isValid(): Attribute
    {
        return Attribute::make(
            get: fn() => preg_match('/^\+?[1-9]\d{7,14}$/', $this->number) ? true : false,
        );
    }

    // Mutator untuk auto-convert ke E.164
    public function setNumberAttribute($value)
    {
        if (! empty($value) && ! empty($this->country_code)) {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $parsedNumber = $phoneUtil->parse($value, $this->country_code);
                $value = $phoneUtil->format($parsedNumber, PhoneNumberFormat::E164);
            } catch (NumberParseException $e) {
                // Biarkan nilai asli jika parsing gagal
            }
        }

        $this->attributes['number'] = $value;
    }
}
