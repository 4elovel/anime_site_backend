<?php

namespace AnimeSite\Filament\Resources\TagResource\RelationManagers;

use AnimeSite\Enums\Source;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use AnimeSite\Enums\Kind;
use AnimeSite\Enums\Status;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Tag;

class AnimesRelationManager extends RelationManager
{
    protected static string $relationship = 'animes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
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
                    ->sortable(),

                TextColumn::make('kind')
                    ->label('Жанр')
                    ->formatStateUsing(fn ($state) => $state->name())
                    ->badge()
                    ->color(fn (Kind $state): string => $state->getBadgeColor())
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state) => $state->name())
                    ->badge()
                    ->color(fn (Status $state): string => $state->getBadgeColor()),

                BooleanColumn::make('is_published')
                    ->label('Опубліковано')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('aliases')
                    ->label('Псевдоніми')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->label(__('Опис'))
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('studio.name')
                    ->label('Студія')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('duration')
                    ->label('Тривалість')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('episodes_count')
                    ->label('Кількість епізодів')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('imdb_score')
                    ->label('Оцінка IMDB')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('first_air_date')
                    ->label(__('Дата початку ефіру'))
                    ->dateTime('d F Y р.')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_air_date')
                    ->label(__('Дата завершення ефіру'))
                    ->dateTime('d F Y р.')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


                TextColumn::make('api_sources')
                    ->label('API Джерела')
                    ->formatStateUsing(fn ($state) => is_string($state)
                        ? implode(', ', json_decode($state, true) ?? [])
                        : (is_array($state) ? implode(', ', $state) : '')
                    )
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('attachments')
                    ->label('Медіа')
                    ->formatStateUsing(fn ($state) => is_string($state)
                        ? implode(', ', json_decode($state, true) ?? [])
                        : (is_array($state) ? implode(', ', $state) : '')
                    )
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('related')
                    ->label('Пов’язані фільми')
                    ->formatStateUsing(fn ($state) => is_string($state)
                        ? implode(', ', json_decode($state, true) ?? [])
                        : (is_array($state) ? implode(', ', $state) : '')
                    )
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('similars')
                    ->label('Схожі фільми')
                    ->formatStateUsing(fn ($state) => is_string($state)
                        ? implode(', ', json_decode($state, true) ?? [])
                        : (is_array($state) ? implode(', ', $state) : '')
                    )
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('period')
                    ->label('Період')
                    ->formatStateUsing(fn ($state) => $state?->name())
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('restricted_rating')
                    ->label('Віковий рейтинг')
                    ->formatStateUsing(fn ($state) => $state?->name())
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('source')
                    ->label('Джерело')
                    ->formatStateUsing(fn ($state) => $state?->name())
                    ->badge()
                    ->color(fn (Source $state): string => $state->getBadgeColor())
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('poster')
                    ->label('Постер')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('meta_title')
                    ->label(('Meta заголовок'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('meta_description')
                    ->label(__('Meta опис'))
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('meta_image')
                    ->label('Meta зображення')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        Select::make('tag_id')
                            ->label('Тег')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->dehydrated()
                            ->options(Tag::query()->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        DB::table('anime_tag')->insert([
                            'anime_id' => $data['anime_id'],
                            'tag_id' => $data['tag_id'],
                        ]);

                        return Anime::find($data['anime_id']);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->defaultSort('first_air_date', 'desc');
    }
}
