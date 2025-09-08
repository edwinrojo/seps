<?php

namespace App\Filament\Resources\Suppliers\Tables;

use App\Filament\Actions\SecureDeleteAction;
use App\Filament\Resources\Suppliers\Schemas\AssignForm;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

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
                        TextColumn::make('user.email')->record($record)->prefix('System user: ')
                            ->formatStateUsing(fn ($record) => $record->user->name . ' (' . $record->user->email . ')')
                            ->icon(Heroicon::User)->table($table)->inline(),
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
                ActionGroup::make([
                    EditAction::make()
                        ->hiddenLabel()
                        ->color('gray')
                        ->tooltip('Edit supplier')
                        ->icon(Heroicon::PencilSquare),
                    Action::make('assign')
                        ->hiddenLabel()
                        ->color('gray')
                        ->tooltip('Assign to existing user account')
                        ->extraAttributes(['class' => 'success-icon-color'])
                        ->icon(Heroicon::UserPlus)
                        ->modal()
                        ->modalWidth(Width::Large)
                        ->modalSubmitAction(fn (Action $action) => $action->label('Save Changes')->icon(Heroicon::OutlinedCheck))
                        ->modalCancelAction(fn (Action $action) => $action->label('Cancel')->icon(Heroicon::XMark))
                        ->slideOver()
                        ->modalHeading(fn ($record) => $record->business_name)
                        ->modalDescription('Assign this supplier to an existing user account. This will link the supplier record to the selected user for business profile management and tracking.')
                        ->fillForm(fn ($record) => [
                            'user_id' => $record->user_id,
                        ])
                        ->schema(AssignForm::configure())
                        ->action(function (array $data, $record) {
                            $data['user_id'] ? $record->update(['user_id' => $data['user_id']]) : null;

                            Notification::make()
                                ->title('Supplier Assigned')
                                ->body('The supplier has been successfully assigned to the user account.')
                                ->success()
                                ->send();
                        }),
                    SecureDeleteAction::make()
                        ->hiddenLabel()
                        ->color('gray')
                        ->tooltip('Delete supplier')
                        ->extraAttributes(['class' => 'danger-icon-color'])
                        ->icon(Heroicon::Trash),
                ])->buttonGroup()
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
