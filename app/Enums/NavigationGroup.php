<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup implements HasLabel
{
    case Suppliers;
    case Administration;
    case Accounts;

    public function getLabel(): string
    {
        return match($this) {
            self::Suppliers => 'Suppliers',
            self::Administration => 'Administration',
            self::Accounts => 'Accounts',
        };
    }
}
