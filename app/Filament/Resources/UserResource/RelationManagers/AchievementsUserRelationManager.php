<?php

namespace AnimeSite\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AnimeSite\Models\Achievement;
use AnimeSite\Models\User;
use Illuminate\Support\Facades\DB;

class AchievementsUserRelationManager extends RelationManager
{
    protected static string $relationship = 'achievementsPivot';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('achievement_id')
                    ->label('Досягення')
                    ->options(Achievement::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('user_id')
                    ->label('Користувач')
                    ->options(User::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('progress_count')
                    ->label('Прогрес')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(100),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('progress_count')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('achievement.name')
                    ->label('Досягнення')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('progress_count')
                    ->label('Прогрес')
                    ->sortable()
                    ->badge()
                    ->searchable()
                    ->color(fn($state) => match (true) {
                        $state < 10 => 'danger',
                        $state < 50 => 'warning',
                        $state >= 50 => 'success',
                        default => 'muted',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати досягнення')
                    ->form([
                        Select::make('achievement_id')
                            ->label('Досягення')
                            ->options(Achievement::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('user_id')
                            ->label('Користувач')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->dehydrated()
                            ->options(User ::query()->pluck('name', 'id'))
                            ->required(),
                        TextInput::make('progress_count')
                            ->label('Прогрес')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(100),
                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        DB::table('achievement_user')->insert([
                            'achievement_id' => $data['achievement_id'],
                            'user_id' => $data['user_id'],
                            'progress_count' => $data['progress_count'],
                        ]);

                        return Achievement::find($data['achievement_id']);
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('progress_count', 'desc');
    }
}
