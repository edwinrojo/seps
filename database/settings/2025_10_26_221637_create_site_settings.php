<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.lob_reference_document', null);
        $this->migrator->add('general.document_expiry_notification_days', null);
    }
};
