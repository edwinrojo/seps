<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class SiteValidationPurpose extends Model
{
    use HasUlids;

    protected $fillable = [
        'site_validation_id',
        'validation_purpose_id',
    ];
}
