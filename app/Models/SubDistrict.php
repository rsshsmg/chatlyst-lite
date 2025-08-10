<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubDistrict extends Model
{
    use HasFactory;

    protected $table = 'subdistricts';

    protected $fillable = [
        'code',
        'name',
        'district_id'
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
