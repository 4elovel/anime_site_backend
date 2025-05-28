<?php

namespace AnimeSite\Filament\Resources\UserResource\RelationManagers;

use AnimeSite\Models\User;
use AnimeSite\Models\UserSubscription;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Models\Tariff;
use Illuminate\Database\Eloquent\Model;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

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

                TextColumn::make('start_date')
                    ->label('Дата початку')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Дата закінчення')
                    ->dateTime()
                    ->sortable(),

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
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Активні')
                    ->query(fn (Builder $query) => $query->where('is_active', true)),

                Tables\Filters\Filter::make('auto_renew')
                    ->label('З автопродовженням')
                    ->query(fn (Builder $query) => $query->where('auto_renew', true)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('Додати підписку')
                ->form([
                    Select::make('tariff_id')
                        ->label('Тариф')
                        ->options(Tariff::all()->pluck('name', 'id'))
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
                ])
                ->using(function (array $data, RelationManager $livewire): Model {
                    return UserSubscription::create([
                        'tariff_id' => $data['tariff_id'],
                        'user_id' => $data['user_id'],
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date'],
                        'is_active' => $data['is_active'] ?? false,
                        'auto_renew' => $data['auto_renew'] ?? false,
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
