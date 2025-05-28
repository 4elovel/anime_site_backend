<?php

namespace AnimeSite\Filament\Resources\TagResource\RelationManagers;

use AnimeSite\Enums\Gender;
use AnimeSite\Enums\PersonType;
use AnimeSite\Models\Person;
use AnimeSite\Models\Tag;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PersonsRelationManager extends RelationManager
{
    protected static string $relationship = 'people';

    protected static ?string $label = 'Людина';      // Однина
    protected static ?string $pluralLabel = 'Люди'; // Множина
    protected static ?string $recordTitleAttribute = 'name';

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
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Додати персонажа')
                    ->form([
                        Select::make('person_id')
                            ->label('Людина')
                            ->options(Person::query()->pluck('name', 'id'))
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
                        DB::table('taggables')->insert([
                            'tag_id' => $data['tag_id'],
                            'taggable_id' => $data['person_id'],
                            'taggable_type' => Person::class,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        return Person::find($data['person_id']);
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DetachAction::make(),
            ])
            ->bulkActions([
                // Не додаємо булк дії
            ]);
    }
}
