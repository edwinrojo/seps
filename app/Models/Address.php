<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasUlids;

    protected $fillable = [
        'supplier_id',
        'line_1',
        'line_2',
        'municipality_city',
        'barangay',
        'province',
        'country',
        'zip_code'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
