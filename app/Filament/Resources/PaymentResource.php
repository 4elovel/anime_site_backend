<?php

namespace AnimeSite\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use AnimeSite\Enums\PaymentStatus;
use AnimeSite\Filament\Resources\PaymentResource\Pages;
use AnimeSite\Models\Payment;
use AnimeSite\Models\Tariff;
use AnimeSite\Models\User;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Підписки';
    protected static ?string $pluralModelLabel = 'Платежі';
    protected static ?string $modelLabel = 'Платіж';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                        Section::make()
                            ->schema([
                                Select::make('tariff_id')
                                    ->label('Тариф')
                                    ->options(Tariff::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('user_id')
                                    ->label('Користувач')
                                    ->searchable()
                                    ->preload()
                                    ->options(User::query()->pluck('name', 'id'))
                                    ->required(),
                            ])
                            ->columns(2),
                        Section::make()
                            ->schema([
                                TextInput::make('amount')
                                    ->label('Сума')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01),

                                TextInput::make('currency')
                                    ->label('Валюта')
                                    ->required()
                                    ->default('UAH')
                                    ->maxLength(3),
                            ])
                            ->columns(2),
                        Section::make()
                            ->schema([
                                TextInput::make('payment_method')
                                    ->label('Спосіб оплати')
                                    ->required()
                                    ->default('LiqPay')
                                    ->maxLength(50),

                                TextInput::make('transaction_id')
                                    ->label('ID транзакції')
                                    ->required(),
                            ])
                            ->columns(2),
                Section::make()
                    ->schema([
                        Select::make('status')
                            ->label('Статус')
                            ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->name()]))
                            ->required(),
                        Textarea::make('liqpay_data')
                            ->label('Дані LiqPay (JSON)')
                            ->columnSpanFull()
                            ->rows(10),
                    ])
                    ->columns(1),

            ])
            ->columns(2);
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

                TextColumn::make('amount')
                    ->label('Сума')
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Валюта')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Спосіб оплати')
                    ->sortable(),

                TextColumn::make('transaction_id')
                    ->label('ID транзакції')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (PaymentStatus $state) => $state->name())
                    ->color(fn (PaymentStatus $state) => $state->getBadgeColor())
                    ->badge()
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
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->name()]))
                    ->multiple(),

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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
