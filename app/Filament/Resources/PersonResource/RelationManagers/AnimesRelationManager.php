<?php

namespace AnimeSite\Filament\Resources\PersonResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use AnimeSite\Enums\Kind;
use AnimeSite\Enums\Status;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Person;

class AnimesRelationManager extends RelationManager
{
    protected static string $relationship = 'animes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('image')
                    ->label('Зображення')
                    ->circular(),

                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pivot.character_name')
                    ->label('Персонаж')
                    ->visible(fn ($livewire) => $livewire->ownerRecord->type === 'character')
                    ->searchable(),

                TextColumn::make('kind')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (Kind $state) => $state->name()),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (Status $state) => $state->name())
                    ->color(fn (Status $state) => $state->getBadgeColor()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kind')
                    ->label('Тип')
                    ->options(collect(Kind::cases())->mapWithKeys(fn ($kind) => [$kind->value => $kind->name()]))
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options(collect(Status::cases())->mapWithKeys(fn ($status) => [$status->value => $status->name()]))
                    ->multiple(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати аніме')
                    ->form([
                        Select::make('anime_id')
                            ->label('Аніме')
                            ->options(Anime::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('person_id')
                            ->label('Персонаж')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->dehydrated()
                            ->options(Person::query()->pluck('name', 'id'))
                            ->required(),
                        TextInput::make('character_name')
                            ->label('Ім\'я персонажа')
                            ->visible(fn (RelationManager $livewire) => $livewire->ownerRecord->type === 'character')
                            ->required(fn (RelationManager $livewire) => $livewire->ownerRecord->type === 'character'),
                        Select::make('voice_person_id')
                            ->label('Актор озвучення')
                            ->options(Person::query()->where('type', 'Voice Actor')->pluck('name', 'id'))
                            ->searchable()
                            ->visible(fn (RelationManager $livewire) => $livewire->ownerRecord->type === 'character'),
                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        DB::table('anime_person')->insert([
                            'anime_id' => $data['anime_id'],
                            'person_id' => $data['person_id'],
                            'character_name' => $data['character_name'] ?? '',
                            'voice_person_id' => $data['voice_person_id'] ?? null,
                        ]);

                        return Anime::find($data['anime_id']);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->form(fn (Tables\Actions\EditAction $action): array => [
                        TextInput::make('character_name')
                            ->label('Ім\'я персонажа')
                            ->visible(fn ($livewire) => $livewire->ownerRecord->type === 'character')
                            ->required(fn ($livewire) => $livewire->ownerRecord->type === 'character'),
                        Select::make('voice_person_id')
                            ->label('Актор озвучення')
                            ->relationship('people', 'name', fn (Builder $query) =>
                                $query->where('type', 'Voice Actor'))
                            ->searchable()
                            ->preload()
                            ->visible(fn ($livewire) => $livewire->ownerRecord->type === 'character'),
                    ]),

                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
