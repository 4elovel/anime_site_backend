<?php

namespace Liamtseva\Cinema\Filament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Liamtseva\Cinema\Enums\NotificationType;
use Liamtseva\Cinema\Filament\Resources\NotificationHistoryResource\Pages;
use Liamtseva\Cinema\Models\NotificationHistory;
use Liamtseva\Cinema\Models\User;

class NotificationHistoryResource extends Resource
{
    protected static ?string $model = NotificationHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Користувачі';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Користувач')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('type')
                    ->label('Тип повідомлення')
                    ->options(NotificationType::all())
                    ->required(),

                TextInput::make('notifiable_type')
                    ->label('Тип об\'єкта')
                    ->required(),

                TextInput::make('notifiable_id')
                    ->label('ID об\'єкта')
                    ->required(),

                DateTimePicker::make('read_at')
                    ->label('Прочитано'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Тип повідомлення'),

                TextColumn::make('notifiable_type')
                    ->label('Тип об\'єкта'),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('read_at')
                    ->label('Прочитано')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Тип повідомлення')
                    ->options(NotificationType::all()),

                SelectFilter::make('user_id')
                    ->label('Користувач')
                    ->options(User::all()->pluck('name', 'id')),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationHistories::route('/'),
            'create' => Pages\CreateNotificationHistory::route('/create'),
            'edit' => Pages\EditNotificationHistory::route('/{record}/edit'),
        ];
    }
}
