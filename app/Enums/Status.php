<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum Status: string implements HasLabel, HasDescription
{
    case Approved = 'approved';
    case PendingReview = 'pending_review';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::Approved => 'Approved',
            self::PendingReview => 'Pending Review',
            self::Rejected => 'Rejected',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Approved => 'The document has been approved.',
            self::PendingReview => 'The document is pending review.',
            self::Rejected => 'The document has been rejected.',
        };
    }
}
