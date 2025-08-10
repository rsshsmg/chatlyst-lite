<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'phone',
        'timezones',
        'numeric_code',
        'iso3',
        'capital',
        'currency',
        'currency_symbol',
        'currency_code',
        'currency_name'
    ];

    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }
}
