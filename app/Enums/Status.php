<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum Status: string implements HasLabel, HasColor, HasIcon
{
    case Validated = 'validated';
    case PendingReview = 'pending_review';
    case Rejected = 'rejected';
    case Expired = 'expired';

    public function getLabel(): string
    {
        return match ($this) {
            self::Validated => 'Validated',
            self::PendingReview => 'Pending Review',
            self::Rejected => 'Rejected',
            self::Expired => 'Expired',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Validated => 'success',
            self::PendingReview => 'warning',
            self::Rejected => 'danger',
            self::Expired => 'danger',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::PendingReview => Heroicon::Clock,
            self::Rejected => Heroicon::XCircle,
            self::Validated => Heroicon::ShieldCheck,
            self::Expired => Heroicon::ExclamationCircle,
        };
    }

    public function getFilamentIcon(): string
    {
        return match ($this) {
            self::PendingReview => 'heroicon-s-clock',
            self::Rejected => 'heroicon-s-x-circle',
            self::Validated => 'heroicon-s-shield-check',
            self::Expired => 'heroicon-s-exclamation-circle',
        };
    }
}
