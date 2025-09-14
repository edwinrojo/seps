<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Status extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'statusable_id',
        'statusable_type',
        'status',
        'remarks',
        'status_date'
    ];

    public function statusable(): MorphTo
    {
        return $this->morphTo();
    }
}
