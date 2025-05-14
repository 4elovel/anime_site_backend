<?php

namespace AnimeSite\Filament\Resources\AnimeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\PersonType;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Person;

class PeoplePivotRelationManager extends RelationManager
{
    protected static string $relationship = 'people';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label('Ім\'я')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('original_name')
                    ->label('Справжнє ім\'я')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => $state->name())
                    ->badge()
                    ->color(fn (PersonType $state): string => $state->getBadgeColor()),
                TextColumn::make('gender')
                    ->label('Стать')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->name())
                    ->color(fn (Gender $state): string => $state->getBadgeColor()),

                TextColumn::make('slug')
                    ->label(('Slug'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')
                    ->label(__('Опис'))
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('image')
                    ->label('Зображення')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birthday')
                    ->label(__('Дата народження'))
                    ->dateTime('d F Y р.')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birthplace')
                    ->label('Місце народження')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('meta_title')
                    ->label(('Meta загаловок'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('meta_description')
                    ->label(__('Meta опис'))
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('meta_image')
                    ->label('Meta зображення')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати персонажа')
                    ->form([
                        Select::make('person_id')
                            ->label('Персонаж')
                            ->options(Person::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('anime_id')
                            ->label('Аніме')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->options(Anime::query()->pluck('name', 'id'))
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        TextInput::make('character_name')
                            ->label('Ім\'я персонажа')
                            ->required(),
                        Select::make('voice_person_id')
                            ->label('Актор озвучення')
                            ->options(Person::query()->where('type', 'Voice Actor')->pluck('name', 'id'))
                            ->searchable(),
                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        DB::table('anime_person')->insert([
                            'anime_id' => $data['anime_id'],
                            'person_id' => $data['person_id'],
                            'character_name' => $data['character_name'] ?? '',
                            'voice_person_id' => $data['voice_person_id'] ?? null,
                        ]);

                        return Person::find($data['person_id']);
                    }),
            ])
            ->filters([

            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DetachAction::make(),

            ])
            ->bulkActions([
                DetachBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
