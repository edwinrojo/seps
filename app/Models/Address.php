<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Address extends Model
{
    use HasUlids;

    protected $fillable = [
        'supplier_id',
        'line_1',
        'line_2',
        'municipality_id',
        'barangay_id',
        'province_id',
        'country',
        'zip_code'
    ];

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->line_1,
            $this->line_2,
            optional($this->barangay)->name,
            optional($this->municipality)->name,
            optional($this->province)->name,
            $this->country,
            $this->zip_code,
        ]);

        return implode(', ', $parts);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function statuses(): MorphMany
    {
        return $this->morphMany(Status::class, 'statusable');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function site_image(): MorphOne
    {
        return $this->morphOne(SiteImage::class, 'site_imageable');
    }

    public function getIsValidatedAttribute(): bool
    {
        $latestStatus = $this->statuses()->latest()->first();
        return $latestStatus && $latestStatus->status === \App\Enums\Status::Validated;
    }
}
