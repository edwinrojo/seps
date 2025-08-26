<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'website',
        'email',
        'mobile_number',
        'landline_number',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
