<?php

namespace App\Models;

use App\Enums\ProcType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Supplier extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'website',
        'email',
        'mobile_number',
        'landline_number',
        'owner_name',
        'supplier_type',
    ];

    protected function casts(): array {
        return [
            'supplier_type' => ProcType::class,
        ];
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplierLobs(): HasMany
    {
        return $this->hasMany(SupplierLob::class);
    }

    public function statuses(): MorphMany
    {
        return $this->morphMany(Status::class, 'statusable');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
