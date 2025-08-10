<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'regency_id'
    ];

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function subdistricts(): HasMany
    {
        return $this->hasMany(Subdistrict::class);
    }
}
