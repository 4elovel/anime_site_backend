<?php

namespace AnimeSite\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AnimeSite\Models\Episode;
use AnimeSite\Models\SearchHistory;
use AnimeSite\Models\User;
use AnimeSite\Models\WatchHistory;

class SearchHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'searchHistories';

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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('query')
                    ->label('Рядок пошука')
                    ->sortable()
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати до пошуку')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Користувач')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->dehydrated()
                            ->options(User::query()->pluck('name', 'id'))
                            ->required(),
                        Forms\Components\TextInput::make('query')
                            ->label('Рядок пошука')
                            ->required(),
                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        return SearchHistory::create([
                            'user_id' => $livewire->ownerRecord->id,
                            'query' => $data['query'],
                            'created_at' => now(),
                            'updated_at' => now(),
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
