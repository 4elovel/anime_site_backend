<?php

namespace AnimeSite\Filament\Resources\TagResource\RelationManagers;

use AnimeSite\Models\Selection;
use AnimeSite\Models\Tag;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SelectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'selections';

    protected static ?string $label = 'Добірка';      // Однина
    protected static ?string $pluralLabel = 'Добірки'; // Множина
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
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ImageColumn::make('poster')
                    ->label('Постер')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Користувач')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label('Опубліковано')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
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
                    ->label('Додати добірку')
                    ->form([
                        Select::make('selection_id')
                            ->label('Добірка')
                            ->options(Selection::query()->pluck('name', 'id'))
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
                            'taggable_id' => $data['selection_id'],
                            'taggable_type' => Selection::class,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        return Selection::find($data['selection_id']);
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
