<?php

namespace AnimeSite\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use AnimeSite\Filament\Resources\UserSubscriptionResource\Pages;
use AnimeSite\Models\Tariff;
use AnimeSite\Models\User;
use AnimeSite\Models\UserSubscription;

class UserSubscriptionResource extends Resource
{
    protected static ?string $model = UserSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Підписки';
    protected static ?string $pluralModelLabel = 'Підписки користувачів';
    protected static ?string $modelLabel = 'Підписка користувача';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                        Section::make()
                            ->schema([
                                Select::make('user_id')
                                    ->label('Користувач')
                                    ->options(User::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),

                                Select::make('tariff_id')
                                    ->label('Тариф')
                                    ->options(Tariff::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ])
                            ->columns(2),

                        Section::make()
                            ->schema([
                        DateTimePicker::make('start_date')
                            ->label('Дата початку')
                            ->required(),

                        DateTimePicker::make('end_date')
                            ->label('Дата закінчення')
                            ->required()
                            ->after('start_date'),
                            ])
                            ->columns(2),
                        Section::make()
                            ->schema([
                        Toggle::make('is_active')
                            ->label('Активна')
                            ->default(true),

                        Toggle::make('auto_renew')
                            ->label('Автоматичне продовження')
                            ->default(false),
                            ])
                            ->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tariff.name')
                    ->label('Тариф')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Дата початку')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('end_date')
                    ->label('Дата закінчення')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                BooleanColumn::make('is_active')
                    ->label('Активна')
                    ->sortable(),

                BooleanColumn::make('auto_renew')
                    ->label('Автоматичне продовження')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Активні')
                    ->query(fn ($query) => $query->where('is_active', true)),

                Tables\Filters\Filter::make('auto_renew')
                    ->label('З автопродовженням')
                    ->query(fn ($query) => $query->where('auto_renew', true)),

                SelectFilter::make('user')
                    ->label('Користувач')
                    ->relationship('user', 'name')
                    ->searchable(),

                SelectFilter::make('tariff')
                    ->label('Тариф')
                    ->relationship('tariff', 'name')
                    ->searchable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListUserSubscriptions::route('/'),
            'create' => Pages\CreateUserSubscription::route('/create'),
            'edit' => Pages\EditUserSubscription::route('/{record}/edit'),
        ];
    }
}
