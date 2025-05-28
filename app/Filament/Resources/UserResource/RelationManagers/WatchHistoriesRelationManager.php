<?php

namespace AnimeSite\Filament\Resources\UserResource\RelationManagers;

use AnimeSite\Models\Episode;
use AnimeSite\Models\User;
use AnimeSite\Models\WatchHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WatchHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'watchHistories';

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

                Tables\Columns\TextColumn::make('episode.name')
                    ->label('Епізод')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('progress_time')
                    ->label('Прогрес часу')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn ($state) => gmdate('H:i:s', $state))
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
                ->label('Додати до переглядів')
                ->form([
                    Forms\Components\Select::make('user_id')
                        ->label('Користувач')
                        ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                        ->disabled()
                        ->dehydrated()
                        ->options(User::query()->pluck('name', 'id'))
                        ->required(),
                    Forms\Components\Select::make('episode_id')
                        ->label('Епізод')
                        ->options(Episode::query()->with('anime')->get()->mapWithKeys(function ($episode) {
                            return [$episode->id => "{$episode->anime->name} - Епізод {$episode->number}"];
                        }))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('progress_time')
                        ->label('Прогрес часу')
                        ->numeric()
                        ->required(),
                ])
                ->using(function (array $data, RelationManager $livewire): Model {
                    return WatchHistory::create([
                        'user_id' => $livewire->ownerRecord->id,
                        'episode_id' => $data['episode_id'],
                        'progress_time' => $data['progress_time'],
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
