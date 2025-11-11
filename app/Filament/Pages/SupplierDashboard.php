<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SupplierDashboard extends Page
{
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.supplier-dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?string $slug = 'dashboard';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::Home;
}
