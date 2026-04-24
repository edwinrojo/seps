<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class AnalyticsSnapshot extends Model
{
    use HasUlids;

    protected $fillable = ['snapshot_date', 'metric_key', 'metric_value', 'dimensions'];

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'metric_value' => 'decimal:2',
            'dimensions' => 'json',
        ];
    }
}
