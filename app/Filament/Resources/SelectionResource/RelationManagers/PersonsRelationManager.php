<?php

namespace AnimeSite\Filament\Resources\SelectionResource\RelationManagers;

use AnimeSite\Models\Selection;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\PersonType;
use AnimeSite\Models\Person;

class PersonsRelationManager extends RelationManager
{
    protected static string $relationship = 'persons';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип')
                    ->options(collect(PersonType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->name()]))
                    ->multiple(),

                Tables\Filters\SelectFilter::make('gender')
                    ->label('Стать')
                    ->options(collect(Gender::cases())->mapWithKeys(fn ($gender) => [$gender->value => $gender->name()]))
                    ->multiple(),
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
                        Select::make('selection_id')
                            ->label('Добірка')
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id)
                            ->disabled()
                            ->dehydrated()
                            ->options(Selection::query()->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->using(function (array $data, RelationManager $livewire): Model {
                        DB::table('selectionables')->insert([
                            'selection_id' => $data['selection_id'],
                            'selectionable_id' => $data['person_id'],
                            'selectionable_type' => Person::class,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        return Person::find($data['person_id']);
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }
}
