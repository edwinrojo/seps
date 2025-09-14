<?php

namespace App\Filament\Resources\LOBCategories\Pages;

use App\Filament\Actions\SecureDeleteAction;
use App\Filament\Resources\LOBCategories\LOBCategoryResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateLOBCategory extends CreateRecord
{
    protected static string $resource = LOBCategoryResource::class;

}
