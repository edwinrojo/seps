<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LobSubcategory extends Model
{
    use HasUlids;

    protected $fillable = [
        'lob_category_id',
        'title',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(LobCategory::class, 'lob_category_id');
    }

    public function supplierLobs(): HasMany
    {
        return $this->hasMany(SupplierLob::class, 'lob_subcategory_id');
    }
}
