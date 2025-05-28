<?php

namespace AnimeSite\Filament\Resources\UserResource\RelationManagers;

use AnimeSite\Enums\Currency;
use AnimeSite\Models\Payment;
use AnimeSite\Models\User;
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
use AnimeSite\Models\Tariff;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

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
                    ->color(fn (PaymentStatus $state): string => $state->getBadgecolor())
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
                TextColumn::make('liqpay_data')
                    ->label('Дані LiqPay')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->name()]))
                    ->multiple(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати оплату')
                    ->form([
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
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->dehydrated()
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
                            'user_id' => $livewire->ownerRecord->id,
                            'tariff_id' => $data['tariff_id'],
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
