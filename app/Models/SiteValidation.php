<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SiteValidation extends Model
{
    use HasUlids;

    protected $fillable = [
        'supplier_id',
        'address_id',
        'twg_id',
        'validation_purpose_id',
        'validation_date',
        'purpose',
        'findings',
        'recommendations',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function twg(): BelongsTo
    {
        return $this->belongsTo(Twg::class, 'twg_id', 'user_id');
    }

    public function validation_purposes(): BelongsToMany
    {
        return $this->belongsToMany(ValidationPurpose::class, 'site_validation_purposes', 'site_validation_id', 'validation_purpose_id')->withTimestamps();
    }

    public function site_images(): MorphMany
    {
        return $this->morphMany(SiteImage::class, 'site_imageable');
    }
}
