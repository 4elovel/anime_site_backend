<?php

namespace AnimeSite\Filament\Resources\AnimeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Tag;

class TagsRelationManager extends RelationManager
{
    protected static string $relationship = 'tags';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('Опис'))
                    ->limit(80),
                IconColumn::make('is_genre')
                    ->label('Жанр')
                    ->boolean(),
                ImageColumn::make('image')
                    ->label('Зображення'),

                TextColumn::make('aliases')
                    ->label('Псевдоніми')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('slug')
                    ->label(('Slug'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('parent.name')
                    ->label('Батьківський тег')
                    ->sortable()
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
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати тег')
                    ->form([
                        Select::make('tag_id')
                            ->label('Тег')
                            ->options(Tag::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('anime_id')
                            ->label('Аніме')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->options(Anime::query()->pluck('name', 'id'))
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        DB::table('anime_tag')->insert([
                            'anime_id' => $data['anime_id'],
                            'tag_id' => $data['tag_id'],
                        ]);

                        return Tag::find($data['tag_id']);
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
