<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SupplierLob extends Model
{
    protected $fillable = [
        'supplier_id',
        'lob_category_id',
        'lob_subcategory_id',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lobCategory(): BelongsTo
    {
        return $this->belongsTo(LobCategory::class);
    }

    public function lobSubcategory(): BelongsTo
    {
        return $this->belongsTo(LobSubcategory::class);
    }

}
