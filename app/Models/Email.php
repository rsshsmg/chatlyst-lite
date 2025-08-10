<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'person_id',
        'email',
        'verified_at',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
