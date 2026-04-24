<?php

namespace App\Models;

use App\Enums\Status as EnumStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Attachment extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'supplier_id',
        'document_id',
        'file_path',
        'validity_date',
        'file_size',
    ];

    protected function casts(): array
    {
        return [
            'validity_date' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function statuses(): MorphMany
    {
        return $this->morphMany(Status::class, 'statusable');
    }

    public function getIsValidatedAttribute(): bool
    {
        $latest_status = $this->statuses()
            ->latest('status_date')
            ->latest('id')
            ->first();

        return $latest_status && $latest_status->status === EnumStatus::Validated;
    }
}
