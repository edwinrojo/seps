<?php

namespace App\Models;

use App\Enums\ProcType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasUlids;

    protected $fillable = [
        'document_type_id',
        'title',
        'description',
        'procurement_type',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'procurement_type' => 'array',
        ];
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}
