<?php

namespace App\Models;

use App\Enums\Status as EnumsStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    /**
     * Attribute casts for the model.
     *
     * Cast the `status` to the enum class and `status_date` to datetime so
     * calling ->format() on the attribute returns a Carbon instance.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => EnumsStatus::class,
        'status_date' => 'datetime',
    ];

    public function statusable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
