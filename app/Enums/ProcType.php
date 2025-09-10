<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum ProcType: string implements HasLabel, HasDescription
{
    case GOODS = 'goods';
    case CONSULTING_SERVICES = 'consulting services';
    case INFRASTRUCTURE = 'infrastructure';

    public function getLabel(): string
    {
        return match ($this) {
            self::GOODS => 'Goods',
            self::CONSULTING_SERVICES => 'Consulting Services',
            self::INFRASTRUCTURE => 'Infrastructure',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::GOODS => 'Procurement of goods and supplies',
            self::CONSULTING_SERVICES => 'Consulting and professional services',
            self::INFRASTRUCTURE => 'Infrastructure development and maintenance',
        };
    }
}
