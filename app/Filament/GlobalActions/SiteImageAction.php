<?php

namespace App\Filament\GlobalActions;

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

    public static function saveMultiple($siteValidation, $site_images)
    {
        if (!$site_images || !is_array($site_images)) {
            return;
        }

        // Delete existing images
        foreach ($siteValidation->site_images as $existingImage) {
            Storage::disk('public')->delete($existingImage->file_path);
            $existingImage->delete();
        }

        foreach ($site_images as $site_image) {
            $sizeInBytes = Storage::disk('public')->size($site_image);

            $siteValidation->site_images()->create([
                'file_path' => $site_image,
                'file_size' => $sizeInBytes,
            ]);
        }
    }
}
