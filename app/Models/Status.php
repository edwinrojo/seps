<?php

namespace App\Models;

use App\Enums\Status as EnumsStatus;
use App\Helpers\EligibilityChangeNotifier;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Status extends Model
{
    use HasUlids;

    /**
     * Cache pre-create eligibility by model object id for comparing after persistence.
     *
     * @var array<int, bool>
     */
    protected static array $eligibilityBeforeCreate = [];

    protected $fillable = [
        'user_id',
        'statusable_id',
        'statusable_type',
        'status',
        'remarks',
        'status_date',
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

    protected static function booted(): void
    {
        static::creating(function (self $status): void {
            $supplier = self::resolveSupplierFromStatus($status);

            if (! $supplier) {
                return;
            }

            self::$eligibilityBeforeCreate[spl_object_id($status)] = EligibilityChangeNotifier::currentEligibility($supplier);
        });

        static::created(function (self $status): void {
            $objectId = spl_object_id($status);

            if (! array_key_exists($objectId, self::$eligibilityBeforeCreate)) {
                return;
            }

            $previousEligibility = self::$eligibilityBeforeCreate[$objectId];
            unset(self::$eligibilityBeforeCreate[$objectId]);

            $supplier = self::resolveSupplierFromStatus($status);

            if (! $supplier) {
                return;
            }

            EligibilityChangeNotifier::notifyIfChanged($supplier, $previousEligibility);
        });
    }

    private static function resolveSupplierFromStatus(self $status): ?Supplier
    {
        return match ($status->statusable_type) {
            Attachment::class => Attachment::query()->find($status->statusable_id)?->supplier,
            Address::class => Address::query()->find($status->statusable_id)?->supplier,
            Supplier::class => Supplier::query()->find($status->statusable_id),
            default => null,
        };
    }

    public function statusable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
