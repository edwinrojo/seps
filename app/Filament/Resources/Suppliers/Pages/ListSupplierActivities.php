<?php

namespace App\Filament\Resources\Suppliers\Pages;

use App\Filament\Resources\Suppliers\SupplierResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListSupplierActivities extends ListActivities
{
    protected static string $resource = SupplierResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        $user = Auth::user();

        abort_unless($user && Gate::forUser($user)->allows('viewActivities', $this->getRecord()), 403);
    }
}
