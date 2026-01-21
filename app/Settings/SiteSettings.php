<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{
    public ?string $lob_reference_document;
    public ?int $document_expiry_notification_days;

    public static function group(): string
    {
        return 'general';
    }
}
