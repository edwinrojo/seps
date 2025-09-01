<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Html;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Official Supplier Registry')
            ->description('This table provides a comprehensive list of accredited suppliers, including their company details, contact information, and nature of goods or services offered.')
            ->deferLoading()
            ->striped()
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label('#'),
                TextColumn::make('business_name')
                    ->label('Business Name')
                    ->getStateUsing(fn ($record, $table): array => [
                        TextColumn::make('business_name')->record($record)->weight(FontWeight::Bold)->size(TextSize::Large)->color('primary')->table($table)->inline(),
                        TextColumn::make('user.email')->record($record)->prefix('System user: ')->icon(Heroicon::User)->table($table)->inline(),
                    ])
                    ->listWithLineBreaks()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->getStateUsing(fn ($record, $table): array => [
                        TextColumn::make('email')->record($record)->icon(Heroicon::Envelope)->table($table)->inline(),
                        TextColumn::make('website')->record($record)->icon(Heroicon::GlobeAlt)->table($table)->inline(),
                    ])
                    ->listWithLineBreaks()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('mobile_number')
                    ->label('Contact Information')
                    ->getStateUsing(fn ($record, $table): array => [
                        TextColumn::make('mobile_number')->record($record)->prefix('+63 ')->icon(Heroicon::DevicePhoneMobile)->table($table)->inline(),
                        TextColumn::make('landline_number')->record($record)->icon(Heroicon::Phone)->table($table)->inline(),
                    ])
                    ->listWithLineBreaks(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->extraAttributes(['style' => 'visibility: hidden;']),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
