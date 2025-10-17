<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ValidationPurpose extends Model
{
    use HasUlids;

    protected $fillable = [
        'purpose',
        'description',
        'is_iv',
    ];

    public function site_validations(): HasMany
    {
        return $this->hasMany(SiteValidation::class, 'validation_purpose_id');
    }
}
