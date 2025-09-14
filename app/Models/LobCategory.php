<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LobCategory extends Model
{
    use HasUlids;

    protected $fillable = [
        'title',
        'description',
    ];

    public function lobSubcategories(): HasMany
    {
        return $this->hasMany(LOBSubcategory::class);
    }

    public function supplierLobs(): HasMany
    {
        return $this->hasMany(SupplierLob::class);
    }
}
