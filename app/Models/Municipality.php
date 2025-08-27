<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipality extends Model
{
    use HasUlids;

    protected $fillable = [
        'province_id',
        'municipality_code',
        'name',
        'old_name',
        'is_capital',
        'is_city',
        'is_municipality',
        'province_code',
        'district_code',
        'region_code',
        'island_group_code',
        'psgc_10_digit_code',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function barangays(): HasMany
    {
        return $this->hasMany(Barangay::class);
    }
}
