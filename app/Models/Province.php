<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasUlids;

    protected $fillable = [
        'psgc_code',
        'name',
        'region_code',
        'island_group_code',
        'psgc_10_digit_code',
    ];

    public function municipalities(): HasMany
    {
        return $this->hasMany(Municipality::class);
    }
}
