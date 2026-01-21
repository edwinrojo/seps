<?php

namespace App\Filament\Pages;

use App\Enums\NavigationGroup;
use App\Models\Document;
use App\Settings\SiteSettings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SystemSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string $settings = SiteSettings::class;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Administration;

    protected static ?int $navigationSort = 4;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('System Configuration')
                    ->description('Configure the system-wide settings for the application. These settings will affect various aspects of the system behavior.')
                    ->aside()
                    ->columnSpanFull()
                    ->schema([
                        Select::make('lob_reference_document')
                            ->label('Line of Business Reference Document')
                            ->options(function () {
                                $documents = Document::all();
                                return $documents->pluck('title', 'id')->mapWithKeys(function ($title, $id) use ($documents) {
                                    $description = $documents->where('id', $id)->first()->description;
                                    return [$id => '<b class="text-primary-600">'.$title.'</b>' . ($description ? "<span style='display: block;' class='text-sm text-gray-500'>$description</span>" : '')];
                                });
                            })
                            ->helperText('Select the document that will serve as the reference for Line of Business (LOB) validations across the system.')
                            ->allowHtml()
                            ->searchable()
                            ->required(),
                        Select::make('document_expiry_notification_days')
                            ->native(false)
                            ->label('Document Expiry Notification Days')
                            ->options([30 => '30 Days', 15 => '15 Days', 7 => '7 Days'])
                            ->helperText('Select the number of days prior to document expiry that suppliers will be notified.'),
                    ])
            ]);
    }

    public function getFormActions(): array
    {
        return [
            // ...parent::getFormActions(),
            $this->getSaveFormAction()
                ->icon(Heroicon::OutlinedCheckCircle)
                ->label('Save Settings')
        ];
    }

    public static function canAccess(): bool
    {
        return request()->user()->role === \App\Enums\UserRole::Administrator;
    }

    public function canEdit(): bool
    {
        return request()->user()->role === \App\Enums\UserRole::Administrator;
    }
}
