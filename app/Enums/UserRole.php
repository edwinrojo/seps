<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel, HasDescription
{
    case Administrator = 'administrator';
    case EndUser = 'end-user';
    case Supplier = 'supplier';
    case Twg = 'twg';

    public function getLabel(): string
    {
        return match ($this) {
            self::Administrator => 'System Administrator',
            self::EndUser => 'End-user',
            self::Supplier => 'Supplier',
            self::Twg => 'TWG',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Administrator => 'Has full access to the system',
            self::EndUser => 'Has limited access to the system',
            self::Supplier => 'Can manage supplier-related tasks',
            self::Twg => 'Can manage TWG-related tasks',
        };
    }
}
