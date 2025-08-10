<?php

namespace App\Models;

use App\Enums\GuarantorType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guarantor extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'name',
        'email',
        'code',
        'guarantor_type',
        'description',
    ];

    protected $casts = [
        'guarantor_type' => GuarantorType::class,
    ];

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class)->withTimestamps();
    }
}
