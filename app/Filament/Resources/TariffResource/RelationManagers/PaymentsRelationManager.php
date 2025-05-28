<?php

namespace AnimeSite\Filament\Resources\TariffResource\RelationManagers;

use AnimeSite\Enums\Currency;
use AnimeSite\Models\Payment;
use AnimeSite\Models\Tariff;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Enums\PaymentStatus;
use AnimeSite\Models\User;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Користувач')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                TextInput::make('amount')
                    ->label('Сума')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01),

                Select::make('currency')
                    ->label('Валюта')
                    ->options(Currency::labels())
                    ->default(Currency::UAH->value)
                    ->required(),

                TextInput::make('payment_method')
                    ->label('Спосіб оплати')
                    ->required()
                    ->default('LiqPay')
                    ->maxLength(50),

                TextInput::make('transaction_id')
                    ->label('ID транзакції')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(128),

                Select::make('status')
                    ->label('Статус')
                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->name()]))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->name()]))
                    ->multiple(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->form([
                        Section::make()
                            ->schema([
                                Select::make('tariff_id')
                                    ->label('Тариф')
                                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                                    ->disabled()
                                    ->options(Tariff::all()->pluck('name', 'id'))
                                    ->dehydrated()
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

                                Select::make('currency')
                                    ->label('Валюта')
                                    ->options(Currency::labels())
                                    ->default(Currency::UAH->value)
                                    ->required(),
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
                        Select::make('status')
                            ->label('Статус')
                            ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->name()]))
                            ->required(),
                        Textarea::make('liqpay_data')
                            ->label('Дані LiqPay (JSON)')
                            ->columnSpanFull()
                            ->rows(10),
                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        return Payment::create([
                            'user_id' => $data['user_id'],
                            'tariff_id' => $livewire->ownerRecord->id,
                            'amount' => $data['amount'],
                            'currency' => $data['currency'],
                            'payment_method' => $data['payment_method'],
                            'transaction_id' => $data['transaction_id'],
                            'status' => $data['status'],
                            'liqpay_data' => $data['liqpay_data'] ?? null,
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
