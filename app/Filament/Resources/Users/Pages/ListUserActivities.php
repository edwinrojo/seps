<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListUserActivities extends ListActivities
{
    protected static string $resource = UserResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        $user = Auth::user();

        abort_unless($user && Gate::forUser($user)->allows('viewActivities', $this->getRecord()), 403);
    }
}
