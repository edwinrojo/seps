<?php

namespace App\Filament\Resources\Suppliers\Actions;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class SiteImageAction
{
    public static function save($address, $state)
    {
        if (!$state) {
            return;
        }
        // Action logic for adding/updating site image
        $address->site_image?->delete();
        if ($address->site_image?->file_path) {
            Storage::disk('public')->delete($address->site_image?->file_path);
        }

        $sizeInBytes = Storage::disk('public')->size($state);

        $address->site_image()->create([
            'file_path' => $state,
            'file_size' => $sizeInBytes,
        ]);
    }
}
